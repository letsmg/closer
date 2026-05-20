<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserTermsAcceptance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller para gerenciamento de aceite dos Termos de Uso e Política de Privacidade
 * 
 * Fornece endpoints para:
 * - Verificar status do aceite do usuário logado
 * - Registrar novo aceite dos termos vigentes
 */
class TermsAcceptanceController extends Controller
{
    /**
     * Verifica o status do aceite dos termos para o usuário autenticado
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        $requiredTermsVersion = config('terms.version', '2026-05-20');
        $requiredPrivacyVersion = config('terms.privacy_version', '2026-05-20');

        $hasValidAcceptance = UserTermsAcceptance::hasValidAcceptance(
            $user->id,
            $requiredTermsVersion,
            $requiredPrivacyVersion
        );

        // Busca o aceite mais recente do usuário
        $latestAcceptance = UserTermsAcceptance::where('user_id', $user->id)
            ->latest('accepted_at')
            ->first();

        return response()->json([
            'accepted' => $hasValidAcceptance,
            'required_terms_version' => $requiredTermsVersion,
            'required_privacy_version' => $requiredPrivacyVersion,
            'latest_acceptance' => $latestAcceptance ? [
                'terms_version' => $latestAcceptance->terms_version,
                'privacy_version' => $latestAcceptance->privacy_version,
                'accepted_at' => $latestAcceptance->accepted_at,
            ] : null,
            'terms_url' => url('/terms'),
            'privacy_url' => url('/privacy'),
        ]);
    }

    /**
     * Registra o aceite dos termos para o usuário autenticado
     */
    public function accept(Request $request): JsonResponse
    {
        $request->validate([
            'terms_version' => 'required|string',
            'privacy_version' => 'required|string',
        ]);

        $user = $request->user();
        $termsVersion = $request->input('terms_version');
        $privacyVersion = $request->input('privacy_version');

        // Verifica se as versões enviadas correspondem às versões ativas
        $requiredTermsVersion = config('terms.version', '2026-05-20');
        $requiredPrivacyVersion = config('terms.privacy_version', '2026-05-20');

        if ($termsVersion !== $requiredTermsVersion) {
            return response()->json([
                'success' => false,
                'message' => "Versão dos Termos de Uso desatualizada. A versão atual é: {$requiredTermsVersion}.",
                'required_version' => $requiredTermsVersion,
            ], 422);
        }

        if ($privacyVersion !== $requiredPrivacyVersion) {
            return response()->json([
                'success' => false,
                'message' => "Versão da Política de Privacidade desatualizada. A versão atual é: {$requiredPrivacyVersion}.",
                'required_version' => $requiredPrivacyVersion,
            ], 422);
        }

        // Registra o aceite no banco de dados
        $acceptance = UserTermsAcceptance::recordAcceptance(
            $user->id,
            $termsVersion,
            $privacyVersion,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Termos de Uso e Política de Privacidade aceitos com sucesso.',
            'acceptance' => [
                'id' => $acceptance->id,
                'terms_version' => $acceptance->terms_version,
                'privacy_version' => $acceptance->privacy_version,
                'accepted_at' => $acceptance->accepted_at,
            ],
        ]);
    }
}
