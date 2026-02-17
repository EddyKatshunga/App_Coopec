<?php

namespace App\Providers;

use App\Models\Depense;
use App\Models\Revenu;
use App\Observers\DepenseObserver;
use App\Observers\RevenuObserver;
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
        Revenu::observe(RevenuObserver::class);
        Depense::observe(DepenseObserver::class);
    }
}
