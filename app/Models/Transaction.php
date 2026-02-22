<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use ManageClotureComptable;
    Use Blameable;
    use AffectsCoffre;

    protected $fillable = [
        'compte_id',
        'agent_collecteur_id',
        'type_transaction',
        'montant',
        'monnaie',
        'solde_avant',
        'solde_apres',
        'status',    
    ];

    public function journeeComptable(): BelongsTo
    {
        return $this->belongsTo(CloturesComptable::class, 'journee_comptable_id');
    }

    /**
     * Retourne la colonne de date spécifique à ce modèle.
     */
    public function getDateColumnName(): string
    {
        return 'date_transaction';
    }

    public function compte(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }

    public function agent_collecteur(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_collecteur_id');
    }

    public function isAddition(): bool {
        return $this->type === 'DEPOT';
    }

    public static function getGroupedByAgent(string $type, int $agenceId, $date)
    {
        return self::with('agent_collecteur')
            ->selectRaw('agent_collecteur_id, monnaie, COUNT(*) as nbre_operations, SUM(montant) as total_montant')
            ->where('type', $type) // 'depot' ou 'retrait'
            ->where('agence_id', $agenceId)
            ->whereDate('date_transaction', $date)
            ->groupBy('agent_collecteur_id', 'devise')
            ->get()
            ->groupBy('agent_collecteur_id'); // Permet de regrouper CDF et USD sous le même agent
    }
}
