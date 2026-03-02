<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Perfil;
use App\Services\MatchService;
use Illuminate\Http\Request;

class InteracaoController extends Controller
{
    protected $matchService;

    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function like(Request $request, $perfilId)
    {
        $perfilOrigem = $request->user()->perfil;
        $perfilDestino = Perfil::findOrFail($perfilId);

        $resultado = $this->matchService->registrarLike($perfilOrigem, $perfilDestino);

        return response()->json($resultado);
    }

    public function dislike(Request $request, $perfilId)
    {
        $perfilOrigem = $request->user()->perfil;

        Like::updateOrCreate(
            [
                'perfil_origem_id' => $perfilOrigem->id,
                'perfil_destino_id' => $perfilId,
            ],
            ['tipo' => 'dislike']
        );

        return response()->json(['status' => 'ok']);
    }

    public function segundaChance(Request $request, $perfilId)
    {
        $perfilOrigem = $request->user()->perfil;

        $like = Like::where('perfil_origem_id', $perfilOrigem->id)
            ->where('perfil_destino_id', $perfilId)
            ->where('tipo', 'dislike')
            ->firstOrFail();

        $like->update(['tipo' => 'like']);

        $resultado = $this->matchService->registrarLike(
            $perfilOrigem,
            Perfil::findOrFail($perfilId)
        );

        if (!$resultado['match']) {
            $revelado = $this->matchService->revelarProximoLike($perfilOrigem);
            $resultado['revelacao'] = $revelado;
        }

        return response()->json($resultado);
    }
}