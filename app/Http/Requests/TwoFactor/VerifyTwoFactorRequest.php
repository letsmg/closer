<?php

namespace App\Http\Requests\TwoFactor;

use App\Http\Requests\SanitizedRequest;

class VerifyTwoFactorRequest extends SanitizedRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|digits:6',
            'temp_token' => 'required|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'O código de verificação é obrigatório.',
            'code.string' => 'O código deve ser um texto.',
            'code.digits' => 'O código deve ter exatamente 6 dígitos.',
            'temp_token.required' => 'O token temporário é obrigatório.',
            'temp_token.string' => 'O token temporário deve ser um texto.',
        ];
    }
}
