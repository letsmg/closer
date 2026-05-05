<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\SanitizedRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends SanitizedRequest
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
            'username' => 'required|string|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).+$/',
            ],
            'scope' => 'sometimes|string|max:500',
            'device_fingerprint' => 'sometimes|string|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'O email é obrigatório.',
            'username.string' => 'O email deve ser um texto.',
            'username.email' => 'O email deve ser válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos 1 letra maiúscula e 1 caractere especial (!@#$%^&*()).',
            'scope.string' => 'O escopo deve ser um texto.',
            'scope.max' => 'O escopo não pode ter mais de 500 caracteres.',
            'device_fingerprint.string' => 'O fingerprint deve ser um texto.',
            'device_fingerprint.max' => 'O fingerprint não pode ter mais de 255 caracteres.',
        ];
    }

    /**
     * Get sanitized credentials
     */
    public function getCredentials(): array
    {
        return [
            'email' => $this->sanitized('username'),
            'password' => $this->sanitized('password'),
        ];
    }
}
