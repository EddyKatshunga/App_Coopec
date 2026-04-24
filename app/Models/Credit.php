<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Credit extends Model
{
    use ManageClotureComptable;
    use AffectsCoffre;
    use Blameable;
    
    protected $hidden = ['id'];
    
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
    
    protected $fillable = [
        'numero_credit', 'membre_id', 'user_id', 'agent_id', 'zone_id', 'agence_id',
        'monnaie', 'capital', 'interet', 'total_remboursement',
        'taux_penalite_journalier', 'unite_temps', 'duree',
        'date_fin_prevue', 'garant_nom', 'garant_adresse',
        'garant_telephone', 'negocie', 'note_negociation',
        'date_cloture_forcee', 'observation',
    ];

    protected $casts = [
        'date_credit' => 'date',
        'date_fin_prevue' => 'date',
        'date_cloture_forcee' => 'datetime',
        'negocie' => 'boolean',
        // Utilisation de décimales pour éviter les bugs de précision financière
        'capital' => 'decimal:2',
        'interet' => 'decimal:2',
        'taux_penalite_journalier' => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */
    
    public function journeeComptable(): BelongsTo
    {
        return $this->belongsTo(CloturesComptable::class, 'journee_comptable_id');
    }

    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class)->orderBy('date_paiement', 'asc');
    }

    public function membre(): BelongsTo { return $this->belongsTo(Membre::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function zone(): BelongsTo { return $this->belongsTo(Zone::class); }
    public function agence(): BelongsTo { return $this->belongsTo(Agence::class); }
    public function agent(): BelongsTo { return $this->belongsTo(Agent::class); }

    /* ================= MÉTHODES DE CONFIGURATION ================= */

    public function getDateColumnName(): string
    {
        return 'date_credit';
    }

    public function getDateCalculAttribute(): Carbon
    {
        return now();
    }
    
    public function isAddition(): bool 
    {
        return false;
    }

    /* ================= ATTRIBUTS CALCULÉS ================= */

    public function getTotalAttribute(): float
    {
        return (float) ($this->capital + $this->interet);
    }

    public function getMontantEcheanceAttribute(): float
    {
        // Protection contre la division par zéro
        return $this->duree > 0 ? (float) ($this->total / $this->duree) : $this->total;
    }

    public function getSituationActuelle(?Carbon $dateConsultation = null): array
    {
        $dateRef = $dateConsultation ?? $this->date_calcul;
        $resteDu = $this->total; 
        $dateDernierCalcul = clone $this->date_fin_prevue;

        // Optimisation N+1 : Filtrer la collection en mémoire si chargée
        if ($this->relationLoaded('remboursements')) {
            $remboursements = $this->remboursements
                ->where('date_paiement', '<=', $dateRef)
                ->sortBy('date_paiement');
        } else {
            $remboursements = $this->remboursements()
                ->where('date_paiement', '<=', $dateRef)
                ->orderBy('date_paiement', 'asc') 
                ->get();
        }

        $restePenalite = 0.0;
        
        foreach ($remboursements as $paiement) {
            $montantAffecteALaBase = $paiement->montant_capital_payee + $paiement->montant_interet_payee;
            $resteDu -= $montantAffecteALaBase;
            $restePenalite = (float) $paiement->reste_penalite;

            if ($paiement->date_paiement->gt($this->date_fin_prevue)) {
                $dateDernierCalcul = clone $paiement->date_paiement;
            }
        }
        
        $penalitesCourantes = $restePenalite;
        $joursRetardCourants = 0;
        
        if ($dateRef->gt($dateDernierCalcul) && $dateRef->gt($this->date_fin_prevue) && $resteDu > 0) {
            $joursRetardCourants = (int) $dateRef->diffInDays($dateDernierCalcul, true);
            $penalitesCourantes += $joursRetardCourants * $resteDu * ($this->taux_penalite_journalier / 100);
        }

        return [
            'reste_du_base' => max(0, round($resteDu, 2)),
            'penalites_courantes' => round($penalitesCourantes, 2),
            'total_a_payer' => round(max(0, $resteDu) + $penalitesCourantes, 2),
            'jours_retard_courants' => $joursRetardCourants,
        ];
    }

    public function getResteDuAttribute(): float
    {
        return (float) $this->getSituationActuelle()['reste_du_base'];
    }

    public function getPenalitesCourantesAttribute(): float
    {
        return (float) $this->getSituationActuelle()['penalites_courantes'];
    }

    public function getStatutAttribute(): string
    {
        if ($this->date_cloture_forcee && $this->negocie) {
            return 'termine_negocie';
        }

        $situation = $this->getSituationActuelle();
        
        if ($situation['total_a_payer'] <= 0) {
            // Optimisation : Prendre le dernier paiement de la collection si chargée
            $dernierPaiement = $this->relationLoaded('remboursements') 
                ? $this->remboursements->sortByDesc('date_paiement')->first()
                : $this->remboursements()->latest('date_paiement')->first();

            $dateFinEffective = $dernierPaiement ? $dernierPaiement->date_paiement : $this->date_calcul;

            return $dateFinEffective->gt($this->date_fin_prevue) ? 'termine_en_retard' : 'termine';
        }

        if ($this->date_calcul->gt($this->date_fin_prevue)) {
            return 'en_retard';
        }

        return 'en_cours';
    }

    public function getActifAttribute(): bool
    {
        return in_array($this->statut, ['en_cours', 'en_retard'], true);
    }

    public function getHistoriquePenalites(): Collection
    {
        $historique = [];
        $resteDu = $this->total;
        $dateRef = clone $this->date_fin_prevue;
        
        $remboursements = $this->relationLoaded('remboursements')
            ? $this->remboursements->sortBy('date_paiement')
            : $this->remboursements()->orderBy('date_paiement', 'asc')->get();

        foreach ($remboursements as $paiement) {
            if ($paiement->date_paiement->gt($this->date_fin_prevue)) {
                $jours = $paiement->date_paiement->diffInDays($dateRef);
                
                if ($jours > 0) {
                    $montantPenalite = $jours * $resteDu * ($this->taux_penalite_journalier / 100);
                    
                    $historique[] = [
                        'date_debut' => $dateRef->format('d/m/Y'),
                        'date_fin'   => $paiement->date_paiement->format('d/m/Y'),
                        'jours'      => $jours,
                        'base'       => round($resteDu, 2),
                        'taux'       => $this->taux_penalite_journalier,
                        'total'      => round($montantPenalite, 2),
                        'type'       => 'Paiement effectué'
                    ];
                    
                    $dateRef = clone $paiement->date_paiement;
                }
            }
            $resteDu -= ($paiement->montant_capital_payee + $paiement->montant_interet_payee);
        }

        $today = now();
        if ($today->gt($dateRef) && $resteDu > 0) {
            $joursCourants = $today->diffInDays($dateRef);
            if ($joursCourants > 0) {
                $historique[] = [
                    'date_debut' => $dateRef->format('d/m/Y'),
                    'date_fin'   => $today->format('d/m/Y'),
                    'jours'      => $joursCourants,
                    'base'       => round($resteDu, 2),
                    'taux'       => $this->taux_penalite_journalier,
                    'total'      => round($joursCourants * $resteDu * ($this->taux_penalite_journalier / 100), 2),
                    'type'       => 'Période en cours'
                ];
            }
        }

        return collect($historique);
    }

    /**
     * Scope pour calculer le montant payé via une sous-requête (très performant)
     */
    public function scopeWithSommesPayees($query)
    {
        return $query->addSelect(['total_paye' => \App\Models\CreditRemboursement::selectRaw(
            'COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0)'
        )
        ->whereColumn('credit_id', 'credits.id')]);
    }

    /**
     * Scope pour filtrer par actif/inactif
     */
    public function scopeActif($query, $bool = true)
    {
        $subQuery = "((SELECT COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0) 
                    FROM credit_remboursements WHERE credit_id = credits.id) < (capital + interet))";

        return $bool 
            ? $query->whereRaw($subQuery)->whereNull('date_cloture_forcee')
            : $query->where(fn($q) => $q->whereRaw("NOT $subQuery")->orWhereNotNull('date_cloture_forcee'));
    }

    /**
     * Scope pour les statuts spécifiques
     */
    public function scopeStatut($query, $statut)
    {
        $today = now()->format('Y-m-d');
        $subPaye = "(SELECT COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0) 
                    FROM credit_remboursements WHERE credit_id = credits.id)";

        return match ($statut) {
            'en_cours' => $query->whereRaw("$subPaye < (capital + interet)")
                                ->whereDate('date_fin_prevue', '>=', $today)
                                ->whereNull('date_cloture_forcee'),
            'en_retard' => $query->whereRaw("$subPaye < (capital + interet)")
                                ->whereDate('date_fin_prevue', '<', $today)
                                ->whereNull('date_cloture_forcee'),
            'termine' => $query->whereRaw("$subPaye >= (capital + interet)"),
            'termine_negocie' => $query->whereNotNull('date_cloture_forcee')->where('negocie', true),
            default => $query,
        };
    }

    public function scopeEnRetard($query)
    {
        return $query->actif()->whereDate('date_fin_prevue', '<', now());
    }

    /* ================= STATISTIQUES ================= */

    public static function getCreditGroupedByZone(int $agenceId, $date): Collection
    {
        return self::with('zone')
            ->selectRaw('zone_id, monnaie, COUNT(*) as nbre_operations, SUM(capital) as total_montant')
            ->where('agence_id', $agenceId)
            ->whereDate('date_credit', $date)
            ->groupBy('zone_id', 'monnaie')
            ->get()
            ->groupBy('zone_id');
    }

    public static function getInteretGroupedByZone(int $agenceId, $date): Collection
    {
        return self::with('zone')
            ->selectRaw('zone_id, monnaie, COUNT(*) as nbre_operations, SUM(interet) as total_montant')
            ->where('agence_id', $agenceId)
            ->whereDate('date_credit', $date)
            ->groupBy('zone_id', 'monnaie')
            ->get()
            ->groupBy('zone_id');
    }
}