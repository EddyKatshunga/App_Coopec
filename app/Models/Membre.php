<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Membre extends Model
{
    use Blameable;
    
    protected $hidden = ['id'];
    
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'user_id',
        'numero_identification',
        'qualite',
        'sexe',
        'lieu_de_naissance',
        'date_de_naissance',
        'adresse',
        'telephone',
        'activites',
        'adresse_activite',
        'date_adhesion',
        'agent_parrain_id',
    ];

    protected $casts = [
        'date_de_naissance' => 'date',
        'date_adhesion' => 'date',
    ];

    protected $appends = ['nom', 'email'];

    // belongsTo: Se place dans le modèle de la table qui contient la clé étrangère.
    public function agentParrain(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_parrain_id');
    }
    
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // hasOne: Se place dans le modèle de la table qui ne contient pas la clé étrangère 
    // (elle "possède" l'autre enregistrement à distance).
    public function agent() : HasOne
    {
        return $this->hasOne(Agent::class, 'membre_id');
    }

    public function getNomAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function comptes(): HasMany
    {
        return $this->hasMany(Compte::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }
    
    public function creditEnCours()
    {
        return $this->hasOne(Credit::class)
            ->whereIn('statut', ['en_cours', 'en_retard']);
    }

    // Un helper pour clarifier le code dans Blade
    public function hasActiveCredit(): bool
    {
        return $this->creditEnCours()->exists();
    }

}
