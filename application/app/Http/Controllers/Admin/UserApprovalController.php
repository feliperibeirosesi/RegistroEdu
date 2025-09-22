<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\SecurityEmailHelper;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;

class UserApprovalController extends Controller
{
    public function approve(Request $request, User $user)
    {
        if ($user->status !== User::STATUS_WAITING_ADMIN) {
            return Tools::error('Usuário não está aguardando aprovação', 400);
        }

        $admin = auth()->user();

        $user->update([
            'status' => User::STATUS_APPROVED,
            'admin_approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        SecurityEmailHelper::sendApprovalNotification($user, $admin);

        Tools::logAuthEvent(LogLevel::INFO, "User approved by admin", [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'email' => $user->email
        ]);

        return Tools::success('Usuário aprovado com sucesso');
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if ($user->status !== User::STATUS_WAITING_ADMIN) {
            return Tools::error('Usuário não está aguardando aprovação', 400);
        }

        $admin = auth()->user();

        $user->update([
            'status' => User::STATUS_REJECTED,
            'rejection_reason' => $request->reason,
            'approved_by' => $admin->id,
        ]);

        SecurityEmailHelper::sendRejectionNotification($user, $admin, $request->reason);

        Tools::logAuthEvent(LogLevel::INFO, "User rejected by admin", [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'email' => $user->email,
            'reason' => $request->reason
        ]);

        return Tools::success('Usuário rejeitado');
    }
}
