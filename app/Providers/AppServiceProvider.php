<?php

namespace App\Providers;

use App\Models\Registry;
use App\Observers\AuditLogObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Registry::observe(AuditLogObserver::class);
    }
}
