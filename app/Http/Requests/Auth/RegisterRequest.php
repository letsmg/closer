<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\SanitizedRequest;

class RegisterRequest extends SanitizedRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,}$/',
            ],
            'password_confirmation' => 'required|string|min:8|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.regex' => 'A senha deve conter pelo menos 1 letra maiúscula, 1 letra minúscula e 1 caractere especial (!@#$%^&*()).',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
            'password_confirmation.string' => 'A confirmação de senha deve ser um texto.',
            'password_confirmation.min' => 'A confirmação de senha deve ter pelo menos 8 caracteres.',
            'password_confirmation.max' => 'A confirmação de senha não pode ter mais de 255 caracteres.',
        ];
    }

    /**
     * Get sanitized user data
     */
    public function getUserData(): array
    {
        return [
            'name' => $this->sanitized('name'),
            'email' => $this->sanitized('email'),
            'password' => $this->sanitized('password'),
        ];
    }
}
