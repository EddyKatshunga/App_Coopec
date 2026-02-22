<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agence extends Model
{
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
        'solde_actuel_epargne_usd',
    ];

    /**
     * Retourne les règles de validation
     * @param int|null $id ID de l'agence pour ignorer l'unique lors de l'update
     */
    public static function rules($id = null): array
    {
        return [
            'nom'   => "required|string|max:255|unique:agences,nom,{$id}",
            'code'  => "nullable|string|max:50|unique:agences,code,{$id}",
            'ville' => 'nullable|string|max:255',
            'pays'  => 'nullable|string|max:255',
            
            // Validation de la clé étrangère vers la table des agents
            'chef_agence_id' => 'nullable|exists:agents,id',
            
            // Les soldes ne peuvent pas être négatifs (min:0)
            'solde_actuel_coffre_cdf'  => 'nullable|numeric|min:0',
            'solde_actuel_coffre_usd'  => 'nullable|numeric|min:0',
            'solde_actuel_epargne_cdf' => 'nullable|numeric|min:0',
            'solde_actuel_epargne_usd' => 'nullable|numeric|min:0',
        ];
    }
    
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

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function creditRemboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class);
    }

    public function chefAgence(): BelongsTo
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
