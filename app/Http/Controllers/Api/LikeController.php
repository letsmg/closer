<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LikeModel;
use App\Models\UserMatch;
use App\Models\SecondChance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CURTIR OR PASS
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'liked_user_id' => 'required|exists:users,id|not_in:' . $user->id,
            'is_like' => 'required|boolean'
        ]);

        // Daily limit for free level
        if ($user->nivel_acesso == 0 && $request->is_like) {

            $likesToday = LikeModel::where('user_id', $user->id)
                ->where('is_like', true)
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($likesToday >= 20) {
                return response()->json([
                    'error' => 'Daily limit of 20 likes reached.',
                    'code' => 'LIMIT_LIKES_REACHED'
                ], 403);
            }
        }

        $targetId = $request->liked_user_id;
        $isLike   = $request->is_like;

        return DB::transaction(function () use ($user, $targetId, $isLike) {

            // Update or create interaction
            $like = LikeModel::updateOrCreate(
                ['user_id' => $user->id, 'liked_user_id' => $targetId],
                ['is_like' => $isLike]
            );

            // If it's a like, check for match and create second chance
            if ($isLike) {
                $this->checkForMatchAndCreateSecondChance($user, $targetId, $like);
            }

            return response()->json($like);
        });
    }

    /**
     * Check for mutual like and create second chance
     */
    private function checkForMatchAndCreateSecondChance($user, $targetId, $like)
    {
        // Check if target user also liked current user
        $mutualLike = LikeModel::where('user_id', $targetId)
            ->where('liked_user_id', $user->id)
            ->where('is_like', true)
            ->first();

        if ($mutualLike) {
            // Create match
            $match = UserMatch::create([
                'user1_id' => $user->id,
                'user2_id' => $targetId,
                'matched_at' => now()
            ]);

            // Create second chances for both users
            SecondChance::create([
                'profile_id' => $user->id,
                'like_id' => $like->id,
            ]);

            SecondChance::create([
                'profile_id' => $targetId,
                'like_id' => $mutualLike->id,
            ]);
        }
    }
}
