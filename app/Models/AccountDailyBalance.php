<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountDailyBalance extends Model
{
    protected $table = 'account_daily_balances';

    protected $fillable = [
        'uuid',
        'account_id',
        'agence_id',
        'cloture_comptable_id',
        'monnaie',
        'solde_debut',
        'total_debit_jour',
        'total_credit_jour',
        'solde_fin',
    ];

    protected $casts = [
        'solde_debut'       => 'decimal:2',
        'total_debit_jour'  => 'decimal:2',
        'total_credit_jour' => 'decimal:2',
        'solde_fin'         => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function clotureComptable(): BelongsTo
    {
        return $this->belongsTo(CloturesComptable::class, 'cloture_comptable_id');
    }

    public static function getAccountDailyBalanceForDate(
        int $agenceId,
        Account $account,
        string $monnaie,
        ?string $date = null
    ): ?AccountDailyBalance {
        $date = $date ?? now()->toDateString();
        
        return AccountDailyBalance::where('account_id', $account->id)
            ->where('monnaie', $monnaie)
            ->whereHas('clotureComptable', function ($query) use ($date, $agenceId) {
                $query->where('agence_id', $agenceId)
                    ->whereDate('date_cloture', '<=', $date);
            })
            ->orderByDesc(
                CloturesComptable::select('date_cloture')
                    ->whereColumn('clotures_comptables.id', 'account_daily_balances.cloture_comptable_id')
                    ->limit(1)
            )
            ->first();
    }

}