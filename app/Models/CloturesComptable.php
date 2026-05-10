<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CloturesComptable extends Model
{
    use Blameable;

    protected $hidden = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'agence_id',
        'date_cloture',
        'statut',
        'observation_cloture',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_cloture' => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Relations
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'journee_comptable_id');
    }

    // Relation inverse vers AccountDailyBalance
    public function accountDailyBalances(): HasMany
    {
        return $this->hasMany(AccountDailyBalance::class, 'cloture_comptable_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'journee_comptable_id');
    }

    public function depots(): HasMany
    {
        return $this->hasMany(Transaction::class, 'journee_comptable_id')->where('type_transaction', 'DEPOT');
    }

    public function retraits(): HasMany
    {
        return $this->hasMany(Transaction::class, 'journee_comptable_id')->where('type_transaction', 'RETRAIT');;
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class, 'journee_comptable_id');
    }

    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class, 'journee_comptable_id');
    }

    // Méthodes d'état
    public function isOuverte(): bool
    {
        return $this->statut === 'ouverte';
    }

    public function isCloturee(): bool
    {
        return $this->statut === 'cloturee';
    }

    public function isVerrouillee(): bool
    {
        return $this->statut === 'verouillee';
    }
}