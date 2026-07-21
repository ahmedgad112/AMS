<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function store(Request $request, User $user, ImpersonationService $impersonation): RedirectResponse
    {
        $admin = $request->user();

        if ($impersonation->isImpersonating()) {
            return back()->with('error', 'أنهِ جلسة المعاينة الحالية أولاً.');
        }

        if ($user->id === $admin->id) {
            return back()->with('error', 'لا يمكنك الدخول بحسابك.');
        }

        $impersonation->start($admin, $user);

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

        return redirect()
            ->route('users.index')
            ->with('success', 'عدت إلى حسابك.');
    }
}
