<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProfileRequest extends SanitizedRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $customerLevels = [0, 1, 2, 3, 4, 5];

        return [
            'nickname' => ['sometimes', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'date', 'before:-18 years'],
            'gender' => ['sometimes', 'string', 'max:50'],
            'gender_identity' => ['sometimes', 'nullable', 'string', 'max:100'],
            'sexual_orientation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'purpose' => ['sometimes', 'nullable', 'string', 'max:100'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:255'],
            'biography' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'smoker' => ['sometimes', 'nullable', 'boolean'],
            'drinker' => ['sometimes', 'nullable', 'boolean'],
            'marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'country_id' => ['sometimes', 'nullable', 'integer', 'exists:countries,id'],
            'state_id' => ['sometimes', 'nullable', 'integer', 'exists:states,id'],
            'city_id' => ['sometimes', 'nullable', 'integer', 'exists:cities,id'],
            'visibility' => ['sometimes', Rule::in(['public', 'hidden', 'matches_only'])],
            'contact_methods' => ['sometimes', 'nullable', 'array'],

            'preference' => ['sometimes', 'array'],
            'preference.gender' => ['sometimes', 'nullable', 'string', 'max:50'],
            'preference.gender_identity' => ['sometimes', 'nullable', 'string', 'max:100'],
            'preference.sexual_orientation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'preference.purpose' => ['sometimes', 'nullable', 'string', 'max:100'],
            'preference.smoker' => ['sometimes', 'nullable', 'boolean'],
            'preference.drinker' => ['sometimes', 'nullable', 'boolean'],
            'preference.marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'preference.country_id' => ['sometimes', 'nullable', 'integer', 'exists:countries,id'],
            'preference.state_id' => ['sometimes', 'nullable', 'integer', 'exists:states,id'],
            'preference.city_id' => ['sometimes', 'nullable', 'integer', 'exists:cities,id'],
            'preference.search_radius_km' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:200'],
            'preference.min_age' => ['sometimes', 'nullable', 'integer', 'min:18', 'max:85'],
            'preference.max_age' => ['sometimes', 'nullable', 'integer', 'min:18', 'max:85', 'gte:preference.min_age'],
            'preference.visibility' => ['sometimes', 'nullable', 'string', 'max:50'],
            'preference.allow_global_search' => ['sometimes', 'nullable', 'boolean'],
            'preference.hide_location' => ['sometimes', 'nullable', 'boolean'],
            'preference.invisible_mode' => ['sometimes', 'nullable', 'boolean'],
            'preference.interested_hobbies' => ['sometimes', 'nullable', 'array'],
            'preference.interested_hobbies.*' => ['integer', 'exists:hobbies,id'],
            'preference.discoverable_levels' => ['sometimes', 'nullable', 'array'],
            'preference.discoverable_levels.*' => ['integer', Rule::in($customerLevels)],
            'preference.visible_levels' => ['sometimes', 'nullable', 'array'],
            'preference.visible_levels.*' => ['integer', Rule::in($customerLevels)],
        ];
    }
}
