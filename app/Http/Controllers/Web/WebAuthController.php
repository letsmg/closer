<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Controller de Autenticação Web (Session-based)
 * 
 * Para uso exclusivo em aplicações web Laravel tradicionais
 * Usa sessões PHP/Laravel para manter estado de autenticação
 */
class WebAuthController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * EXIBE FORMULÁRIO DE LOGIN
     * --------------------------------------------------------------------------
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * --------------------------------------------------------------------------
     * EXIBE FORMULÁRIO DE REGISTRO
     * --------------------------------------------------------------------------
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * --------------------------------------------------------------------------
     * PROCESSA LOGIN WEB
     * --------------------------------------------------------------------------
     */
    public function login(Request $request)
    {
        \Log::info('Tentativa de login Web:', ['email' => $request->email, 'ip' => $request->ip()]);

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Verifica se email foi banido
        $emailNormalizado = strtolower(trim($request->email));
        $hashEmail = $this->gerarHashEmail($emailNormalizado);
        
        $emailBanido = DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();

        if ($emailBanido) {
            return redirect()->back()
                ->with('error', 'Conta suspensa. Entre em contato com o suporte.');
        }

        // Tenta autenticar
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return redirect()->back()
                ->with('error', 'Credenciais inválidas.')
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Verifica verificação de email
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->back()
                ->with('warning', 'Você precisa confirmar seu e-mail antes de fazer login. Verifique sua caixa de entrada.');
        }

        // Verifica se conta está ativa
        if (!$user->ativo) {
            Auth::logout();
            return redirect()->back()
                ->with('error', 'Sua conta está desativada. Entre em contato com o suporte.');
        }

        // Atualiza último login
        $user->update([
            'ultimo_ip'       => $request->ip(),
            'ultimo_login_em' => now(),
        ]);

        \Log::info('Login Web bem-sucedido:', ['user_id' => $user->id, 'email' => $user->email]);

        // Redireciona baseado no nível do usuário
        return $this->redirectBasedOnRole($user);
    }

    /**
     * --------------------------------------------------------------------------
     * PROCESSA REGISTRO WEB
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $emailNormalizado = strtolower(trim($request->email));
        $hashEmail = $this->gerarHashEmail($emailNormalizado);

        // Verifica se email foi banido
        $emailBanido = DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();

        if ($emailBanido) {
            return redirect()->back()
                ->with('error', 'Este email não pode ser utilizado para cadastro.');
        }

        try {
            // Cria usuário com Argon2id
            $user = User::create([
                'name'     => $request->name,
                'email'    => $emailNormalizado,
                'password' => Hash::make($request->password),
            ]);

            // Envia email de verificação
            $user->sendEmailVerificationNotification();

            \Log::info('Registro Web bem-sucedido:', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->route('login')
                ->with('success', 'Conta criada com sucesso! Verifique seu email para ativar sua conta.');

        } catch (\Exception $e) {
            \Log::error('Erro no registro Web: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao criar conta. Tente novamente.')
                ->withInput($request->except('password'));
        }
    }

    /**
     * --------------------------------------------------------------------------
     * LOGOUT WEB
     * --------------------------------------------------------------------------
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Você saiu da sua conta com sucesso.');
    }

    /**
     * --------------------------------------------------------------------------
     * REDIRECIONAMENTO BASEADO NO PAPEL DO USUÁRIO
     * --------------------------------------------------------------------------
     */
    private function redirectBasedOnRole($user)
    {
        // Admin (nível 3)
        if ($user->nivel === 3) {
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Bem-vindo(a), Administrador!');
        }

        // Usuários padrão
        return redirect()->intended('/dashboard')
            ->with('success', 'Login realizado com sucesso! Bem-vindo(a), ' . $user->name);
    }

    /**
     * --------------------------------------------------------------------------
     * FUNÇÃO PRIVADA
     * --------------------------------------------------------------------------
     */
    
    /**
     * Gera hash SHA256 do email
     */
    private function gerarHashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }
}
