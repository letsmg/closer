<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePerfilRequest;
use App\Services\PerfilService;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * Retorna perfil do usuário logado
     */
    public function show(Request $request)
    {
        $perfil = $request->user()
            ->perfil()
            ->with('user.fotos', 'hobbies')
            ->first();

        return response()->json($perfil);
    }

    /**
     * Atualiza perfil
     */
    public function update(
        UpdatePerfilRequest $request,
        PerfilService $service
    ) {
        $resultado = $service->update(
            $request->user(),
            $request->validated()
        );

        return response()->json($resultado);
    }
}