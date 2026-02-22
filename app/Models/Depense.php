<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use ManageClotureComptable;
    use Blameable;
    use AffectsCoffre;

    protected $fillable = [
        'montant',
        'monnaie',
        'libelle',
        'reference',
        'description',
        'types_depense_id',
        'beneficiaire_id',
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
        return 'date_operation';
    }

    public function typeDepense(): BelongsTo
    {
        return $this->belongsTo(TypesDepense::class, 'types_depense_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function beneficiaire(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'beneficiaire_id');   
    }

    public function isAddition(): bool {
        return false; // Toujours une diminution
    }

    public static function getGroupedByType(int $agenceId, $date)
    {
        return self::with('typeDepense')
            ->selectRaw('types_depense_id, monnaie, COUNT(*) as nbre_operations, SUM(montant) as total_montant')
            ->where('agence_id', $agenceId)
            ->whereDate('date_operation', $date)
            ->groupBy('types_depense_id', 'monnaie')
            ->get()
            ->groupBy('types_depense_id');
    }
}
