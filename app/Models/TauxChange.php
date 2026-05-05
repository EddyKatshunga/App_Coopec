<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TauxChange extends Model
{
    use Blameable;

    protected $fillable = [
        'uuid',
        'taux_achat',
        'taux_vente',
        'date_application',
        'est_actif',
        'created_by'
    ];

    protected $casts = [
        'taux_achat' => 'decimal:4',
        'taux_vente' => 'decimal:4',
        'taux_moyen' => 'decimal:4', // Champ calculé par la DB
        'date_application' => 'date',
        'est_actif' => 'boolean',
    ];

    /**
     * Récupère le dernier taux actif en vigueur
     */
    public static function actuel()
    {
        return self::where('est_actif', true)
            ->latest('date_application')
            ->first();
    }

    /**
     * Helper pour calculer un gain ou une perte de change
     * Utile lors des écritures au journal
     */
    public function calculerEcart($montant, $tauxReference)
    {
        // Logique personnalisée selon votre flux (achat ou vente)
        return $montant * ($this->taux_moyen - $tauxReference);
    }
}

