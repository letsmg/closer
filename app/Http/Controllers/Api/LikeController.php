<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\UserMatch;
use App\Models\SegundaChance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CURTIR OU PASSAR
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'liked_user_id' => 'required|exists:users,id|not_in:' . $user->id,
            'is_like' => 'required|boolean'
        ]);

        // 🔒 Limite diário para nível gratuito
        if ($user->nivel_acesso == 0 && $request->is_like) {

            $likesHoje = Like::where('user_id', $user->id)
                ->where('is_like', true)
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($likesHoje >= 20) {
                return response()->json([
                    'error' => 'Limite diário de 20 curtidas atingido.',
                    'code' => 'LIMIT_LIKES_REACHED'
                ], 403);
            }
        }

        $targetId = $request->liked_user_id;
        $isLike   = $request->is_like;

        return DB::transaction(function () use ($user, $targetId, $isLike) {

            // Atualiza ou cria interação
            $like = Like::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'liked_user_id' => $targetId
                ],
                [
                    'is_like' => $isLike
                ]
            );

            // Se for DISLIKE
            if (!$isLike) {

                SegundaChance::firstOrCreate([
                    'perfil_id' => $user->perfil->id,
                    'like_id'   => $like->id
                ]);

                return response()->json([
                    'status' => 'disliked'
                ]);
            }

            // 🔥 Incrementa karma do perfil curtido
            DB::table('perfis')
                ->where('user_id', $targetId)
                ->increment('pontos_likes');

            // 🔁 Verifica reciprocidade
            $matchBack = Like::where('user_id', $targetId)
                ->where('liked_user_id', $user->id)
                ->where('is_like', true)
                ->exists();

            if ($matchBack) {

                $id1 = min($user->id, $targetId);
                $id2 = max($user->id, $targetId);

                $match = UserMatch::firstOrCreate([
                    'user_one_id' => $id1,
                    'user_two_id' => $id2
                ]);

                return response()->json([
                    'status' => 'match',
                    'message' => 'É um Match! Vocês já podem conversar.',
                    'match_id' => $match->id
                ]);
            }

            return response()->json([
                'status' => 'liked'
            ]);
        });
    }
}