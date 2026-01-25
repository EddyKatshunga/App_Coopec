<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compte extends Model
{
    use Blameable;
    
    protected $fillable = [
        'membre_id',
        'numero_compte',
        'solde_cdf',
        'solde_usd',
    ];

    protected $appends = ['nom'];

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class, 'membre_id');
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class, 'compte_id');
    }

    public function getNomAttribute(): ?string
    {
        return $this->membre?->nom;
    }
}
