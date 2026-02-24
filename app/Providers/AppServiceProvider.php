<?php

namespace App\Providers;

use App\Models\Credit;
use App\Models\CreditRemboursement;
use App\Models\Depense;
use App\Models\Revenu;
use App\Models\Transaction;
use App\Observers\CreditObserver;
use App\Observers\CreditRemboursementObserver;
use App\Observers\DepenseObserver;
use App\Observers\RevenuObserver;
use App\Observers\TransactionObserver;
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
        Transaction::observe(TransactionObserver::class);
        Credit::observe(CreditObserver::class);
        CreditRemboursement::observe(CreditRemboursementObserver::class);
    }
}
