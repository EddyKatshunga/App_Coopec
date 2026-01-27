<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agence extends Model
{
    use Blameable;

    protected $fillable = [
        'nom',
        'ville',
        'pays',
    ];
    
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
}
