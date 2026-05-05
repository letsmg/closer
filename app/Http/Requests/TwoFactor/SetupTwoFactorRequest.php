<?php

namespace App\Http\Requests\TwoFactor;

use App\Http\Requests\SanitizedRequest;

class SetupTwoFactorRequest extends SanitizedRequest
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
            'password' => 'required|string',
            'code' => 'required|string|digits:6',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'password.required' => 'A senha é obrigatória para confirmar a configuração do 2FA.',
            'password.string' => 'A senha deve ser um texto.',
            'code.required' => 'O código de verificação é obrigatório.',
            'code.string' => 'O código deve ser um texto.',
            'code.digits' => 'O código deve ter exatamente 6 dígitos.',
        ];
    }
}
