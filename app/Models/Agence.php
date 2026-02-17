<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agence extends Model
{
    use VerifieClotureComptable;
    use Blameable;

    protected $fillable = [
        'nom',
        'ville',
        'code',
        'pays',
        'chef_agence_id',
        'solde_actuel_coffre_cdf',
        'solde_actuel_coffre_usd',
        'solde_actuel_epargne_cdf',
        'solde_actuel_epargne_cdf',
    ];
    
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('statut', 'VALIDE');
    }

    public function allTransactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactionsPeriode(
        ?string $dateDebut = null, 
        ?string $dateFin = null,
        string $dateColumn = 'date_transaction'
    ): HasMany {
        $query = $this->hasMany(Transaction::class)->where('statut', 'VALIDE');
        
        // Définir les dates par défaut
        $dateDebut = $dateDebut ?? now()->format('Y-m-d');
        $dateFin = $dateFin ?? $dateDebut;
        
        return $query->whereBetween($dateColumn, [$dateDebut, $dateFin]);
    }

    public function allTransactionsPeriode(
        ?string $dateDebut = null, 
        ?string $dateFin = null,
        string $dateColumn = 'date_transaction'
    ): HasMany {
        $query = $this->hasMany(Transaction::class);
        
        // Définir les dates par défaut
        $dateDebut = $dateDebut ?? now()->format('Y-m-d');
        $dateFin = $dateFin ?? $dateDebut;
        
        return $query->whereBetween($dateColumn, [$dateDebut, $dateFin]);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function credits()
    {
        return $this->hasManyThrough(Credit::class, Zone::class);
    }

    public function chefAgence() : BelongsTo
    {
        return $this->belongsTo(Agent::class, 'chef_agence_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function revenus(): HasMany
    {
        return $this->hasMany(Revenu::class);
    }
}
