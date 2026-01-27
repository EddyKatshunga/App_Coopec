<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agent extends Model
{
    use Blameable;

    protected $fillable = [
        'membre_id',
        'agence_id',
        'created_by',
        'updated_by',
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

    // hasOne: Se place dans le modèle de la table qui ne contient pas la clé étrangère 
    // (elle "possède" l'autre enregistrement à distance).
    public function zone() : HasOne
    {
        return $this->hasOne(Zone::class, 'gerant_id');
    }

    // belongsTo: Se place dans le modèle de la table qui contient la clé étrangère.
    public function agence(): BelongsTo
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
