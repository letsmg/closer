<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REGISTRO
    |--------------------------------------------------------------------------
    | - Cria usuário
    | - Cria perfil inicial
    | - Envia email de verificação
    | - NÃO faz login automático
    */
    public function cadastrar(Request $request)
    {
        // 1. Validação (Adicionei o confirmed para segurança)
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed', // Android deve enviar password_confirmation

            // Dados do perfil
            'idade'     => 'required|integer',
            'sexo'      => 'required|string',
            'pais_id'   => 'required|exists:paises,id',
            'estado_id' => 'required|exists:estados,id',
            'cidade_id' => 'required|exists:cidades,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // 2. Criação do usuário
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'ultimo_ip' => $request->ip(),
                ]);

                // 3. Criação do perfil
                $user->perfil()->create([
                    'idade'             => $request->idade,
                    'sexo'              => $request->sexo,
                    'orientacao_sexual' => $request->get('orientacao_sexual', 'não definido'),
                    'pais_id'           => $request->pais_id,
                    'estado_id'         => $request->estado_id,
                    'cidade_id'         => $request->cidade_id,
                ]);

                // 4. Envia email de verificação
                // Nota: O model User deve ter: class User extends Authenticatable implements MustVerifyEmail
                $user->sendEmailVerificationNotification();

                return response()->json([
                    'status' => 'sucesso',
                    'message' => 'Cadastro realizado com sucesso. Verifique seu e-mail antes de fazer login.'
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'message' => 'Erro ao processar cadastro. Tente novamente mais tarde.',
                'debug' => $e->getMessage() 
            ], 500);
        }
    }


    public function atualizar(Request $request)
    {
        $usuario = $request->user(); // Pega o usuário logado pelo Token

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'biografia' => 'nullable|string|max:500',
            'sexo' => 'sometimes|string',
            'cidade_id' => 'sometimes|exists:cidades,id',
        ]);

        try {
            DB::transaction(function () use ($request, $usuario) {
                // Atualiza dados na tabela users
                if ($request->has('name')) {
                    $usuario->update(['name' => $request->name]);
                }

                // Atualiza dados na tabela perfis
                $usuario->perfil()->update($request->only([
                    'biografia', 'sexo', 'cidade_id', 'idade', 'orientacao_sexual'
                ]));
            });

            return response()->json([
                'status' => 'sucesso',
                'mensagem' => 'Perfil atualizado com sucesso!',
                'dados' => $usuario->load('perfil')
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'erro', 'message' => 'Erro ao atualizar.'], 500);
        }
    }





    /**
     * EXCLUIR CONTA (Seguindo boas práticas de LGPD)
     */
    public function excluir(Request $request)
    {
        $usuario = $request->user();

        try {
            // Opcional: Se quiser apenas desativar e manter logs
            // $usuario->update(['ativo' => false]);
            
            // Se o model User usar SoftDeletes, ele apenas marca a data de exclusão
            $usuario->delete(); 

            return response()->json([
                'status' => 'sucesso',
                'mensagem' => 'Sua conta foi excluída e seus dados de perfil removidos conforme a LGPD.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'erro', 'message' => 'Erro ao excluir conta.'], 500);
        }
    }


    
}