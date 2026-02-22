<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;

class AgenceDirectionHistory extends Model
{
    use Blameable;

    protected $fillable = [
        'agence_id',
        'ancien_directeur_id',
        'nouveau_directeur_id',
    ];
}
