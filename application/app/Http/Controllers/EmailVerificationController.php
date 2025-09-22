<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Tools;
use App\Helpers\SecurityEmailHelper;
use App\Helpers\SecureTokenHelper;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;

class EmailVerificationController extends Controller
{
    public function verifyEmail(Request $request, $token)
    {
        $tokenData = SecureTokenHelper::validateEmailToken($token);

        if (!$tokenData) {
            Tools::logAuthEvent(LogLevel::WARNING, "Invalid or expired email verification token", [
                'token_hash' => hash('sha256', $token),
                'ip_context' => Tools::getIpContext(),
                'reason' => 'token_validation_failed'
            ]);

            return redirect(config('app.frontend_url', '/') . '/login?error=invalid_token');
        }

        $user = User::find($tokenData['user_id']);

        if (!$user) {
            Tools::logAuthEvent(LogLevel::WARNING, "User not found for valid token", [
                'user_id' => $tokenData['user_id'],
                'ip_context' => Tools::getIpContext()
            ]);

            return redirect(config('app.frontend_url', '/') . '/login?error=invalid_token');
        }

        $hashedToken = hash('sha256', $token);
        if ($user->email_verification_token !== $hashedToken) {
            Tools::logAuthEvent(LogLevel::WARNING, "Token hash mismatch", [
                'user_id' => $user->id,
                'ip_context' => Tools::getIpContext()
            ]);

            return redirect(config('app.frontend_url', '/') . '/login?error=invalid_token');
        }

        if ($user->email_verified_at) {
            return redirect(config('app.frontend_url', '/') . '/login?message=already_verified');
        }

        $user->update([
            'email_verified_at' => now(),
            'status' => User::STATUS_WAITING_ADMIN,
            'email_verification_token' => null,
        ]);

        Tools::logAuthEvent(LogLevel::INFO, "Email verified successfully", [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        SecurityEmailHelper::sendWaitingApproval($user);
        SecurityEmailHelper::sendAdminNotification($user);

        return redirect()->route('confirmed');
    }
}
