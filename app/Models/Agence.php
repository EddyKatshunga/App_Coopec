<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agence extends Model
{
    use Blameable;

    protected $hidden = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'nom',
        'ville',
        'code',
        'pays',
        'chef_agence_id',
        'solde_actuel_coffre_cdf',
        'solde_actuel_coffre_usd',
        'solde_actuel_epargne_cdf',
        'solde_actuel_epargne_usd',
    ];

    /**
     * Retourne les règles de validation
     * @param int|null $id ID de l'agence pour ignorer l'unique lors de l'update
     */
    public static function rules($id = null): array
    {
        return [
            'nom'   => "required|string|max:255|unique:agences,nom,{$id}",
            'code'  => "nullable|string|max:50|unique:agences,code,{$id}",
            'ville' => 'nullable|string|max:255',
            'pays'  => 'nullable|string|max:255',
        ];
    }
    
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

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function creditRemboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class);
    }

    public function chefAgence(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'chef_agence_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function revenus(): HasMany
    {
        return $this->hasMany(Revenu::class);
    }

    public function cloturesComptables(): HasMany
    {
        return $this->hasMany(CloturesComptable::class);
    }

    public function journeeOuverte() : ?CloturesComptable
    {
        return $this->cloturesComptables()->where('statut', 'ouverte')->first();
    }

    /* ================= RELATIONS FILTRÉES ================= */

    /**
     * Accès direct aux crédits en cours de toutes les zones de l'agence.
     */
    public function creditsEnCours(): HasMany
    {
        return $this->hasMany(Credit::class)->enCours();
    }

    /**
     * Accès direct aux crédits en retard de l'agence.
     */
    public function creditsEnRetard(): HasMany
    {
        return $this->hasMany(Credit::class)->enRetard();
    }

    /* ================= CONSOLIDATION FINANCIÈRE ================= */

    /**
     * Consolidation de toutes les zones pour une devise.
     * Répond à : "Quel est l'état global de mon agence ?"
     */
    public function getBilanCredits(string $devise): object
    {
        $stats = $this->creditsEnCours()
            ->devise($devise)
            ->selectRaw('
                SUM(capital + interet) as total_dehors,
                SUM(total_remboursement) as total_encaisse
            ')
            ->first();

        $totalDehors = (float) $stats->total_dehors;
        $totalEncaisse = (float) $stats->total_encaisse;

        return (object) [
            'capital_interet_total' => $totalDehors,
            'montant_recupere'      => $totalEncaisse,
            'reste_a_recouvrer'     => $totalDehors - $totalEncaisse,
            'nombre_credits_actifs' => $this->creditsEnCours()->devise($devise)->count(),
            'nombre_retards'        => $this->creditsEnRetard()->devise($devise)->count(),
        ];
    }
}
