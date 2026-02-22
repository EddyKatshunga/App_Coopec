<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypesRevenu extends Model
{
    use Blameable;
    
    protected $fillable = [
        'nom',
        'code_comptable',
        'est_actif',
    ];

    public function revenus(): HasMany
    {
        return $this->hasMany(Revenu::class, 'types_revenus_id');
    }
}
