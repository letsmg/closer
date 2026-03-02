<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $emailNormalizado = strtolower(trim($request->email));
        $hashEmail = $this->gerarHashEmail($emailNormalizado);

        /*
        |--------------------------------------------------------------------------
        | 🔒 Verifica se email já foi banido
        |--------------------------------------------------------------------------
        */
        $emailBanido = DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();

        if ($emailBanido) {
            return response()->json([
                'message' => 'Este email não pode ser utilizado para cadastro.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Cria usuário
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $emailNormalizado,
            'password' => Hash::make($request->password),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Gera token Sanctum
        |--------------------------------------------------------------------------
        */
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN (mantém o seu código atual)
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        \Log::info('Tentativa de login:', $request->all());

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return response()->json([
                'message' => 'Você precisa confirmar seu e-mail antes de fazer login.'
            ], 403);
        }

        if (!$user->ativo) {
            Auth::logout();
            return response()->json([
                'message' => 'Sua conta está desativada.'
            ], 403);
        }

        $user->update([
            'ultimo_ip'        => $request->ip(),
            'ultimo_login_em'  => now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('perfil')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Função privada para gerar hash do email
    |--------------------------------------------------------------------------
    */
    private function gerarHashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    public function banirUsuario(Request $request, $userId)
    {
        $admin = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Verifica se é ADMIN (nível 3)
        |--------------------------------------------------------------------------
        */
        if (!$admin || $admin->nivel !== 3) {
            return response()->json([
                'message' => 'Apenas administradores podem realizar banimento.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Busca usuário a ser banido
        |--------------------------------------------------------------------------
        */
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Impede admin de banir outro admin
        |--------------------------------------------------------------------------
        */
        if ($user->nivel === 3) {
            return response()->json([
                'message' => 'Não é permitido banir outro administrador.'
            ], 403);
        }
        if ($admin->id === $user->id) {
            return response()->json([
                'message' => 'Você não pode banir a si mesmo.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Gera hash do email
        |--------------------------------------------------------------------------
        */
        $hashEmail = hash('sha256', strtolower(trim($user->email)));

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Verifica se já foi banido
        |--------------------------------------------------------------------------
        */
        $jaBanido = DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();

        if ($jaBanido) {
            return response()->json([
                'message' => 'Usuário já está banido.'
            ], 409);
        }

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ Registra o banimento
        |--------------------------------------------------------------------------
        */
        DB::table('emails_bloqueados')->insert([
            'user_id'    => $user->id,
            'banido_por' => $admin->id,
            'hash_email' => $hashEmail,
            'motivo'     => $request->motivo,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 7️⃣ Desativa conta
        |--------------------------------------------------------------------------
        */
        $user->update([
            'ativo' => false
        ]);

        /*
        |--------------------------------------------------------------------------
        | 8️⃣ Remove tokens ativos (Sanctum)
        |--------------------------------------------------------------------------
        */
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Usuário banido com sucesso.'
        ]);
    }
}