<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\LikeModel;
use App\Models\UserMatch;
use App\Models\Report;
use App\Models\Hobby;
use App\Enums\UserLevel;
use App\Traits\HasSanitization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiscoveryController extends Controller
{
    use HasSanitization;

    /**
     * Retorna perfis para o swipe (Tinder-like) respeitando:
     * 1. Apenas usuários ativos que não estejam invisíveis
     * 2. Região de interesse com o raio cadastrado, sem mostrar região bloqueada
     * 3. Excluir pessoas bloqueadas
     * 4. Ordem de sexo e orientação sexual compatível
     * 5. Idade (18-85 anos)
     * 6. Interesses iguais
     * 
     * Para usuários PLUS/PREMIUM:
     * - Pode bloquear região (0-200km)
     * - Pode esconder localização
     * - Invisible mode (apenas PREMIUM)
     */
    public function discover(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['message' => 'Complete seu perfil primeiro para descobrir novas pessoas.'], 422);
        }

        $profile->loadMissing([
            'preference',
            'city',
            'blockedRegions',
            'hobbies',
        ]);

        // =============================
        // 1. IDs A EXCLUIR
        // =============================

        // Quem já interagi (like/dislike)
        $interactedIds = LikeModel::where('user_id', $user->id)
            ->pluck('liked_user_id');

        // Quem já deu match
        $matchUserIds = UserMatch::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->get()
            ->flatMap(fn($m) => [$m->user_one_id, $m->user_two_id]);

        // Quem eu bloqueei (profile_ids)
        $blockedProfileIds = $profile->blockedProfiles()->pluck('profiles.id');

        // Quem me bloqueou
        $blockedByProfileIds = $profile->blockedByProfiles()->pluck('profiles.id');

        // Quem eu denunciei -> bloqueio automático
        $reportedUserIds = Report::where('reporter_id', $user->id)
            ->pluck('reported_id');

        // Converte profile_ids para user_ids
        $blockedUserIds = Profile::whereIn('id', $blockedProfileIds)->pluck('user_id');
        $blockedByUserIds = Profile::whereIn('id', $blockedByProfileIds)->pluck('user_id');

        $excludeUserIds = collect([$user->id])
            ->concat($interactedIds)
            ->concat($matchUserIds)
            ->concat($blockedUserIds)
            ->concat($blockedByUserIds)
            ->concat($reportedUserIds)
            ->unique();

        // =============================
        // 2. REGIÕES BLOQUEADAS
        // =============================

        $blockedRegions = $profile->blockedRegions;
        $blockedCountryIds = $blockedRegions->pluck('country_id')->filter();
        $blockedStateIds = $blockedRegions->pluck('state_id')->filter();
        $blockedCityIds = $blockedRegions->pluck('city_id')->filter();

        // =============================
        // 3. QUERY BASE
        // =============================

        $preference = $profile->preference;

        $query = Profile::query()
            ->join('users', 'users.id', '=', 'profiles.user_id')
            ->leftJoin('profile_preferences', 'profile_preferences.profile_id', '=', 'profiles.id')
            ->leftJoin('profile_photos', function ($join) {
                $join->on('profile_photos.user_id', '=', 'profiles.user_id')
                     ->where('profile_photos.is_primary', true);
            })
            ->where('users.ativo', true)
            ->whereNotIn('profiles.user_id', $excludeUserIds);

        // 1. Apenas pessoas ativas que não estejam invisíveis
        // Usuários com invisible_mode = true NÃO aparecem (exceto staff)
        $query->where(function ($q) {
            $q->whereNull('profile_preferences.invisible_mode')
              ->orWhere('profile_preferences.invisible_mode', false);
        });

        // 2. Apenas perfis com visibilidade pública
        $query->where('profiles.visibility', 'public');

        // Filtros por nivel:
        // - discoverable_levels: niveis que o usuario logado deseja ver
        // - visible_levels: niveis autorizados pelo perfil alvo a ve-lo
        try {
            $userLevel = UserLevel::tryFrom((int) $user->nivel_acesso);
            $currentLevel = (int) $user->nivel_acesso;

            if ($userLevel && $userLevel->canFilterByLevel() && $preference) {
                $discoverableLevels = $preference->discoverable_levels ?? null;

                if (!empty($discoverableLevels) && is_array($discoverableLevels)) {
                    $query->whereIn('users.nivel_acesso', $discoverableLevels);
                }
            }

            $query->where(function ($q) use ($currentLevel) {
                $q->whereNull('profile_preferences.visible_levels')
                  ->orWhereJsonLength('profile_preferences.visible_levels', 0)
                  ->orWhereJsonContains('profile_preferences.visible_levels', $currentLevel);
            });
        } catch (\Exception $e) {
            // Ignora erro se a coluna não existir
        }

        // Filtro por região bloqueada
        if ($user->canBlockRegion() && ($blockedCountryIds->isNotEmpty() || $blockedStateIds->isNotEmpty() || $blockedCityIds->isNotEmpty())) {
            $query->where(function ($q) use ($blockedCountryIds, $blockedStateIds, $blockedCityIds) {
                $q->whereNotIn('profiles.country_id', $blockedCountryIds);
                if ($blockedStateIds->isNotEmpty()) {
                    $q->whereNotIn('profiles.state_id', $blockedStateIds);
                }
                if ($blockedCityIds->isNotEmpty()) {
                    $q->whereNotIn('profiles.city_id', $blockedCityIds);
                }
            });
        }

        // =============================
        // 4. FILTROS DE PREFERÊNCIA
        // =============================

        if ($preference) {
            // 5. Idade (mínimo 18, máximo 85)
            $minAge = max(18, $preference->min_age ?? 18);
            $maxAge = min(85, $preference->max_age ?? 85);
            $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, profiles.birth_date, CURDATE())'), [$minAge, $maxAge]);

            // 4. Gênero e orientação sexual compatível
            if (!empty($preference->gender) && $preference->gender !== 'todos') {
                $query->where('profiles.gender', $preference->gender);
            }
            
            if (!empty($preference->sexual_orientation) && $preference->sexual_orientation !== 'todos') {
                $query->where('profiles.sexual_orientation', $preference->sexual_orientation);
            }

            // Propósito
            if (!empty($preference->purpose) && $preference->purpose !== 'todos') {
                $query->where('profiles.purpose', $preference->purpose);
            }

            // 6. Interesses iguais (hobbies em comum)
            $interestedHobbies = $preference->interested_hobbies;
            if (!empty($interestedHobbies) && is_array($interestedHobbies)) {
                $query->whereExists(function ($q) use ($interestedHobbies) {
                    $q->select(DB::raw(1))
                      ->from('profile_hobbies')
                      ->whereColumn('profile_hobbies.profile_id', 'profiles.id')
                      ->whereIn('profile_hobbies.hobby_id', $interestedHobbies);
                });
            }
        }

        // =============================
        // 5. SELECT COM OU SEM DISTÂNCIA
        // =============================

        $useDistance = $preference && $preference->search_radius_km && $profile->city && !$preference->hide_location;

        if ($useDistance) {
            $lat = $profile->city->latitude;
            $lng = $profile->city->longitude;
            $radius = min($preference->search_radius_km, 200); // Max 200km

            $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(cities.latitude)) * cos(radians(cities.longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(cities.latitude)) * sin(radians(cities.longitude) - radians($lng))))";

            $query->join('cities', 'cities.id', '=', 'profiles.city_id')
                ->selectRaw("profiles.*, users.nivel_acesso, profile_preferences.invisible_mode, profile_preferences.hide_location, profile_preferences.interested_hobbies, profile_preferences.discoverable_levels, profile_preferences.visible_levels, {$haversine} AS distance, profile_photos.path as primary_photo_path")
                ->havingRaw("distance <= ?", [$radius]);
        } else {
            $query->selectRaw("profiles.*, users.nivel_acesso, profile_preferences.invisible_mode, profile_preferences.hide_location, profile_preferences.interested_hobbies, profile_preferences.discoverable_levels, profile_preferences.visible_levels, 0 AS distance, profile_photos.path as primary_photo_path");
        }

        // =============================
        // 6. ORDEM INTELIGENTE
        // =============================

        $perfis = $query
            ->orderByDesc('users.nivel_acesso')
            ->orderBy('distance', 'asc')
            ->orderByDesc('profiles.ultima_interacao_at')
            ->orderByDesc('profiles.reputacao')
            ->with([
                'user',
                'city:id,name,state_id',
                'city.state:id,name',
                'hobbies',
                'photos' => function ($q) {
                    $q->orderBy('is_primary', 'desc')->orderBy('order');
                },
            ])
            ->paginate(20);

        // Verificar se esgotaram os perfis
        $totalCount = $perfis->total();
        $hasMore = $perfis->hasMorePages();
        
        $response = [
            'data' => $perfis->items(),
            'current_page' => $perfis->currentPage(),
            'last_page' => $perfis->lastPage(),
            'per_page' => $perfis->perPage(),
            'total' => $totalCount,
            'has_more' => $hasMore,
            'exhausted' => $totalCount === 0 && !$hasMore,
        ];

        // Se esgotou, adicionar mensagem para redefinir filtros
        if ($totalCount === 0) {
            $response['message'] = 'Nenhum perfil disponível no momento. Tente ajustar seus filtros.';
            $response['suggestion'] = 'Aumente o raio de busca, faixa de idade ou interesses para encontrar mais pessoas.';
        }

        return response()->json($response);
    }

    /**
     * Registra um like
     */
    public function like(Request $request, $profileId)
    {
        $user = Auth::user();
        $targetProfile = Profile::findOrFail($profileId);

        LikeModel::updateOrCreate(
            [
                'user_id' => $user->id,
                'liked_user_id' => $targetProfile->user_id,
            ],
            ['is_like' => true]
        );

        // Incrementa contador diário de likes
        $today = now()->toDateString();
        if ($user->daily_likes_date === $today) {
            $user->increment('daily_likes_count');
        } else {
            $user->daily_likes_count = 1;
            $user->daily_likes_date = $today;
            $user->save();
        }

        // Verifica se houve match (like recíproco)
        $reciprocalLike = LikeModel::where('user_id', $targetProfile->user_id)
            ->where('liked_user_id', $user->id)
            ->where('is_like', true)
            ->exists();

        $match = null;
        if ($reciprocalLike) {
            $match = UserMatch::create([
                'user_one_id' => $user->id,
                'user_two_id' => $targetProfile->user_id,
                'matched_at' => now(),
            ]);

            $profile = $user->profile;
            if ($profile) {
                $profile->update(['ultima_interacao_at' => now()]);
            }
            $targetProfile->update(['ultima_interacao_at' => now()]);
        }

        return response()->json([
            'status' => 'ok',
            'is_like' => true,
            'match' => $match ? [
                'id' => $match->id,
                'user' => [
                    'id' => $targetProfile->user_id,
                    'name' => $targetProfile->user?->name,
                    'nickname' => $targetProfile->nickname,
                ],
            ] : null,
        ]);
    }

    /**
     * Registra um dislike
     */
    public function dislike(Request $request, $profileId)
    {
        $user = Auth::user();
        $targetProfile = Profile::findOrFail($profileId);

        LikeModel::updateOrCreate(
            [
                'user_id' => $user->id,
                'liked_user_id' => $targetProfile->user_id,
            ],
            ['is_like' => false]
        );

        return response()->json(['status' => 'ok', 'is_like' => false]);
    }
}
