<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agent extends Model
{
    use VerifieClotureComptable;
    use Blameable;

    protected $fillable = [
        'membre_id',
        'agence_id',
    ];

    protected $appends = ['nom'];

    public function getNomAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function membre() : BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function user()
    {
        return $this->membre->user();
    }

    public function membresAmenes(): HasMany
    {
        return $this->hasMany(Membre::class);
    }

    public function depensesExecutees(): HasMany
    {
        return $this->hasMany(Depense::class, 'beneficiaire_id');
    }

    // hasOne: Se place dans le modèle de la table qui ne contient pas la clé étrangère 
    // (elle "possède" l'autre enregistrement à distance).
    public function zone_dirige() : HasOne //La zone que l'agent dirige
    {
        return $this->hasOne(Zone::class, 'gerant_id');
    }

    public function agence_dirige() : HasOne //L'agence que l'agent dirige
    {
        return $this->hasOne(Agence::class, 'chef_agence_id');
    }

    // belongsTo: Se place dans le modèle de la table qui contient la clé étrangère.
    public function agence(): BelongsTo //L'agence à laquelle appartient l'agent
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }

    public function allTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'agent_collecteur_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'agent_collecteur_id')
            ->where('statut', 'VALIDE');
    }

    public function transactionsPeriode(
        ?string $dateDebut = null, 
        ?string $dateFin = null,
        string $dateColumn = 'date_transaction'
    ): HasMany {
        $query = $this->hasMany(Transaction::class, 'agent_collecteur_id')->where('statut', 'VALIDE');
        
        // Définir les dates par défaut
        $dateDebut = $dateDebut ?? now()->format('Y-m-d');
        $dateFin = $dateFin ?? $dateDebut;
        
        return $query->whereBetween($dateColumn, [$dateDebut, $dateFin]);
    }

    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class, 'agent_id');
    }

    /*Remboursements d'un seul jour : $agent->remboursementsPeriode('2024-01-15', '2024-01-15');
        Remboursements d'ajourd'hui : $agent->remboursementsPeriode();
        Remboursements sur une période : $agent->remboursementsPeriode('2024-01-01', '2024-01-31')*/
    public function remboursementsPeriode(
        ?string $dateDebut = null, 
        ?string $dateFin = null,
        string $dateColumn = 'date_paiement'
    ): HasMany {
        $query = $this->hasMany(CreditRemboursement::class, 'agent_id');
        
        // Définir les dates par défaut
        $dateDebut = $dateDebut ?? now()->format('Y-m-d');
        $dateFin = $dateFin ?? $dateDebut;
        
        return $query->whereBetween($dateColumn, [$dateDebut, $dateFin]);
    }
}
