<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueRole extends Model
{
    use VerifieClotureComptable;
    use Blameable;

    protected $fillable = [
        'user_id',
        'ancien_role',
        'nouveau_role',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
