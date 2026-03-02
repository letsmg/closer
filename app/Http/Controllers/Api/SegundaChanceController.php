<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SegundaChance;
use Illuminate\Http\Request;

class SegundaChanceController extends Controller
{
    // LISTAR SEGUNDAS CHANCES DISPONÍVEIS
    public function index(Request $request)
    {
        $perfil = $request->user()->perfil;

        $segundas = SegundaChance::with([
                'like.likedUser.perfil.cidade',
                'like.likedUser.fotos'
            ])
            ->where('perfil_id', $perfil->id)
            ->whereNull('usado_em')
            ->latest()
            ->paginate(20);

        return response()->json($segundas);
    }

    // USAR SEGUNDA CHANCE
    public function usar($id)
    {
        $segundaChance = SegundaChance::findOrFail($id);

        if ($segundaChance->usado_em) {
            return response()->json(['error' => 'Já utilizada'], 400);
        }

        $segundaChance->update([
            'usado_em' => now()
        ]);

        return response()->json(['status' => 'ok']);
    }
}