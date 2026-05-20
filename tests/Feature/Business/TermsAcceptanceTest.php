<?php

namespace Tests\Feature\Business;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserTermsAcceptance;
use App\Enums\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Testes para o fluxo de aceitação de Termos de Uso e Política de Privacidade.
 * 
 * Cobre:
 * - Bloqueio de acesso sem aceitação
 * - Versionamento de termos
 * - Invalidação de aceitação quando versão muda
 */
class TermsAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_without_acceptance_is_blocked()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        
        $response = $this->actingAs($user)
            ->getJson('/api/discover');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'terms_not_accepted']);
    }

    /** @test */
    public function user_with_valid_acceptance_can_access()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        
        // Simula aceitação dos termos
        UserTermsAcceptance::create([
            'user_id' => $user->id,
            'terms_version' => 'v1',
            'privacy_version' => 'v1',
            'accepted_at' => now(),
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/discover');

        // Pode retornar 200 ou outro erro (ex: sem perfis), mas não 403 de terms
        $response->assertStatus(200);
    }

    /** @test */
    public function acceptance_is_invalidated_when_terms_version_changes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        
        // Aceita versão antiga
        UserTermsAcceptance::create([
            'user_id' => $user->id,
            'terms_version' => 'v1',
            'privacy_version' => 'v1',
            'accepted_at' => now(),
            'ip_address' => '127.0.0.1',
        ]);

        // Simula que a versão atual é v2
        config(['closer.terms_version' => 'v2']);

        $response = $this->actingAs($user)
            ->getJson('/api/discover');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'terms_not_accepted']);
    }

    /** @test */
    public function acceptance_is_invalidated_when_privacy_version_changes()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);
        
        UserTermsAcceptance::create([
            'user_id' => $user->id,
            'terms_version' => 'v1',
            'privacy_version' => 'v1',
            'accepted_at' => now(),
            'ip_address' => '127.0.0.1',
        ]);

        config(['closer.privacy_version' => 'v2']);

        $response = $this->actingAs($user)
            ->getJson('/api/discover');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'terms_not_accepted']);
    }

    /** @test */
    public function user_can_accept_terms()
    {
        $user = User::factory()->create(['nivel_acesso' => UserLevel::FREE->value]);

        $response = $this->actingAs($user)
            ->postJson('/api/terms/accept', [
                'terms_version' => 'v1',
                'privacy_version' => 'v1',
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('user_terms_acceptances', [
            'user_id' => $user->id,
            'terms_version' => 'v1',
            'privacy_version' => 'v1',
        ]);
    }
}
