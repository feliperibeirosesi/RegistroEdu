<?php

namespace App\Helpers;

use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use App\Utils\Tools;

class SecurityEmailHelper
{
    private static function dispatchOrSendNow($job)
    {
        SendEmailJob::dispatch(
            $job->toEmail,
            $job->subject,
            $job->template,
            $job->data,
            $job->fromEmail,
            $job->fromName
        );
    }

    public static function sendEmailVerification(User $user)
    {
        $tokenData = SecureTokenHelper::generateEmailToken($user);

        $user->update(['email_verification_token' => $tokenData['hashed_token']]);

        $data = [
            'name' => $user->name,
            'verification_url' => url("/auth/verify-email/" . urlencode($tokenData['token'])),
            'expires_in' => 24,
        ];

        SendEmailJob::dispatch(
            $user->email,
            'Confirme seu email - RegistroEdu',
            'emails.security.email-verification',
            $data
        );
    }

    public static function sendWaitingApproval(User $user)
    {
        $data = [
            'name' => $user->name,
            'estimated_time' => '2-3 dias úteis',
        ];

        $job = new SendEmailJob(
            $user->email,
            'Aguardando aprovação - RegistroEdu',
            'emails.security.waiting-approval',
            $data
        );

        self::dispatchOrSendNow($job);
    }

    public static function sendAdminNotification(User $newUser)
    {
        $admins = User::where('role', 'admin')->get();

        $data = [
            'user_name' => $newUser->name,
            'user_email' => $newUser->email,
            'user_domain' => substr(strrchr($newUser->email, "@"), 1),
            'registration_date' => $newUser->created_at->format('d/m/Y H:i'),
            'review_url' => url("/admin/users/{$newUser->id}/review"),
            'approve_url' => url("/admin/users/{$newUser->id}/approve"),
            'reject_url' => url("/admin/users/{$newUser->id}/reject"),
        ];

        foreach ($admins as $admin) {
            $job = new SendEmailJob(
                $admin->email,
                'Nova solicitação de registro - RegistroEdu',
                'emails.admin.new-registration',
                $data
            );
            self::dispatchOrSendNow($job);
        }
    }

    public static function sendApprovalNotification(User $user, User $admin)
    {
        $data = [
            'name' => $user->name,
            'approved_by' => $admin->name,
            'approved_at' => now()->format('d/m/Y H:i'),
            'login_url' => config('app.frontend_url') . '/login',
        ];

        $job = new SendEmailJob(
            $user->email,
            'Registro aprovado! Bem-vindo - RegistroEdu',
            'emails.security.registration-approved',
            $data
        );

        self::dispatchOrSendNow($job);
    }

    public static function sendRejectionNotification(User $user, User $admin, string $reason)
    {
        $data = [
            'name' => $user->name,
            'rejected_by' => $admin->name,
            'rejected_at' => now()->format('d/m/Y H:i'),
            'reason' => $reason,
            'contact_email' => 'suporte@registroedu.com',
        ];

        $job = new SendEmailJob(
            $user->email,
            'Registro não aprovado - RegistroEdu',
            'emails.security.registration-rejected',
            $data
        );

        self::dispatchOrSendNow($job);
    }
}
