<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Cria usuário de teste
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'ativo' => true,
        ]);
    }

    /** @test */
    public function usuario_pode_fazer_login_com_credenciais_validas()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => [
                        'uuid',
                        'name',
                        'email',
                    ],
                ],
            ]);
    }

    /** @test */
    public function login_retorna_erro_com_credenciais_invalidas()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Credenciais inválidas.',
            ]);
    }

    /** @test */
    public function login_bloqueia_email_nao_verificado()
    {
        $this->user->update(['email_verified_at' => null]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Você precisa confirmar seu e-mail antes de fazer login.',
            ]);
    }

    /** @test */
    public function login_bloqueia_conta_desativada()
    {
        $this->user->update(['ativo' => false]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Sua conta está desativada.',
            ]);
    }

    /** @test */
    public function usuario_pode_obter_dados_com_token_valido()
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token_info' => [
                        'exp',
                        'iat',
                    ],
                ],
            ]);
    }

    /** @test */
    public function acesso_negado_sem_token()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token não fornecido.',
            ]);
    }

    /** @test */
    public function token_expirado_retorna_erro_401()
    {
        // Simula token expirado (TTL = 0 para teste)
        config(['jwt.ttl' => 0]);
        
        $token = auth('api')->login($this->user);
        
        sleep(1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function usuario_pode_fazer_logout()
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout realizado com sucesso.',
            ]);
    }

    /** @test */
    public function refresh_token_renovia_acesso_token()
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ],
            ]);
    }

    /** @test */
    public function uuid_e_usado_na_resposta_em_vez_de_id_incremental()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        
        $user = $response->json('data.user');
        
        // Verifica que retornou UUID, não ID numérico
        $this->assertArrayHasKey('uuid', $user);
        $this->assertMatchesRegularExpression('/^[0-9A-Z]{26}$/', $user['uuid']); // Formato ULID
        $this->assertArrayNotHasKey('id', $user);
    }
}
