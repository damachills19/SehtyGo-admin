<?php

namespace App\Providers;

use App\Services\SupabaseService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

        // admin-layout renders on every authenticated page, so the sidebar
        // "Pending Approvals" badge needs its count available everywhere,
        // not just on the approvals page itself — a view composer is the
        // one place to compute it once per request rather than repeating
        // this in every controller.
        View::composer('components.admin-layout', function ($view) {
            $pendingApprovalsCount = 0;
            if (auth()->check()) {
                try {
                    $supabase = app(SupabaseService::class);
                    foreach (['wc_doctors', 'wc_labs', 'wc_pharmacies', 'wc_riders'] as $table) {
                        $pendingApprovalsCount += count($supabase->select($table, [
                            'verification_status' => 'eq.pending',
                            'select' => 'id',
                        ]));
                    }
                } catch (\Throwable $e) {
                    // Badge is a nice-to-have — never break page rendering
                    // over a failed count.
                }
            }
            $view->with('pendingApprovalsCount', $pendingApprovalsCount);
        });
    }
}
