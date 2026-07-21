<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationService
{
    public const SESSION_KEY = 'impersonator_id';

    public function isImpersonating(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    public function impersonator(): ?User
    {
        if (! $this->isImpersonating()) {
            return null;
        }

        return User::find(session(self::SESSION_KEY));
    }

    public function start(User $admin, User $target): void
    {
        session([self::SESSION_KEY => $admin->id]);
        Auth::login($target);
    }

    public function stop(): ?User
    {
        $impersonatorId = session()->pull(self::SESSION_KEY);

        if (! $impersonatorId) {
            return null;
        }

        $impersonator = User::find($impersonatorId);

        if ($impersonator) {
            Auth::login($impersonator);
        }

        return $impersonator;
    }
}
