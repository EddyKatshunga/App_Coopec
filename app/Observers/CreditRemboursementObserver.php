<?php

namespace App\Observers;

use App\Models\CreditRemboursement;

class CreditRemboursementObserver
{
    /**
     * Handle the CreditRemboursement "created" event.
     */
    public function created(CreditRemboursement $creditRemboursement): void
    {
        //
    }

    /**
     * Handle the CreditRemboursement "updated" event.
     */
    public function updated(CreditRemboursement $creditRemboursement): void
    {
        //
    }

    /**
     * Handle the CreditRemboursement "deleted" event.
     */
    public function deleted(CreditRemboursement $creditRemboursement): void
    {
        //
    }

    /**
     * Handle the CreditRemboursement "restored" event.
     */
    public function restored(CreditRemboursement $creditRemboursement): void
    {
        //
    }

    /**
     * Handle the CreditRemboursement "force deleted" event.
     */
    public function forceDeleted(CreditRemboursement $creditRemboursement): void
    {
        //
    }
}
