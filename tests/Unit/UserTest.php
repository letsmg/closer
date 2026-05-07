<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use App\Enums\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_be_created_with_required_fields()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'ativo' => true,
            'nivel_acesso' => UserLevel::FREE->value,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue($user->ativo);
        $this->assertEquals(UserLevel::FREE->value, $user->nivel_acesso);
    }

    /** @test */
    public function user_has_profile_relationship()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Profile::class, $user->perfil);
        $this->assertEquals($profile->id, $user->perfil->id);
    }

    /** @test */
    public function user_level_methods_work_correctly()
    {
        $freeUser = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        $plusUser = User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        $premiumUser = User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);
        $adminUser = User::factory()->create(['nivel_acesso' => UserLevel::ADMIN->value]);

        // Test free user
        $this->assertTrue($freeUser->isFree());
        $this->assertFalse($freeUser->isPlus());
        $this->assertFalse($freeUser->isPremium());
        $this->assertFalse($freeUser->isAdmin());

        // Test plus user
        $this->assertFalse($plusUser->isFree());
        $this->assertTrue($plusUser->isPlus());
        $this->assertFalse($plusUser->isPremium());
        $this->assertFalse($plusUser->isAdmin());

        // Test premium user
        $this->assertFalse($premiumUser->isFree());
        $this->assertFalse($premiumUser->isPlus());
        $this->assertTrue($premiumUser->isPremium());
        $this->assertFalse($premiumUser->isAdmin());

        // Test admin user
        $this->assertFalse($adminUser->isFree());
        $this->assertFalse($adminUser->isPlus());
        $this->assertFalse($adminUser->isPremium());
        $this->assertTrue($adminUser->isAdmin());
    }

    /** @test */
    public function user_access_methods_work_correctly()
    {
        $freeUser = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        $plusUser = User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        $premiumUser = User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);

        // Test free user limitations
        $this->assertFalse($freeUser->hasPlusAccess());
        $this->assertFalse($freeUser->hasPremiumAccess());
        $this->assertEquals(10, $freeUser->getDailyMatchesLimit());
        $this->assertEquals(20, $freeUser->getDailyMessagesLimit());

        // Test plus user access
        $this->assertTrue($plusUser->hasPlusAccess());
        $this->assertFalse($plusUser->hasPremiumAccess());
        $this->assertEquals(50, $plusUser->getDailyMatchesLimit());
        $this->assertEquals(100, $plusUser->getDailyMessagesLimit());

        // Test premium user access
        $this->assertTrue($premiumUser->hasPlusAccess());
        $this->assertTrue($premiumUser->hasPremiumAccess());
        $this->assertEquals(PHP_INT_MAX, $premiumUser->getDailyMatchesLimit()); // Unlimited
        $this->assertEquals(PHP_INT_MAX, $premiumUser->getDailyMessagesLimit()); // Unlimited
    }

    /** @test */
    public function user_scopes_work_correctly()
    {
        User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);
        User::factory()->create(['nivel_acesso' => UserLevel::ADMIN->value]);
        User::factory()->create(['nivel_acesso' => UserLevel::OPERATIONAL->value]);

        // Test byLevel scope
        $freeUsers = User::byLevel(UserLevel::FREE)->get();
        $this->assertCount(1, $freeUsers);

        // Test paid scope
        $paidUsers = User::paid()->get();
        $this->assertCount(2, $paidUsers); // Plus and Premium

        // Test admins scope
        $adminUsers = User::admins()->get();
        $this->assertCount(2, $adminUsers); // Admin and Operational

        // Test free scope
        $freeUsers = User::free()->get();
        $this->assertCount(1, $freeUsers);
    }

    /** @test */
    public function user_jwt_methods_work()
    {
        $user = User::factory()->create();

        $this->assertEquals($user->id, $user->getJWTIdentifier());
        
        $claims = $user->getJWTCustomClaims();
        $this->assertArrayHasKey('uuid', $claims);
        $this->assertArrayHasKey('level', $claims);
        $this->assertEquals($user->uuid, $claims['uuid']);
        $this->assertEquals($user->nivel_acesso, $claims['level']);
    }

    /** @test */
    public function user_casts_work_correctly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'premium_expira_em' => now()->addDays(30),
            'ativo' => true,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->premium_expira_em);
        $this->assertIsBool($user->ativo);
        $this->assertTrue($user->ativo);
    }

    /** @test */
    public function user_fillable_fields_are_correct()
    {
        $fillable = [
            'name',
            'email',
            'password',
            'uuid',
            'ativo',
            'nivel_acesso',
            'reputacao',
            'ultima_interacao_at',
            'ultima_conversa_at',
            'assinatura_id',
            'premium_expira_em',
        ];

        $this->assertEquals($fillable, (new User)->getFillable());
    }

    /** @test */
    public function user_hidden_fields_are_correct()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($hidden, (new User)->getHidden());
    }
}
