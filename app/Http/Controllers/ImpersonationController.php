<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPermissions;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    use AuthorizesPermissions;

    public function store(Request $request, User $user, ImpersonationService $impersonation): RedirectResponse
    {
        $this->authorizePermission('impersonate-users');

        $admin = $request->user();

        if ($impersonation->isImpersonating()) {
            return back()->with('error', 'أنهِ جلسة المعاينة الحالية أولاً.');
        }

        if ($user->id === $admin->id) {
            return back()->with('error', 'لا يمكنك الدخول بحسابك.');
        }

        $impersonation->start($admin, $user);

        ActivityLogger::log(
            "الدخول بحساب المستخدم «{$user->name}»",
            'impersonate_start',
            'auth',
            $user,
            ['impersonator_id' => $admin->id, 'impersonator_name' => $admin->name]
        );

        return redirect()
            ->route('dashboard')
            ->with('success', "أنت الآن تعرض النظام كـ {$user->name}.");
    }

    public function destroy(ImpersonationService $impersonation): RedirectResponse
    {
        $impersonator = $impersonation->stop();

        if (! $impersonator) {
            return redirect()->route('dashboard');
        }

        ActivityLogger::log(
            'العودة إلى الحساب الأصلي بعد المعاينة',
            'impersonate_stop',
            'auth',
            $impersonator
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'عدت إلى حسابك.');
    }
}
