<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Returns logged user's profile
     */
    public function show(Request $request)
    {
        $profile = $request->user()
            ->profile()
            ->with('user.profilePhotos', 'hobbies')
            ->first();

        return response()->json($profile);
    }

    /**
     * Updates profile
     */
    public function update(
        UpdateProfileRequest $request,
        ProfileService $service
    ) {
        $result = $service->update(
            $request->user(),
            $request->validated()
        );

        return response()->json($result);
    }
}
