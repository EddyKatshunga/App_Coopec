<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenu extends Model
{
    use VerifieClotureComptable;
    use Blameable;
    use AffectsCoffre;
    
    protected $fillable = [
        'date_operation',
        'montant',
        'monnaie',
        'libelle',
        'reference',
        'description',
        'types_revenu_id',
        'agence_id',

    ];

    public function typeRevenu()
    {
        return $this->belongsTo(TypesRevenu::class, 'types_revenu_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function getAgenceIdAttribute() {
        return $this->agence?->id;
    }

}
