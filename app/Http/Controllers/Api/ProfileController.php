<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use App\Traits\SanitizesOutput;

class ProfileController extends Controller
{
    use SanitizesOutput;
    /**
     * Returns logged user's profile
     */
    public function show(Request $request)
    {
        $profile = $request->user()
            ->profile()
            ->with('user.profilePhotos', 'hobbies')
            ->first();

        return $this->safeJsonResponse($profile->toArray());
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

        return $this->safeJsonResponse($result);
    }
}
