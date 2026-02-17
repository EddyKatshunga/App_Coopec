<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    use VerifieClotureComptable;
    use Blameable;

    protected $fillable = [
        'nom',
        'code',
        'gerant_id',
        'agence_id',
        'created_by',
        'updated_by',
    ];

    // belongsTo: Se place dans le modèle de la table qui contient la clé étrangère.
    public function gerant(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'gerant_id');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }
}
