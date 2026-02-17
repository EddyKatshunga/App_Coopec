<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
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
        'types_depense_id',
        'beneficiaire_id',
        'agence_id',
    ];

    public function typeDepense()
    {
        return $this->belongsTo(TypesDepense::class, 'types_depense_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function getAgenceIdAttribute() {
        return $this->agence?->id;
    }

    public function beneficiaire(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'beneficiaire_id');   
    }

    public function isAddition(): bool {
        return false; // Toujours une diminution
    }
}
