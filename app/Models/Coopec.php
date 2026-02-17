<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coopec extends Model
{

    public function agences()
    {
        return Agence::all();
    }

    public function agents()
    {
        return Agent::all();
    }
}
