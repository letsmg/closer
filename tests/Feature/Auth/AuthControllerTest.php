<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\BlockedEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ];

        $response = $this->postJson('/api/auth/register', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_fails_with_blocked_email()
    {
        $blockedEmail = BlockedEmail::factory()->create([
            'email_hash' => hash('sha256', strtolower(trim('blocked@example.com')))
        ]);

        $userData = [
            'name' => 'John Doe',
            'email' => 'BLOCKED@example.com', // Test case insensitivity
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Este email não pode ser utilizado para cadastro.'
            ]);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'ativo' => true,
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'perfil'
                ]
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Credenciais inválidas.'
            ]);
    }

    /** @test */
    public function login_fails_with_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null, // Unverified
            'ativo' => true,
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Você precisa confirmar seu e-mail antes de fazer login.'
            ]);
    }

    /** @test */
    public function login_fails_with_inactive_account()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'ativo' => false, // Inactive
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Sua conta está desativada.'
            ]);
    }

    /** @test */
    public function login_updates_user_metadata()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'ativo' => true,
            'ultimo_ip' => null,
            'ultimo_login_em' => null,
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $this->postJson('/api/auth/login', $loginData);

        $user->refresh();
        $this->assertNotNull($user->ultimo_ip);
        $this->assertNotNull($user->ultimo_login_em);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso.'
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    /** @test */
    public function logout_fails_without_authentication()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_ban_user()
    {
        $admin = User::factory()->create(['nivel_acesso' => 3]); // Admin level
        $userToBan = User::factory()->create(['nivel_acesso' => 0]); // Free user

        $token = $admin->createToken('auth_token')->plainTextToken;

        $banData = ['motivo' => 'Violation of terms'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/auth/ban/{$userToBan->id}", $banData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Usuário banido com sucesso.'
            ]);

        $userToBan->refresh();
        $this->assertFalse($userToBan->ativo);

        $this->assertDatabaseHas('blocked_emails', [
            'user_id' => $userToBan->id,
            'banned_by' => $admin->id,
            'reason' => 'Violation of terms',
        ]);
    }

    /** @test */
    public function non_admin_cannot_ban_user()
    {
        $regularUser = User::factory()->create(['nivel_acesso' => 0]); // Free user
        $userToBan = User::factory()->create(['nivel_acesso' => 0]);

        $token = $regularUser->createToken('auth_token')->plainTextToken;

        $banData = ['motivo' => 'No reason'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/auth/ban/{$userToBan->id}", $banData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Apenas administradores podem realizar banimento.'
            ]);
    }

    /** @test */
    public function admin_cannot_ban_another_admin()
    {
        $admin = User::factory()->create(['nivel_acesso' => 3]);
        $anotherAdmin = User::factory()->create(['nivel_acesso' => 3]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $banData = ['motivo' => 'No reason'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/auth/ban/{$anotherAdmin->id}", $banData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Não é permitido banir outro administrador.'
            ]);
    }

    /** @test */
    public function admin_cannot_ban_themselves()
    {
        $admin = User::factory()->create(['nivel_acesso' => 3]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $banData = ['motivo' => 'No reason'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/auth/ban/{$admin->id}", $banData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Você não pode banir a si mesmo.'
            ]);
    }

    /** @test */
    public function cannot_ban_already_banned_user()
    {
        $admin = User::factory()->create(['nivel_acesso' => 3]);
        $userToBan = User::factory()->create(['nivel_acesso' => 0]);

        // Pre-ban the user
        BlockedEmail::factory()->create([
            'user_id' => $userToBan->id,
            'email_hash' => hash('sha256', strtolower(trim($userToBan->email)))
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $banData = ['motivo' => 'Another reason'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/auth/ban/{$userToBan->id}", $banData);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Usuário já está banido.'
            ]);
    }
}
