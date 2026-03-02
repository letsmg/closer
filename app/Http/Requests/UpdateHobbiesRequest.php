<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // você pode colocar regra de auth aqui se quiser
    }

    public function rules(): array
    {
        return [
            // HOBBIES (máximo 10)
            'hobbies' => 'sometimes|array|max:10',
            'hobbies.*' => 'exists:hobbies,id',

            // Dados perfil
            'data_nascimento'   => 'sometimes|date_format:Y-m-d|before:today',
            'altura'            => 'sometimes|integer|min:100|max:250',
            'peso'              => 'sometimes|integer|min:30|max:300',
            'profissao'         => 'sometimes|string|max:100',
            'biografia'         => 'sometimes|string|max:500',

            'fumante'           => 'sometimes|boolean',
            'bebida'            => 'sometimes|boolean',

            'estado_civil'      => 'sometimes|in:solteiro,casado,divorciado,viuvo',
            'sexo'              => 'sometimes|in:masculino,feminino',
            'identidade_genero' => 'sometimes|string|max:50',

            'cidade_id'         => 'sometimes|exists:cidades,id',
            'estado_id'         => 'sometimes|exists:estados,id',

            'visibilidade'         => 'sometimes|boolean',
        ];
    }
}