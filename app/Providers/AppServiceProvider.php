<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Railway terminates TLS and forwards plain HTTP internally, so
        // Laravel must be told explicitly to generate https:// links —
        // it can't reliably detect this from the request alone.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
