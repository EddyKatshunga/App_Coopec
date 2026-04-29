<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use Blameable;

    protected $hidden = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'nom', 'code', 'gerant_id', 'agence_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS & SCOPES FILTRÉS
    |--------------------------------------------------------------------------
    */

    public function gerant(): BelongsTo { return $this->belongsTo(Agent::class, 'gerant_id'); }
    public function agence(): BelongsTo { return $this->belongsTo(Agence::class); }

    /**
     * Relation de base vers tous les crédits.
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    /**
     * Uniquement les crédits dont le dossier est encore ouvert.
     */
    public function creditsEnCours(): HasMany
    {
        return $this->hasMany(Credit::class)->enCours();
    }

    /**
     * Uniquement les crédits en retard.
     */
    public function creditsEnRetard(): HasMany
    {
        return $this->hasMany(Credit::class)->enRetard();
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYSE DU PORTEFEUILLE (Calculs par Devise)
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule les statistiques financières pour une devise donnée (uniquement sur crédits en cours).
     * Retourne un objet contenant : prêté, remboursé, reste à recouvrer.
     */
    public function getStatsPortefeuille(string $devise): object
    {
        // On utilise les colonnes réelles de la DB pour la performance (SUM SQL)
        $data = $this->creditsEnCours()
            ->devise($devise)
            ->selectRaw('
                SUM(capital + interet) as total_prete, 
                SUM(total_remboursement) as total_recupere
            ')
            ->first();

        $prete = (float) $data->total_prete;
        $recupere = (float) $data->total_recupere;

        return (object) [
            'devise'            => strtoupper($devise),
            'total_prete'       => $prete,
            'total_recupere'    => $recupere,
            'reste_a_recouvrer' => $prete - $recupere,
            'taux_recouvrement' => $prete > 0 ? round(($recupere / $prete) * 100, 2) : 0,
            'nombre_dossiers'   => $this->creditsEnCours()->devise($devise)->count(),
            'nombre_retards'    => $this->creditsEnRetard()->devise($devise)->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTS DYNAMIQUES (Helpers pour les vues)
    |--------------------------------------------------------------------------
    | Note : Ces attributs peuvent être gourmands si utilisés dans une boucle 
    | sur beaucoup de zones. Privilégiez getStatsPortefeuille() pour un rapport.
    */

    public function getStatsUsdAttribute(): object
    {
        return $this->getStatsPortefeuille('USD');
    }

    public function getStatsCdfAttribute(): object
    {
        return $this->getStatsPortefeuille('CDF');
    }

    /* ================= QUERY SCOPES ================= */

    /**
     * Filtre les zones appartenant à une agence spécifique.
     */
    public function scopeParAgence($query, $agenceId)
    {
        return $query->where('agence_id', $agenceId);
    }

    /**
     * Filtre les zones qui ont au moins un crédit en retard.
     * Utilisation : Zone::ayantDesRetards()->get();
     */
    public function scopeAyantDesRetards($query)
    {
        return $query->whereHas('credits', function ($q) {
            $q->enRetard(); // On réutilise le scope du modèle Credit
        });
    }
}