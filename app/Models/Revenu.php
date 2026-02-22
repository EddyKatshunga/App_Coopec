<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenu extends Model
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
        'types_revenu_id',
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

    public function typeRevenu()
    {
        return $this->belongsTo(TypesRevenu::class, 'types_revenu_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public static function getGroupedByType(int $agenceId, $date)
    {
        return self::with('typeRevenu')
            ->selectRaw('types_revenu_id, monnaie, COUNT(*) as nbre_operations, SUM(montant) as total_montant')
            ->where('agence_id', $agenceId)
            ->whereDate('date_operation', $date)
            ->groupBy('types_revenu_id', 'monnaie')
            ->get()
            ->groupBy('types_revenu_id');
    }

}
