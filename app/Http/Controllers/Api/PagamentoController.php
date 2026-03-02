<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagamentoController extends Controller
{
    public function verificarCompra(Request $request)
    {
        $request->validate([
            'token_compra' => 'required',
            'produto_id'   => 'required' // Ex: 'assinatura_plus_mensal'
        ]);

        $usuario = Auth::user();

        // Aqui você chamaria a API do Google para validar o token_compra.
        // Por enquanto, vamos simular que a validação foi um sucesso.
        $assinaturaValida = true; 

        if ($assinaturaValida) {
            $usuario->update([
                'nivel_acesso'       => 1, // Torna o usuário Plus
                'assinatura_id'      => $request->produto_id,
                'premium_expira_em'  => now()->addMonths(1), // Adiciona 30 dias
            ]);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Parabéns! Agora você é um usuário Plus.'
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Não foi possível validar sua compra na Play Store.'
        ], 400);
    }
}