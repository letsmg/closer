<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;

class ProfileService
{
    private const PROFILE_FIELDS = [
        'nickname',
        'birth_date',
        'gender',
        'gender_identity',
        'sexual_orientation',
        'purpose',
        'profession',
        'biography',
        'smoker',
        'drinker',
        'marital_status',
        'country_id',
        'state_id',
        'city_id',
        'visibility',
        'contact_methods',
    ];

    private const PREFERENCE_FIELDS = [
        'gender',
        'gender_identity',
        'sexual_orientation',
        'purpose',
        'smoker',
        'drinker',
        'marital_status',
        'country_id',
        'state_id',
        'city_id',
        'search_radius_km',
        'min_age',
        'max_age',
        'visibility',
        'allow_global_search',
        'hide_location',
        'invisible_mode',
        'interested_hobbies',
        'discoverable_levels',
        'visible_levels',
    ];

    public function update(User $user, array $data): array
    {
        $profile = $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'nickname' => $user->name,
                'birth_date' => now()->subYears(18)->toDateString(),
                'gender' => 'other',
                'purpose' => 'all',
                'visibility' => 'public',
            ]
        );

        $profileData = Arr::only($data, self::PROFILE_FIELDS);
        if ($profileData !== []) {
            $profile->update($profileData);
        }

        if (array_key_exists('preference', $data)) {
            $preferenceData = Arr::only($data['preference'] ?? [], self::PREFERENCE_FIELDS);

            if (!$user->canFilterByLevel()) {
                unset($preferenceData['discoverable_levels'], $preferenceData['visible_levels']);
            }

            if ($preferenceData !== []) {
                $profile->preference()->updateOrCreate(
                    ['profile_id' => $profile->id],
                    $preferenceData
                );
            }
        }

        return $profile->fresh(['user', 'preference', 'hobbies', 'photos'])->toArray();
    }
}
