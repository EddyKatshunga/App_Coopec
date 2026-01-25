<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    use Blameable;

    protected $fillable = [
        'nom',
        'code',
        'gerant_id',
        'created_by',
        'updated_by',
    ];

    // belongsTo: Se place dans le modèle de la table qui contient la clé étrangère.
    public function gerant(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'gerant_id');
    }
}
