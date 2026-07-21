<?php

namespace App\Http\Controllers\Concerns;

trait AuthorizesPermissions
{
    protected function authorizePermission(string $permission): void
    {
        abort_unless(auth()->user()?->can($permission), 403);
    }
}
