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
use App\Models\RefreshToken;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Traits\SanitizesOutput;

/**
 * Controller de Autenticação JWT
 * 
 * Suporta tanto requisições de API (Flutter/Mobile) quanto Web
 * - Para Flutter: Envie 'Accept: application/json' no header
 * - Para Web: Requisições normais (retornam redirects ou JSON baseado no Accept header)
 */
class JwtAuthController extends Controller
{
    use SanitizesOutput;

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
        $emailBanido = DB::table('blocked_emails')
            ->where('email_hash', $hashEmail)
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
            
            $emailBanido = DB::table('blocked_emails')
                ->where('email_hash', $hashEmail)
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

            // 🔒 Gera novo token JWT com token_version embutido
            // Isso garante que ao rodar migrate:fresh, os tokens antigos são invalidados
            // pois o token_version do usuário recriado será diferente do que estava no token
            $token = JWTAuth::claims([
                'token_version' => (int) $user->token_version,
            ])->fromUser($user);

            // Gerar Refresh Token persistente para segurança extra
            $refreshData = RefreshToken::generate(
                $user, 
                ['*'], 
                $request->ip(), 
                $request->userAgent()
            );

            return $this->respondWithToken($token, $user->load('perfil'), 'Login realizado com sucesso.')
                ->withCookie(cookie('refresh_token', $refreshData['refresh_token'], 43200, null, null, true, true)); 
                // 43200 min = 30 dias, HttpOnly, Secure

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

            // Revoga Refresh Tokens se existirem
            if ($user = Auth::user()) {
                RefreshToken::revokeAllForUser($user->id);
            }

            // Se for web, faz logout da sessão também
            if ($request->hasSession()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $this->respondWithSuccess('Logout realizado com sucesso.')
                ->withoutCookie('refresh_token');

        } catch (TokenExpiredException $e) {
            return $this->respondWithError('Token expirado.', 401)
                ->withoutCookie('refresh_token');
        } catch (TokenInvalidException $e) {
            return $this->respondWithError('Token inválido.', 401)
                ->withoutCookie('refresh_token');
        } catch (JWTException $e) {
            return $this->respondWithError('Erro ao realizar logout.', 500)
                ->withoutCookie('refresh_token');
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
            // Tenta obter do cookie para maior segurança (evita localStorage)
            $refreshToken = request()->cookie('refresh_token');
            
            if (!$refreshToken) {
                return $this->respondWithError('Refresh token não encontrado.', 401);
            }

            $tokenModel = RefreshToken::validate($refreshToken);
            
            if (!$tokenModel) {
                return $this->respondWithError('Refresh token inválido ou expirado.', 401);
            }

            // Rotaciona o refresh token (impede reuso de tokens antigos)
            $newData = $tokenModel->rotate([], request()->ip(), request()->userAgent());
            
            // Gera novo Access Token (JWT) com token_version
            $user = $tokenModel->user;
            $newAccessToken = JWTAuth::claims([
                'token_version' => (int) $user->token_version,
            ])->fromUser($user);

            return $this->respondWithToken($newAccessToken, $user, 'Token atualizado com sucesso.')
                ->withCookie(cookie(
                    'refresh_token', 
                    $newData['refresh_token'], 
                    43200, 
                    null, 
                    null, 
                    true, 
                    true
                ));

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

            return $this->safeJsonResponse([
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
     * VERIFICAÇÃO DE E-MAIL
     * --------------------------------------------------------------------------
     */

    /**
     * Verifica o e-mail do usuário via link assinado
     */
    public function verify(Request $request)
    {
        // Verifica se a assinatura do link é válida
        if (!$request->hasValidSignature()) {
            // Em vez de erro JSON, redireciona para uma página de erro ou login com erro
            return redirect(config('app.url') . '/login?error=invalid_signature');
        }

        $user = User::findOrFail($request->id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            
            // Log de verificação
            \Log::info("E-mail verificado para o usuário: {$user->email}");
        }

        // Redireciona para o login com mensagem de sucesso
        return redirect(config('app.url') . '/login?verified=1');
    }

    /**
     * Reenvia o e-mail de verificação
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->respondWithError('Este e-mail já foi verificado.', 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->respondWithSuccess('E-mail de verificação reenviado com sucesso.');
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
            
            // 🔒 Incrementa token_version para invalidar todos os JWTs ativos
            // Qualquer token com token_version anterior será rejeitado pelo HybridAuth
            $user->increment('token_version');
            
            // Revoga todos os refresh tokens do usuário
            RefreshToken::revokeAllForUser($user->id);
            
            // Invalida o token atual
            JWTAuth::invalidate(JWTAuth::getToken());
            
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

        return $this->safeJsonResponse($response, $status);
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

        return $this->safeJsonResponse($response, $status);
    }

    /**
     * Resposta de erro simples
     */
    protected function respondWithError(string $message, int $status = 400)
    {
        return $this->safeJsonResponse([
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
        return $this->safeJsonResponse([
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
