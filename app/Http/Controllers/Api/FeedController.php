<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\UserMatch;
use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function buscarPerfis(Request $request)
    {
        $user = Auth::user();
        $perfilUsuario = $user->perfil;

        if (!$perfilUsuario || !$perfilUsuario->cidade) {
            return response()->json([
                'message' => 'Defina sua cidade antes de visualizar o feed.'
            ], 422);
        }

        $latUsuario = $perfilUsuario->cidade->latitude;
        $lngUsuario = $perfilUsuario->cidade->longitude;

        // =============================
        // 1. PERFIS A EXCLUIR
        // =============================

        $idsInteragidos = Like::where('user_id', $user->id)
            ->whereDoesntHave('segundaChance', function ($q) {
                $q->whereNotNull('usado_em');
            })
            ->pluck('liked_user_id');

        $idsMatches = UserMatch::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->get()
            ->flatMap(fn($m) => [$m->user_one_id, $m->user_two_id]);

        $idsBloqueadosPorMim = $perfilUsuario->bloqueados()
            ->pluck('perfil_bloqueado_id');

        $idsQueMeBloquearam = $perfilUsuario->bloqueadoPor()
            ->pluck('perfil_id');

        $excluirIds = $idsInteragidos
            ->concat($idsMatches)
            ->concat($idsBloqueadosPorMim)
            ->concat($idsQueMeBloquearam)
            ->unique();

        // =============================
        // 2. QUERY BASE + DISTÂNCIA
        // =============================

        $query = Perfil::query()
            ->join('cidades', 'cidades.id', '=', 'perfis.cidade_id')
            ->join('users', 'users.id', '=', 'perfis.user_id')
            ->whereNotIn('perfis.id', $excluirIds)
            ->where('perfis.user_id', '!=', $user->id)
            ->where('perfis.visibilidade', false)
            ->where('users.ativo', true);

        // =============================
        // 3. FILTROS DE PREFERÊNCIA
        // =============================

        $min = $perfilUsuario->preferencia->idade_minima ?? 18;
        $max = $perfilUsuario->preferencia->idade_maxima ?? 99;

        $query->whereBetween('perfis.data_nascimento', [
            now()->subYears($max),
            now()->subYears($min)
        ]);

        if ($perfilUsuario->preferencia?->sexo !== 'todos') {
            $query->where('perfis.sexo', $perfilUsuario->preferencia->sexo);
        }

        if ($perfilUsuario->preferencia?->objetivo !== 'todos') {
            $query->where('perfis.objetivo', $perfilUsuario->preferencia->objetivo);
        }

        // =============================
        // 4. CÁLCULO DE DISTÂNCIA (HAVERSINE)
        // =============================
        //esse 6371 é o adio médio da terra em km, se quiser em milhas use 3958.8
        //e acos é uma função que calcula o arco cosseno, radians converte graus para radianos, e cos/sin são funções trigonométricas
        $query->selectRaw("
            perfis.*,
            users.nivel_acesso,
            (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(cidades.latitude)) *
                    cos(radians(cidades.longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(cidades.latitude))
                )
            ) AS distancia
        ", [$latUsuario, $lngUsuario, $latUsuario]);

        // =============================
        // 5. ORDEM INTELIGENTE
        // =============================

        $perfis = $query
            ->orderBy('distancia')               // mais perto primeiro
            ->orderByDesc('nivel_acesso')        // premium primeiro se empate
            ->latest('perfis.created_at')
            ->with(['user.fotos', 'cidade', 'estado', 'preferencia'])
            ->paginate(20);

        return response()->json($perfis);
    }

    public function index(Request $request)
    {
        return $this->buscarPerfis($request);
    }
}