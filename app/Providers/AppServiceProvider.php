<?php

namespace App\Providers;

use App\Models\LoginLog;
use App\Models\LogoutLog;
use App\Observers\LoginLogObserver;
use App\Observers\LogoutLogObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        LoginLog::observe(LoginLogObserver::class);
        LogoutLog::observe(LogoutLogObserver::class);
    }

    public function register()
    {
        //
    }
}
