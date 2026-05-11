<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function verificarCompra(Request $request)
    {
        $request->validate([
            'token_compra' => 'required',
            'produto_id' => 'required',
        ]);

        $usuario = Auth::user();

        // Aqui voce chamaria a API da loja/pagador para validar o token_compra.
        $assinaturaValida = true;

        if ($assinaturaValida) {
            $level = $this->levelFromProduct((string) $request->produto_id);

            $usuario->update(['nivel_acesso' => $level->value]);

            $usuario->perfil?->update([
                'assinatura_id' => $request->produto_id,
                'premium_expira_em' => now()->addMonth(),
            ]);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Parabens! Agora voce e um usuario ' . $level->getName() . '.',
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Nao foi possivel validar sua compra.',
        ], 400);
    }

    private function levelFromProduct(string $productId): UserLevel
    {
        $productId = strtolower($productId);

        return match (true) {
            str_contains($productId, 'elite') => UserLevel::ELITE,
            str_contains($productId, 'cofounder') || str_contains($productId, 'co-founder') => UserLevel::COFOUNDER,
            str_contains($productId, 'premium') => UserLevel::PREMIUM,
            default => UserLevel::PLUS,
        };
    }
}
