<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\Tools;
use App\Models\User;
use App\Helpers\SecurityEmailHelper;
use Psr\Log\LogLevel;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function resendEmailVerification(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return Tools::error('Usuário não autenticado.', 401, [
                    'code' => 'UNAUTHORIZED'
                ]);
            }

            if ($user->isApproved()) {
                return Tools::error('Esta conta já está verificada e ativa.', 400, [
                    'code' => 'ALREADY_VERIFIED'
                ]);
            }

            if ($user->status === User::STATUS_REJECTED) {
                return Tools::error('Esta conta foi rejeitada. Entre em contato com o suporte.', 403, [
                    'code' => 'ACCOUNT_REJECTED'
                ]);
            }

            if ($user->status === User::STATUS_PENDING_EMAIL) {
                SecurityEmailHelper::sendEmailVerification($user);

                Tools::logAuthEvent(LogLevel::INFO, "Email verification resent", [
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'ip_context' => Tools::getIpContext()
                ]);

                return Tools::success('Email de verificação reenviado com sucesso.');
            }

            return Tools::error('Seu email já foi verificado. Aguarde a aprovação do administrador.', 400, [
                'code' => 'EMAIL_ALREADY_VERIFIED'
            ]);

        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, "Error resending email verification", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);

            return Tools::error('Erro interno do servidor. Tente novamente mais tarde.', 500);
        }
    }

    public function checkAccountStatus(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return Tools::error('Usuário não autenticado.', 401, [
                    'code' => 'UNAUTHORIZED'
                ]);
            }

            $statusInfo = $this->getStatusInfo($user);

            Tools::logAuthEvent(LogLevel::INFO, "Account status checked", [
                'email' => $user->email,
                'user_id' => $user->id,
                'status' => $user->status,
                'ip_context' => Tools::getIpContext()
            ]);

            return Tools::success('Status da conta recuperado com sucesso.', [
                'status_info' => $statusInfo
            ]);

        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, "Error checking account status", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);

            return Tools::error('Erro interno do servidor. Tente novamente mais tarde.', 500);
        }
    }

    private function getStatusInfo(User $user): array
    {
        return match($user->status) {
            User::STATUS_PENDING_EMAIL => [
                'status' => 'pending_email_verification',
            ],
            User::STATUS_WAITING_ADMIN => [
                'status' => 'waiting_admin_approval',
            ],
            User::STATUS_REJECTED => [
                'status' => 'rejected',
            ],
            User::STATUS_ACTIVE => [
                'status' => 'approved',
            ],
            default => [
                'status' => 'unknown',
            ]
        };
    }

    public function manageAccount(Request $request)
    {
        try {
            $request->validate([
                'action' => ['required', Rule::in(['check_status', 'resend_email'])]
            ]);

            $action = $request->input('action');

            if ($action === 'resend_email') {
                return $this->resendEmailVerification($request);
            }

            return $this->checkAccountStatus($request);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return Tools::validationError($e->errors());
        }
    }
}
