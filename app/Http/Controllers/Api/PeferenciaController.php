<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Preferencia;
use Illuminate\Http\Request;

class PreferenciaController extends Controller
{
    // Lista todos os preferencias disponíveis no sistema
    public function index()
    {
        return response()->json(Preferencia::all());
    }

    // Salva os preferencias escolhidos pelo usuário (Sincronização NxN)
    public function sincronizar(Request $request)
    {
        $request->validate([
            'preferencias' => 'required|array',
            'preferencias.*' => 'exists:preferencias,id'
        ]);

        $usuario = $request->user();
        
        // O método sync substitui os antigos pelos novos enviados
        $usuario->preferencias()->sync($request->preferencias);

        return response()->json([
            'message' => 'Preferencias atualizados!',
            'atuais' => $usuario->preferencias
        ]);
    }
}