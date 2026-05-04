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
            'username' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
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
            'username.email' => 'Digite um email válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
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
