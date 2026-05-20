<?php

namespace Tests\Feature\Business;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Testes para regras de negócio baseadas em níveis de usuário (UserLevel Enum).
 * 
 * Cobre:
 * - Permissões de cada nível (canViewLikes, hasPlusAccess, etc.)
 * - Limites diários de likes e mensagens
 * - Regras de bloqueio entre níveis
 */
class UserLevelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function free_user_cannot_view_likes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        $this->assertFalse($user->canViewLikes());
    }

    /** @test */
    public function plus_user_cannot_view_likes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        $this->assertFalse($user->canViewLikes());
    }

    /** @test */
    public function premium_user_can_view_likes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);
        $this->assertTrue($user->canViewLikes());
    }

    /** @test */
    public function free_user_has_daily_like_limit()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        $limit = $user->getDailyMatchesLimit();
        $this->assertEquals(70, $limit);
    }

    /** @test */
    public function plus_user_has_unlimited_likes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        $limit = $user->getDailyMatchesLimit();
        $this->assertEquals(PHP_INT_MAX, $limit);
    }

    /** @test */
    public function premium_user_has_unlimited_likes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);
        $limit = $user->getDailyMatchesLimit();
        $this->assertEquals(PHP_INT_MAX, $limit);
    }

    /** @test */
    public function free_user_cannot_send_messages_without_match()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        $this->assertFalse($user->hasPlusAccess());
    }

    /** @test */
    public function plus_user_has_10_daily_messages_limit()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        $limit = $user->getDailyMessagesLimit();
        $this->assertEquals(10, $limit);
    }

    /** @test */
    public function premium_user_has_unlimited_messages()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);
        $limit = $user->getDailyMessagesLimit();
        $this->assertEquals(PHP_INT_MAX, $limit);
    }

    /** @test */
    public function moderator_can_block_customers_below_cofounder()
    {
        $moderator = User::factory()->create(['nivel_acesso' => UserLevel::MODERATOR->value]);
        $freeUser = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        $plusUser = User::factory()->create(['nivel_acesso' => UserLevel::PLUS->value]);
        $premiumUser = User::factory()->create(['nivel_acesso' => UserLevel::PREMIUM->value]);
        $cofounder = User::factory()->create(['nivel_acesso' => UserLevel::COFOUNDER->value]);
        $elite = User::factory()->create(['nivel_acesso' => UserLevel::ELITE->value]);

        $policy = new \App\Policies\BlockPolicy();

        $this->assertTrue($policy->block($moderator, $freeUser));
        $this->assertTrue($policy->block($moderator, $plusUser));
        $this->assertTrue($policy->block($moderator, $premiumUser));
        $this->assertFalse($policy->block($moderator, $cofounder));
        $this->assertFalse($policy->block($moderator, $elite));
    }

    /** @test */
    public function staff_cannot_be_blocked_by_customers()
    {
        $admin = User::factory()->create(['nivel_acesso' => UserLevel::ADMIN->value]);
        $operational = User::factory()->create(['nivel_acesso' => UserLevel::OPERATIONAL->value]);
        $support = User::factory()->create(['nivel_acesso' => UserLevel::SUPPORT->value]);
        $freeUser = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);

        $policy = new \App\Policies\BlockPolicy();

        $this->assertFalse($policy->block($freeUser, $admin));
        $this->assertFalse($policy->block($freeUser, $operational));
        $this->assertFalse($policy->block($freeUser, $support));
    }

    /** @test */
    public function admin_can_view_sensitive_location()
    {
        $admin = User::factory()->create(['nivel_acesso' => UserLevel::ADMIN->value]);
        $policy = new \App\Policies\UserPolicy();
        $this->assertTrue($policy->viewSensitiveLocation($admin));
    }

    /** @test */
    public function operational_cannot_view_sensitive_location()
    {
        $operational = User::factory()->create(['nivel_acesso' => UserLevel::OPERATIONAL->value]);
        $policy = new \App\Policies\UserPolicy();
        $this->assertFalse($policy->viewSensitiveLocation($operational));
    }

    /** @test */
    public function support_cannot_view_financial_data()
    {
        $support = User::factory()->create(['nivel_acesso' => UserLevel::SUPPORT->value]);
        $policy = new \App\Policies\UserPolicy();
        $this->assertFalse($policy->viewFinancialData($support));
    }

    /** @test */
    public function admin_can_view_financial_data()
    {
        $admin = User::factory()->create(['nivel_acesso' => UserLevel::ADMIN->value]);
        $policy = new \App\Policies\UserPolicy();
        $this->assertTrue($policy->viewFinancialData($admin));
    }

    /** @test */
    public function data_masking_masks_coordinates_for_non_admin()
    {
        $operational = User::factory()->create(['nivel_acesso' => UserLevel::OPERATIONAL->value]);
        
        $locationData = [
            'latitude' => -23.5505,
            'longitude' => -46.6333,
        ];

        $masked = \App\Services\DataMaskingService::maskLocationData($locationData, $operational);
        
        $this->assertNotNull($masked);
        $this->assertEquals(-23.6, $masked['latitude']); // Arredondado para 1 casa
        $this->assertEquals(-46.6, $masked['longitude']); // Arredondado para 1 casa
        $this->assertFalse($masked['exact_location']);
        $this->assertTrue($masked['masked']);
    }

    /** @test */
    public function data_masking_returns_full_data_for_admin()
    {
        $admin = User::factory()->create(['nivel_acesso' => UserLevel::ADMIN->value]);
        
        $locationData = [
            'latitude' => -23.5505,
            'longitude' => -46.6333,
        ];

        $result = \App\Services\DataMaskingService::maskLocationData($locationData, $admin);
        
        $this->assertEquals(-23.5505, $result['latitude']);
        $this->assertEquals(-46.6333, $result['longitude']);
        $this->assertArrayNotHasKey('masked', $result);
    }
}
