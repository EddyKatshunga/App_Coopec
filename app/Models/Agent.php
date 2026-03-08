<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Agent extends Model
{
    use Blameable;
    
    protected $hidden = ['id'];
    
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'membre_id',
        'user_id',
        'agence_id',
    ];

    public function getNomAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function membre() : BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membresAmenes(): HasMany
    {
        return $this->hasMany(Membre::class, 'agent_parrain_id');
    }

    public function depensesExecutees(): HasMany
    {
        return $this->hasMany(Depense::class, 'beneficiaire_id');
    }

    public function credits(): HasMany //Les crédits validés par l'agent
    {
        return $this->hasMany(Credit::class);
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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'agent_collecteur_id');
    }

    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class, 'agent_id');
    }

}
