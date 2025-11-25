<?php

namespace App\Providers;

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
        // Share pending organizers count with admin views
        view()->composer('admin.*', function ($view) {
            $view->with('pendingOrganizers', \App\Models\EventOrganizer::where('status', 'pending')->count());
        });
    }
}
