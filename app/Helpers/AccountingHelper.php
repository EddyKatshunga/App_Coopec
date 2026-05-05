<?php

namespace App\Helpers;

class AccountingHelper
{
    public static function debit($accountId, $amount, $monnaie, $taux = null)
    {
        return [
            'account_id' => $accountId,
            'debit' => $amount,
            'credit' => 0,
            'monnaie' => $monnaie,
            'taux_change' => $taux,
        ];
    }

    public static function credit($accountId, $amount, $monnaie, $taux = null)
    {
        return [
            'account_id' => $accountId,
            'debit' => 0,
            'credit' => $amount,
            'monnaie' => $monnaie,
            'taux_change' => $taux,
        ];
    }
}