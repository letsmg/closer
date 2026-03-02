<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shorts;
use Illuminate\Http\Request;

class ShortsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'conteudo' => 'required|string|max:255',
            'tipo'     => 'required|in:q,r',
            'posicao'  => 'required|integer|between:1,7',
        ]);

        $user = $request->user();

        // Verifica quantas frases desse tipo o usuário já tem
        $contagem = Shorts::where('user_id', $user->id)
                          ->where('tipo', $request->tipo)
                          ->count();

        // Se estiver tentando criar uma NOVA posição e já tem 7
        $existePosicao = Shorts::where('user_id', $user->id)
                               ->where('tipo', $request->tipo)
                               ->where('posicao', $request->posicao)
                               ->exists();

        if ($contagem >= 7 && !$existePosicao) {
            return response()->json(['erro' => 'Você já atingiu o limite de 7 frases para este tipo.'], 400);
        }

        // Usa updateOrCreate para que, se ele enviar a mesma posição, apenas atualize a frase
        $short = Shorts::updateOrCreate(
            ['user_id' => $user->id, 'tipo' => $request->tipo, 'posicao' => $request->posicao],
            ['conteudo' => $request->conteudo]
        );

        return response()->json(['mensagem' => 'Short salvo com sucesso!', 'dado' => $short]);
    }

    public function listarMeusShorts(Request $request)
    {
        return response()->json(
            Shorts::where('user_id', $request->user()->id)
                  ->orderBy('posicao')
                  ->get()
        );
    }
}