<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypesDepense extends Model
{
    use Blameable;
    
    protected $hidden = ['id'];
    
    public function getRouteKeyName()
    {
        return 'uuid';
    }

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
