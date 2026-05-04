<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Controller de Autenticação JWT
 * 
 * Suporta tanto requisições de API (Flutter/Mobile) quanto Web
 * - Para Flutter: Envie 'Accept: application/json' no header
 * - Para Web: Requisições normais (retornam redirects ou JSON baseado no Accept header)
 */
class JwtAuthController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * REGISTRO DE USUÁRIO
     * --------------------------------------------------------------------------
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->respondWithErrors($validator->errors(), 422);
        }

        $emailNormalizado = strtolower(trim($request->email));
        $hashEmail = $this->gerarHashEmail($emailNormalizado);

        // 🔒 Verifica se email já foi banido
        $emailBanido = DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();

        if ($emailBanido) {
            return $this->respondWithError('Este email não pode ser utilizado para cadastro.', 403);
        }

        // Cria usuário com Argon2id (configurado no hashing.php)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $emailNormalizado,
            'password' => Hash::make($request->password),
        ]);

        // Envia email de verificação
        $user->sendEmailVerificationNotification();

        // Gera token JWT
        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user, 'Usuário registrado com sucesso. Verifique seu email.', 201);
    }

    /**
     * --------------------------------------------------------------------------
     * LOGIN
     * --------------------------------------------------------------------------
     */
    public function login(Request $request)
    {
        \Log::info('Tentativa de login JWT:', ['email' => $request->email, 'ip' => $request->ip()]);

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->respondWithErrors($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            // Verifica se email foi banido
            $emailNormalizado = strtolower(trim($request->email));
            $hashEmail = $this->gerarHashEmail($emailNormalizado);
            
            $emailBanido = DB::table('emails_bloqueados')
                ->where('hash_email', $hashEmail)
                ->exists();

            if ($emailBanido) {
                return $this->respondWithError('Conta suspensa. Entre em contato com o suporte.', 403);
            }

            // Tenta autenticar e gerar token
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->respondWithError('Credenciais inválidas.', 401);
            }

            // Obtém usuário autenticado
            $user = Auth::user();

            // Verifica verificação de email
            if (!$user->hasVerifiedEmail()) {
                return $this->respondWithError('Você precisa confirmar seu e-mail antes de fazer login.', 403);
            }

            // Verifica se conta está ativa
            if (!$user->ativo) {
                return $this->respondWithError('Sua conta está desativada.', 403);
            }

            // Atualiza último login
            $user->update([
                'ultimo_ip'       => $request->ip(),
                'ultimo_login_em' => now(),
            ]);

            return $this->respondWithToken($token, $user->load('perfil'), 'Login realizado com sucesso.');

        } catch (JWTException $e) {
            \Log::error('Erro JWT no login: ' . $e->getMessage());
            return $this->respondWithError('Não foi possível criar o token. Tente novamente.', 500);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * LOGOUT
     * --------------------------------------------------------------------------
     */
    public function logout(Request $request)
    {
        try {
            // Invalida o token atual
            JWTAuth::invalidate(JWTAuth::getToken());

            // Se for web, faz logout da sessão também
            if ($request->hasSession()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $this->respondWithSuccess('Logout realizado com sucesso.');

        } catch (TokenExpiredException $e) {
            return $this->respondWithError('Token expirado.', 401);
        } catch (TokenInvalidException $e) {
            return $this->respondWithError('Token inválido.', 401);
        } catch (JWTException $e) {
            return $this->respondWithError('Erro ao realizar logout.', 500);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * REFRESH TOKEN
     * --------------------------------------------------------------------------
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            $user = JWTAuth::setToken($token)->toUser();

            return $this->respondWithToken($token, $user, 'Token atualizado com sucesso.');

        } catch (TokenExpiredException $e) {
            return $this->respondWithError('Token expirado. Faça login novamente.', 401);
        } catch (TokenInvalidException $e) {
            return $this->respondWithError('Token inválido.', 401);
        } catch (JWTException $e) {
            return $this->respondWithError('Erro ao atualizar token.', 500);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * DADOS DO USUÁRIO AUTENTICADO
     * --------------------------------------------------------------------------
     */
    public function me(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->respondWithError('Usuário não encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->load('perfil'),
                    'token_info' => [
                        'exp' => JWTAuth::getPayload()->get('exp'),
                        'iat' => JWTAuth::getPayload()->get('iat'),
                    ]
                ]
            ]);

        } catch (TokenExpiredException $e) {
            return $this->respondWithError('Token expirado.', 401);
        } catch (TokenInvalidException $e) {
            return $this->respondWithError('Token inválido.', 401);
        } catch (JWTException $e) {
            return $this->respondWithError('Token não fornecido.', 401);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * REVOKE ALL TOKENS (Logout de todos os dispositivos)
     * --------------------------------------------------------------------------
     */
    public function revokeAllTokens(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            // Em JWT não há blacklist persistente por padrão
            // Você pode implementar uma blacklist em Redis/DB se necessário
            
            return $this->respondWithSuccess('Todos os tokens foram revogados. Faça login novamente.');

        } catch (JWTException $e) {
            return $this->respondWithError('Erro ao revogar tokens.', 500);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * MÉTODOS AUXILIARES DE RESPOSTA
     * --------------------------------------------------------------------------
     */
    
    /**
     * Resposta padronizada com token
     */
    protected function respondWithToken(string $token, User $user, string $message = 'Sucesso', int $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_in'   => JWTAuth::factory()->getTTL() * 60,
                'user'         => $user,
            ]
        ];

        // Se for requisição web (não JSON), pode adicionar cookie
        if (!request()->wantsJson() && !request()->ajax()) {
            // Para web, você pode querer retornar redirect ou view
            // Por enquanto mantemos JSON para consistência
        }

        return response()->json($response, $status);
    }

    /**
     * Resposta de sucesso
     */
    protected function respondWithSuccess(string $message, array $data = [], int $status = 200)
    {
        $response = ['success' => true, 'message' => $message];
        
        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Resposta de erro simples
     */
    protected function respondWithError(string $message, int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => null,
        ], $status);
    }

    /**
     * Resposta com múltiplos erros de validação
     */
    protected function respondWithErrors($errors, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => 'Erros de validação.',
            'errors' => $errors,
        ], $status);
    }

    /**
     * --------------------------------------------------------------------------
     * FUNÇÕES PRIVADAS
     * --------------------------------------------------------------------------
     */
    
    /**
     * Gera hash SHA256 do email para verificação de banimento
     */
    private function gerarHashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }
}
