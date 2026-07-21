<?php

namespace App\Providers;

use App\Services\ImpersonationService;
use App\Services\PermissionSyncService;
use App\Support\PermissionCatalog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $hash = md5(implode('|', PermissionCatalog::allNames()));

        if (Cache::get('permissions.config_hash') !== $hash) {
            app(PermissionSyncService::class)->sync();
            Cache::forever('permissions.config_hash', $hash);
        }

        View::composer('*', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $impersonation = app(ImpersonationService::class);

            $view->with([
                'isImpersonating' => $impersonation->isImpersonating(),
                'impersonator' => $impersonation->impersonator(),
            ]);
        });
    }
}
