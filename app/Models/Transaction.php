<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use VerifieClotureComptable;
    Use Blameable;
    use AffectsCoffre;

    protected $fillable = [
        'compte_id',
        'agence_id',
        'agent_collecteur_id',
        'date_transaction',
        'type_transaction',
        'montant',
        'monnaie',
        'solde_avant',
        'solde_apres',
        'status',    
    ];

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id');
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }

    public function getAgenceIdAttribute() {
        return $this->agence?->id;
    }

    public function agent_collecteur()
    {
        return $this->belongsTo(Agent::class, 'agent_collecteur_id');
    }

    public function isAddition(): bool {
        return $this->type === 'DEPOT';
    }
}
