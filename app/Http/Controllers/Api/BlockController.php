<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Block;
use App\Models\Report;
use App\Models\UserMatch;
use App\Models\LikeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReputationService;

class BlockController extends Controller
{
    /**
     * Registers a block and optionally a report
     */
    public function store(Request $request)
    {
        // 1. Strict validation
        $request->validate([
            'blocked_user_id' => 'required|exists:users,id',
            'report'        => 'boolean', 
            'reason'          => 'required_if:report,true|in:harassment,disrespect,fake_profile,other',
            'description'       => 'nullable|string|max:500'
        ]);

        $user = $request->user();
        $targetId = $request->blocked_user_id;

        // Prevent self-blocking
        if ($user->id == $targetId) {
            return response()->json(['error' => 'Invalid operation.'], 400);
        }

        return DB::transaction(function () use ($user, $targetId, $request) {
            
            // 2. Register the Block
            // Use firstOrCreate to avoid duplicate error if user clicks twice
            Block::firstOrCreate([
                'profile_id' => $user->profile->id,
                'blocked_profile_id' => $targetId
            ]);

            // 3. Process Report and Reputation (Negative Karma)
            if ($request->report) {
                Report::create([
                    'reporter_id' => $user->id,
                    'reported_id' => $targetId,
                    'reason' => $request->reason,
                    'description' => $request->description,
                    'status' => 'pending'
                ]);
            }
        });
    }
}
