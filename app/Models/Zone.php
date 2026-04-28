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
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | SCOPES MÉTIER (100% basés sur Credit)
    |--------------------------------------------------------------------------
    */

    public function creditsActifs()
    {
        return $this->credits()->actif();
    }

    public function creditsEnRetard()
    {
        return $this->credits()->enRetard();
    }

    public function creditsByDevise(string $devise)
    {
        return $this->creditsActifs()->where('monnaie', $devise);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES DE RECHERCHE
    |--------------------------------------------------------------------------
    */
    public function scopeWithDetailedStats($query)
    {
        return $query->withCount(['credits as nb_credits_actifs' => function ($q) {
            $q->actif();
        }])
        ->withSum(['credits as sum_engage_usd' => function ($q) {
            $q->actif()->where('monnaie', 'USD');
        }], DB::raw('capital + interet'))
        ->withSum(['credits as sum_rembourse_usd' => function ($q) {
            $q->actif()->where('monnaie', 'USD');
        }], 'total_remboursement')
        ->withSum(['credits as sum_engage_cdf' => function ($q) {
            $q->actif()->where('monnaie', 'CDF');
        }], DB::raw('capital + interet'))
        ->withSum(['credits as sum_rembourse_cdf' => function ($q) {
            $q->actif()->where('monnaie', 'CDF');
        }], 'total_remboursement');
    }

    /*
    |--------------------------------------------------------------------------
    | KPI TEMPS RÉEL
    |--------------------------------------------------------------------------
    */

    public function getKpi(string $devise): array
    {
        // 1. Statistiques Globales en une seule passe SQL
        $stats = $this->creditsActifs()
            ->where('monnaie', $devise)
            ->selectRaw('
                COUNT(id) as nb_credits,
                SUM(capital) as sum_capital,
                SUM(interet) as sum_interet,
                SUM(capital + interet) as sum_encours,
                SUM(total_remboursement) as sum_rembourse
            ')
            ->first();

        // 2. Statistiques des Retards
        // Le calcul du montant_retard devient extrêmement simple et rapide
        $retardsStats = $this->creditsEnRetard()
            ->where('monnaie', $devise)
            ->selectRaw('
                COUNT(id) as nb_retard, 
                SUM((capital + interet) - total_remboursement) as montant_retard
            ')
            ->first();

        return [
            'capital'        => (float) ($stats->sum_capital ?? 0),
            'interet'        => (float) ($stats->sum_interet ?? 0),
            'encours'        => (float) ($stats->sum_encours ?? 0),
            'rembourse'      => (float) ($stats->sum_rembourse ?? 0),
            'nb_credits'     => (int) ($stats->nb_credits ?? 0),
            'nb_retard'      => (int) ($retardsStats->nb_retard ?? 0),
            'montant_retard' => (float) ($retardsStats->montant_retard ?? 0),
        ];
    }

    public function getTauxRisqueUsdAttribute(): float
    {
        $total = $this->credits_actifs_usd;
        if ($total === 0) return 0;
        return round(($this->credits_retard_actifs_usd / $total) * 100, 2);
    }

    public function getTauxRisqueCdfAttribute(): float
    {
        $total = $this->credits_actifs_cdf;
        if ($total === 0) return 0;
        return round(($this->credits_retard_actifs_cdf / $total) * 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | INDICATEURS MÉTIER
    |--------------------------------------------------------------------------
    */

    public function getTauxRisqueAttribute(): float
    {
        $total = $this->credits_actifs_count ?? $this->creditsActifs()->count();

        if ($total === 0) return 0;

        $retard = $this->credits_retard_count ?? $this->creditsEnRetard()->count();

        return round(($retard / $total) * 100, 2);
    }

    public function getNiveauRisqueAttribute(): string
    {
        return match (true) {
            $this->taux_risque == 0 => 'faible',
            $this->taux_risque < 10 => 'moyen',
            default => 'élevé',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD DÉCISIONNEL
    |--------------------------------------------------------------------------
    */

    public function getDashboardData(): array
    {
        $cdf = $this->getKpi('CDF');
        $usd = $this->getKpi('USD');

        return [
            'zone' => $this->nom,

            'CDF' => [
                ...$cdf,
                'taux_risque' => $cdf['nb_credits'] > 0
                    ? round(($cdf['nb_retard'] / $cdf['nb_credits']) * 100, 2)
                    : 0,
            ],

            'USD' => [
                ...$usd,
                'taux_risque' => $usd['nb_credits'] > 0
                    ? round(($usd['nb_retard'] / $usd['nb_credits']) * 100, 2)
                    : 0,
            ],

            'global' => [
                'exposition' => $cdf['encours'] + $usd['encours'] * 2300,
                'credits_actifs' => $cdf['nb_credits'] + $usd['nb_credits'],
                'credits_retard' => $cdf['nb_retard'] + $usd['nb_retard'],
            ]
        ];
    }
}