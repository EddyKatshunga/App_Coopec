<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compte extends Model
{
    use Blameable;
    
    protected $fillable = [
        'membre_id',
        'user_id',
        'numero_compte',
        'solde_cdf',
        'solde_usd',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class, 'membre_id');
    }

    public function allTransactions() : HasMany
    {
        return $this->hasMany(Transaction::class, 'compte_id');
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class, 'compte_id')
            ->where('statut', 'VALIDE');;
    }

    public function transactionsPeriode(
        ?string $dateDebut = null, 
        ?string $dateFin = null,
        string $dateColumn = 'date_transaction'
    ): HasMany {
        $query = $this->hasMany(Transaction::class, 'compte_id')->where('statut', 'VALIDE');
        
        // Définir les dates par défaut
        $dateDebut = $dateDebut ?? now()->format('Y-m-d');
        $dateFin = $dateFin ?? $dateDebut;
        
        return $query->whereBetween($dateColumn, [$dateDebut, $dateFin]);
    }

}
