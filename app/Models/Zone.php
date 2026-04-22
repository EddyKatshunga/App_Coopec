<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Zone extends Model
{
    use Blameable;

    protected $hidden = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'nom',
        'code',
        'gerant_id',
        'agence_id',
        'nbre_credits_actifs',
        'capital_actif_cdf',
        'interet_actif_cdf',
        'encours_actif_cdf',
        'rembourse_cdf',
        'credits_retard_cdf',
        'penalites_cdf',
        'capital_actif_usd',
        'interet_actif_usd',
        'encours_actif_usd',
        'rembourse_usd',
        'credits_retard_usd',
        'penalites_usd',
        'derniere_activite_at',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    public function gerant(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'gerant_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class);
    }

    // =========================================================
    // CONDITIONS SQL POUR STATUT CALCULÉ
    // =========================================================

    /**
     * Sous-requête pour le montant total remboursé (capital + intérêt)
     */
    protected function subQueryTotalRembourse()
    {
        return "(SELECT COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0)
                 FROM credit_remboursements
                 WHERE credit_remboursements.credit_id = credits.id)";
    }

    /**
     * Condition pour les crédits actifs (en_cours + en_retard)
     */
    protected function conditionCreditsActifs()
    {
        $subQuery = $this->subQueryTotalRembourse();
        $today = now()->format('Y-m-d');

        // Actif = pas encore totalement remboursé ET non clôturé forcé
        return function ($query) use ($subQuery, $today) {
            $query->whereRaw("{$subQuery} < (capital + interet)")
                  ->whereNull('date_cloture_forcee');
        };
    }

    /**
     * Condition pour les crédits en retard parmi les actifs
     */
    protected function conditionCreditsEnRetard()
    {
        $subQuery = $this->subQueryTotalRembourse();
        $today = now()->format('Y-m-d');

        return function ($query) use ($subQuery, $today) {
            $query->whereRaw("{$subQuery} < (capital + interet)")
                  ->whereNull('date_cloture_forcee')
                  ->whereDate('date_fin_prevue', '<', $today);
        };
    }

    // =========================================================
    // CŒUR DU BUSINESS : CRÉDITS ACTIFS UNIQUEMENT
    // =========================================================

    public function creditsActifs()
    {
        return $this->credits()->where($this->conditionCreditsActifs());
    }

    public function creditsTermines()
    {
        $subQuery = $this->subQueryTotalRembourse();
        return $this->credits()->where(function ($query) use ($subQuery) {
            $query->whereRaw("{$subQuery} >= (capital + interet)")
                  ->orWhereNotNull('date_cloture_forcee');
        });
    }

    // =========================================================
    // FILTRES DEVISES
    // =========================================================

    public function creditsCDF()
    {
        return $this->creditsActifs()->where('monnaie', 'CDF');
    }

    public function creditsUSD()
    {
        return $this->creditsActifs()->where('monnaie', 'USD');
    }

    // =========================================================
    // KPI CDF (ACTIF UNIQUEMENT)
    // =========================================================

    public function getCapitalActifCdfAttribute()
    {
        return $this->creditsCDF()->sum('capital');
    }

    public function getInteretActifCdfAttribute()
    {
        return $this->creditsCDF()->sum('interet');
    }

    public function getEncoursActifCdfAttribute()
    {
        return $this->capital_actif_cdf + $this->interet_actif_cdf;
    }

    public function getCreditsRetardActifsCdfAttribute()
    {
        return $this->credits()
            ->where('monnaie', 'CDF')
            ->where($this->conditionCreditsEnRetard())
            ->count();
    }

    // =========================================================
    // KPI USD (ACTIF UNIQUEMENT)
    // =========================================================

    public function getCapitalActifUsdAttribute()
    {
        return $this->creditsUSD()->sum('capital');
    }

    public function getInteretActifUsdAttribute()
    {
        return $this->creditsUSD()->sum('interet');
    }

    public function getEncoursActifUsdAttribute()
    {
        return $this->capital_actif_usd + $this->interet_actif_usd;
    }

    public function getCreditsRetardActifsUsdAttribute()
    {
        return $this->credits()
            ->where('monnaie', 'USD')
            ->where($this->conditionCreditsEnRetard())
            ->count();
    }

    // =========================================================
    // INDICATEURS STRATÉGIQUES (DÉCISION)
    // =========================================================

    public function getExpositionCdfAttribute()
    {
        return $this->encours_actif_cdf;
    }

    public function getExpositionUsdAttribute()
    {
        return $this->encours_actif_usd;
    }

    public function getTauxRisqueCdfAttribute()
    {
        $total = $this->creditsCDF()->count();
        if ($total === 0) return 0;

        return round(
            ($this->credits_retard_actifs_cdf / $total) * 100,
            1
        );
    }

    public function getTauxRisqueUsdAttribute()
    {
        $total = $this->creditsUSD()->count();
        if ($total === 0) return 0;

        return round(
            ($this->credits_retard_actifs_usd / $total) * 100,
            1
        );
    }

    // =========================================================
    // RISK LEVEL
    // =========================================================

    public function getNiveauRisqueCdfAttribute()
    {
        return match (true) {
            $this->credits_retard_actifs_cdf == 0 => 'faible',
            $this->credits_retard_actifs_cdf < 5 => 'moyen',
            default => 'élevé'
        };
    }

    public function getNiveauRisqueUsdAttribute()
    {
        return match (true) {
            $this->credits_retard_actifs_usd == 0 => 'faible',
            $this->credits_retard_actifs_usd < 5 => 'moyen',
            default => 'élevé'
        };
    }

    // =========================================================
    // DASHBOARD DÉCISIONNEL
    // =========================================================

    public function getDashboardData(): array
    {
        return [
            'zone' => $this->nom,
            'CDF' => [
                'exposition' => $this->exposition_cdf,
                'capital' => $this->capital_actif_cdf,
                'interet' => $this->interet_actif_cdf,
                'encours' => $this->encours_actif_cdf,
                'credits_retard' => $this->credits_retard_actifs_cdf,
                'taux_risque' => $this->taux_risque_cdf,
                'niveau_risque' => $this->niveau_risque_cdf,
            ],
            'USD' => [
                'exposition' => $this->exposition_usd,
                'capital' => $this->capital_actif_usd,
                'interet' => $this->interet_actif_usd,
                'encours' => $this->encours_actif_usd,
                'credits_retard' => $this->credits_retard_actifs_usd,
                'taux_risque' => $this->taux_risque_usd,
                'niveau_risque' => $this->niveau_risque_usd,
            ],
            'global' => [
                'total_exposition' => $this->exposition_cdf + $this->exposition_usd,
                'credits_actifs' => $this->creditsActifs()->count(),
            ]
        ];
    }

    // =========================================================
    // OPTIMISATION SQL (SCOPE)
    // =========================================================

    public function scopeWithPerformance($query)
    {
        $subQueryRembourse = "(SELECT COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0)
                               FROM credit_remboursements
                               WHERE credit_remboursements.credit_id = credits.id)";

        $today = now()->format('Y-m-d');

        return $query
            ->withCount([
                'credits as credits_actifs_count' => function ($q) use ($subQueryRembourse, $today) {
                    $q->whereRaw("{$subQueryRembourse} < (capital + interet)")
                      ->whereNull('date_cloture_forcee');
                }
            ])
            ->withSum(['credits as capital_actif' => function ($q) use ($subQueryRembourse, $today) {
                $q->whereRaw("{$subQueryRembourse} < (capital + interet)")
                  ->whereNull('date_cloture_forcee');
            }], 'capital')
            ->withSum(['credits as interet_actif' => function ($q) use ($subQueryRembourse, $today) {
                $q->whereRaw("{$subQueryRembourse} < (capital + interet)")
                  ->whereNull('date_cloture_forcee');
            }], 'interet');
    }
}