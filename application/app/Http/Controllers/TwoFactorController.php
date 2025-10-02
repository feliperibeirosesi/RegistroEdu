<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Utils\Tools;
use App\Services\JWTService;
use PragmaRX\Google2FA\Google2FA;
use Psr\Log\LogLevel;

class TwoFactorController extends Controller
{
    private JWTService $jwtService;
    private Google2FA $google2fa;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
        $this->google2fa = new Google2FA();
    }

    public function generate(Request $request)
    {
        try {
            $payload = $request->get('jwt_payload');

            if (!$payload || !isset($payload['user_id'])) {
                return Tools::error('Usuário não autenticado', 401);
            }

            $user = User::where('id', $payload['user_id'])->first();

            if (!$user) {
                return Tools::error('Usuário não encontrado', 404);
            }

            if ($user->two_factor_enabled && $user->two_factor_verified) {
                Tools::logAuthEvent(LogLevel::INFO, "2FA already configured", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('2FA já está configurado e verificado', 400);
            }

            $secret = $this->google2fa->generateSecretKey();

            $user->update([
                'two_factor_secret' => $secret,
                'two_factor_enabled' => true,
                'two_factor_verified' => false
            ]);

            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                config('app.name', 'RegistroEdu'),
                $user->email,
                $secret
            );

            Tools::logAuthEvent(LogLevel::INFO, "2FA generated for user", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_context' => Tools::getIpContext()
            ]);

            return response()->json([
                'status'  => 'pending_2fa',
                'qr_code_url' => $qrCodeUrl,
                'secret'  => $secret,
                'message' => 'Escaneie o QR Code no seu aplicativo autenticador'
            ]);
        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, "2FA generation error", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);
            return Tools::error('Erro ao gerar 2FA', 500, [
                'code' => '2FA_GENERATION_ERROR'
            ]);
        }
    }

    public function verify(Request $request)
    {
        try {
            $payload = $request->get('jwt_payload');

            if (!$payload || !isset($payload['user_id'])) {
                Tools::logAuthEvent(LogLevel::WARNING, "2FA verification blocked - User not authenticated", [
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('Usuário não autenticado', 401);
            }

            $user = User::where('id', $payload['user_id'])->first();

            if (!$user) {
                Tools::logAuthEvent(LogLevel::WARNING, "2FA verification blocked - User not found", [
                    'user_id' => $payload['user_id'],
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('Usuário não encontrado', 404);
            }

            if (!$user->two_factor_enabled || !$user->two_factor_secret) {
                Tools::logAuthEvent(LogLevel::WARNING, "2FA verification blocked - Not configured", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('2FA não configurado para este usuário', 400);
            }

            $request->validate([
                'code' => 'required|string|size:6'
            ]);

            $code = $request->input('code');
            $valid = $this->google2fa->verifyKey($user->two_factor_secret, $code);

            if ($valid) {
                $user->update([
                    'two_factor_verified' => true,
                    'status' => User::STATUS_WAITING_ADMIN
                ]);

                Tools::logAuthEvent(LogLevel::INFO, "2FA verified successfully", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_context' => Tools::getIpContext()
                ]);

                return response()->json([
                    'status' => '2fa_verified',
                    'message' => '2FA ativado com sucesso. Aguarde aprovação do administrador.',
                    'user_status' => $user->status
                ]);
            }

            Tools::logAuthEvent(LogLevel::WARNING, "2FA verification failed", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_context' => Tools::getIpContext()
            ]);

            return Tools::error('Código inválido', 422, [
                'code' => 'INVALID_2FA_CODE'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Tools::validationError('Código deve conter 6 dígitos', $e->errors());
        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, "2FA verification error", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);
            return Tools::error('Erro ao verificar 2FA', 500, [
                'code' => '2FA_VERIFICATION_ERROR'
            ]);
        }
    }

    public function status(Request $request)
    {
        try {
            $payload = $request->get('jwt_payload');

            if (!$payload || !isset($payload['user_id'])) {
                return Tools::error('Usuário não autenticado', 401);
            }

            $user = User::where('id', $payload['user_id'])->first();

            if (!$user) {
                return Tools::error('Usuário não encontrado', 404);
            }

            $status = $this->determine2FAStatus($user);

            Tools::logAuthEvent(LogLevel::INFO, "2FA status checked", [
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => $status,
                'ip_context' => Tools::getIpContext()
            ]);

            return response()->json([
                'status' => $status,
                'two_factor_enabled' => $user->two_factor_enabled,
                'two_factor_verified' => $user->two_factor_verified
            ]);
        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, "2FA status check error", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);
            return Tools::error('Erro ao verificar status do 2FA', 500, [
                'code' => '2FA_STATUS_ERROR'
            ]);
        }
    }

    public function disable(Request $request)
    {
        try {
            $payload = $request->get('jwt_payload');

            if (!$payload || !isset($payload['user_id'])) {
                Tools::logAuthEvent(LogLevel::WARNING, "2FA disable blocked - User not authenticated", [
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('Usuário não autenticado', 401);
            }

            $user = User::where('id', $payload['user_id'])->first();

            if (!$user) {
                Tools::logAuthEvent(LogLevel::WARNING, "2FA disable blocked - User not found", [
                    'user_id' => $payload['user_id'],
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('Usuário não encontrado', 404);
            }

            if (!$user->two_factor_enabled) {
                return Tools::error('2FA não está ativado', 400);
            }

            $request->validate([
                'code' => 'required|string|size:6'
            ]);

            $code = $request->input('code');
            $valid = $this->google2fa->verifyKey($user->two_factor_secret, $code);

            if (!$valid) {
                Tools::logAuthEvent(LogLevel::WARNING, "2FA disable failed - Invalid code", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_context' => Tools::getIpContext()
                ]);
                return Tools::error('Código inválido', 422, [
                    'code' => 'INVALID_2FA_CODE'
                ]);
            }

            $user->update([
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
                'two_factor_verified' => false
            ]);

            Tools::logAuthEvent(LogLevel::INFO, "2FA disabled", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_context' => Tools::getIpContext()
            ]);

            return response()->json([
                'status' => '2fa_disabled',
                'message' => '2FA desativado com sucesso'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Tools::validationError('Código deve conter 6 dígitos', $e->errors());
        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, "2FA disable error", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);
            return Tools::error('Erro ao desativar 2FA', 500, [
                'code' => '2FA_DISABLE_ERROR'
            ]);
        }
    }

    private function determine2FAStatus(User $user): string
    {
        if ($user->two_factor_verified) {
            return 'verified';
        }

        if ($user->two_factor_enabled) {
            return 'pending_2fa';
        }

        return 'not_enabled';
    }
}
