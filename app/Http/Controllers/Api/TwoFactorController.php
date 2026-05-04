<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

/**
 * 2FA Controller (TOTP - Time-based One-Time Password)
 * 
 * Suporta Google Authenticator, Authy, Microsoft Authenticator
 * RFC 6238 compliant
 */
class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * --------------------------------------------------------------------------
     * Inicia configuração de 2FA (gera QR Code)
     * --------------------------------------------------------------------------
     */
    public function setup(Request $request)
    {
        $user = $request->user();

        // Gera secret único
        $secret = $this->google2fa->generateSecretKey();

        // Gera URL para QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Salva como pending (não ativa até confirmar)
        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => false,
        ]);

        // Gera códigos de backup (10 códigos de uso único)
        $recoveryCodes = $this->generateRecoveryCodes();
        
        // Salva hash dos códigos de backup
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode(
                array_map(fn($code) => hash('sha256', $code), $recoveryCodes)
            )),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'secret' => $secret,
                'qr_code_url' => $qrCodeUrl,
                'recovery_codes' => $recoveryCodes, // Mostrar apenas uma vez!
                'instructions' => 'Escaneie o QR Code com Google Authenticator e digite o código para confirmar.',
            ],
            'warning' => 'Salve os códigos de backup em local seguro. Eles não serão mostrados novamente!',
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Confirma ativação do 2FA
     * --------------------------------------------------------------------------
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido.',
            ], 422);
        }

        $user = $request->user();
        $secret = decrypt($user->two_factor_secret);

        // Verifica código TOTP
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Código incorreto. Tente novamente.',
            ], 400);
        }

        // Ativa 2FA
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Autenticação de dois fatores ativada com sucesso!',
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Verifica código 2FA durante login
     * --------------------------------------------------------------------------
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA não ativado para este usuário.',
            ], 400);
        }

        $code = $request->code;

        // Verifica se é código TOTP (6 dígitos)
        if (strlen($code) === 6 && is_numeric($code)) {
            $secret = decrypt($user->two_factor_secret);
            $valid = $this->google2fa->verifyKey($secret, $code);

            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código incorreto.',
                ], 400);
            }
        } else {
            // Verifica se é código de backup (8 caracteres alfanuméricos)
            if (!$this->verifyRecoveryCode($user, $code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código de backup inválido.',
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Verificação 2FA bem-sucedida.',
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Desativa 2FA
     * --------------------------------------------------------------------------
     */
    public function disable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
            ], 422);
        }

        $user = $request->user();

        // Verifica senha
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.',
            ], 403);
        }

        // Verifica código 2FA
        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Código 2FA incorreto.',
            ], 400);
        }

        // Remove 2FA
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Autenticação de dois fatores desativada.',
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Regenera códigos de backup
     * --------------------------------------------------------------------------
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA não está ativado.',
            ], 400);
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode(
                array_map(fn($code) => hash('sha256', $code), $recoveryCodes)
            )),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'recovery_codes' => $recoveryCodes,
            ],
            'warning' => 'Salve os códigos de backup em local seguro!',
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Status do 2FA
     * --------------------------------------------------------------------------
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => $user->two_factor_enabled,
                'confirmed_at' => $user->two_factor_confirmed_at,
                'has_recovery_codes' => !empty($user->two_factor_recovery_codes),
            ],
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * MÉTODOS PRIVADOS
     * --------------------------------------------------------------------------
     */

    /**
     * Gera 10 códigos de backup de 8 caracteres
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(\Str::random(4) . '-' . \Str::random(4));
        }
        
        return $codes;
    }

    /**
     * Verifica código de backup
     */
    protected function verifyRecoveryCode(User $user, string $code): bool
    {
        $storedHashes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        $codeHash = hash('sha256', strtoupper(str_replace('-', '', $code)));
        
        if (in_array($codeHash, $storedHashes, true)) {
            // Remove código usado (one-time use)
            $storedHashes = array_diff($storedHashes, [$codeHash]);
            
            $user->update([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($storedHashes))),
            ]);
            
            return true;
        }
        
        return false;
    }
}
