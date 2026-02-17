<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypesDepense extends Model
{
    use VerifieClotureComptable, Blameable;

    protected $fillable = [
        'nom',
        'code_comptable',
        'est_actif',
    ];

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'types_depenses_id');
    }
}
