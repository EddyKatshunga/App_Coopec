<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'date_operation',
        'libelle',
        'agence_id',
        'journee_comptable_id',
    ];

    protected $hidden = ['id'];
    
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $casts = [
        'date_operation' => 'date',
    ];

    /**
     * Les lignes de débit/crédit de cette écriture.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    /**
     * L'agence liée à cette écriture.
     */
    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }

    /**
     * La journée comptable associée (clôture).
     */
    public function journeeComptable(): BelongsTo
    {
        return $this->belongsTo(CloturesComptable::class, 'journee_comptable_id');
    }

    /* --- Relations inverses vers les tables opérationnelles --- */
    
    public function revenu(): HasOne
    {
        return $this->hasOne(Revenu::class, 'journal_entry_id');
    }

    public function depense(): HasOne
    {
        return $this->hasOne(Depense::class, 'journal_entry_id');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'journal_entry_id');
    }

    public function credit(): HasOne
    {
        return $this->hasOne(Credit::class, 'journal_entry_id');
    }

    public function remboursement(): HasOne
    {
        return $this->hasOne(CreditRemboursement::class, 'journal_entry_id');
    }

}