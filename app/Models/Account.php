<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'numero',
        'nom',
        'type',
        'parent_id',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    protected $hidden = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Le compte parent (si c'est un sous-compte).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Les sous-comptes associés.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Les lignes d'écriture comptable associées à ce compte.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function getBalance(int $agenceId, string $monnaie, ?string $date = null): float
    {
        $dailyBalance = AccountDailyBalance::getAccountDailyBalanceForDate(
            $agenceId, 
            $this, 
            $monnaie, 
            $date
        );

        return $dailyBalance ? (float) $dailyBalance->solde_fin : 0.0;
    }
}