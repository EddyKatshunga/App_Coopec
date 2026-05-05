<?php

namespace App\Models;

use App\Helpers\AccountingHelper;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Credit extends Model
{
    use ManageClotureComptable;
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
        'date_cloture_forcee', 'observation', 'statut', 'journal_entry_id',
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

    /**
     * Actions automatiques au cycle de vie du modèle.
     */
    protected static function booted()
    {
        static::created(function (Credit $credit) {
            $compteCaisseNumero    = '57';
            $compteCapitalNumero   = '46';
            $compteInteretsNumero  = '47';

            $compteCaisse   = Account::where('numero', $compteCaisseNumero)->firstOrFail();
            $compteCapital  = Account::where('numero', $compteCapitalNumero)->firstOrFail();
            $compteInterets = Account::where('numero', $compteInteretsNumero)->firstOrFail();
            $compteProduit  = Account::where('numero', '71')->firstOrFail(); // Intérêts perçus

            $capital = (float) $credit->capital;
            $interet = (float) $credit->interet;

            $lignes = [
                // Débits : créances distinctes
                AccountingHelper::debit($compteCapital->id, $capital, $credit->monnaie),
                AccountingHelper::debit($compteInterets->id, $interet, $credit->monnaie),
                // Crédits : caisse (capital seulement) et produit (intérêts)
                AccountingHelper::credit($compteCaisse->id, $capital, $credit->monnaie),
                AccountingHelper::credit($compteProduit->id, $interet, $credit->monnaie),
            ];

            app(AccountingService::class)->record(
                $lignes,
                "Octroi crédit #{$credit->numero_credit}",
                $credit
            );
        });
    }

    /* ================= RELATIONS ================= */

    /**
     * L'écriture comptable associée à cette opération.
     */
    public function journalEntry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
    
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

    public function getSituationActuelle(): array
    {
        $dateRef = now();
        $dateDernierCalcul = clone $this->date_fin_prevue;

        // Optimisation N+1 : Filtrer la collection en mémoire si chargée
        if ($this->relationLoaded('remboursements')) {
            $remboursements = $this->remboursements
                ->sortBy('date_paiement');
        } else {
            $remboursements = $this->remboursements()
                ->orderBy('date_paiement', 'asc') 
                ->get();
        }

        $restePenalite = 0.0;
        
        foreach ($remboursements as $paiement) {
            $restePenalite = (float) $paiement->reste_penalite;

            if ($paiement->date_paiement->gt($this->date_fin_prevue)) {
                $dateDernierCalcul = clone $paiement->date_paiement;
            }
        }
        
        $penalitesCourantes = $restePenalite;
        $joursRetardCourants = 0;
        
        if ($dateRef->gt($dateDernierCalcul) && $dateRef->gt($this->date_fin_prevue) && $this->reste_du > 0) {
            $joursRetardCourants = (int) $dateRef->diffInDays($dateDernierCalcul, true);
            $penalitesCourantes += $joursRetardCourants * $this->reste_du * ($this->taux_penalite_journalier / 100);
        }

        return [
            'penalites_courantes' => round($penalitesCourantes, 2),
            'jours_retard_courants' => $joursRetardCourants,
        ];
    }

    /**
     * Somme totale du capital déjà remboursé.
     */
    public function getTotalCapitalPayeAttribute(): float
    {
        return (float) $this->remboursements->sum('montant_capital_payee');
    }

    /**
     * Somme totale des intérêts déjà remboursés.
     */
    public function getTotalInteretPayeAttribute(): float
    {
        return (float) $this->remboursements->sum('montant_interet_payee');
    }

    /**
     * Vérifie si ce crédit spécifique est en retard.
     */
    public function estEnRetard(): bool
    { 
        // Doit être "en cours" ET la date de fin doit être passée.
        return $this->statut === 'en_cours' && $this->date_fin_prevue->isPast();
    }

    /**
     * Détermine le type de retard précis (Dynamique).
     */
    public function getTypeRetardAttribute(): ?string
    {
        if ($this->statut === 'termine' || now()->lessThanOrEqualTo($this->date_fin_prevue)) {
            return null;
        }

        $resteCapital = $this->capital - $this->total_capital_paye;
        $resteInteret = $this->interet - $this->total_interet_paye;
        $aDesPenalites = $this->penalites_courantes > 0;

        if ($resteCapital > 0.01 && $resteInteret > 0.01 && $aDesPenalites) {
            return 'retard_capital_interet_penalite';
        }
        if ($resteCapital > 0.01 && $resteInteret > 0.01) {
            return 'retard_capital_interet';
        }
        if ($resteCapital > 0.01) {
            return 'retard_capital';
        }
        if ($aDesPenalites) {
            return 'retard_penalites';
        }

        return 'retard';
    }

    /**
     * Suggère à l'agent si le dossier est prêt pour la clôture manuelle.
     */
    public function getPeutEtreClotureAttribute(): bool
    {
        if ($this->statut === 'termine') return false;

        // Prêt à clore si : reste du (cap+int) <= 0 ET pénalités <= 0
        return $this->reste_du <= 0.01 && $this->penalites_courantes <= 0.01;
    }

    public function getResteDuAttribute(): float
    {
        if ($this->statut === 'termine') return 0.00;
        return (float) $this->total - $this->total_remboursement;
    }

    public function getPenalitesCourantesAttribute(): float
    {
        return (float) $this->getSituationActuelle()['penalites_courantes'];
    }

    public function getJoursRetardsAttribute(): float
    {
        return (int) $this->getSituationActuelle()['jours_retard_courants'];
    }

    public function getResteGlobalAttribute(): float
    {
        return (float) $this->reste_du + $this->penalites_courantes;
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

    /* ================= QUERY SCOPES ================= */

    /**
     * Filtre par devise (USD ou CDF).
     * Utilisation : Credit::devise('USD')->...
     */
    public function scopeDevise($query, string $monnaie)
    {
        return $query->where('monnaie', strtoupper($monnaie));
    }

    /**
     * Filtre les crédits dont le dossier est encore ouvert.
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Filtre les crédits dont le dossier est officiellement fermé.
     */
    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    /**
     * Filtre les crédits en retard (En cours ET date fin dépassée).
     */
    public function scopeEnRetard($query)
    {
        return $query->enCours()
                    ->where('date_fin_prevue', '<', now()->startOfDay());
    }

    /**
     * Filtre par zone spécifique.
     */
    public function scopeParZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
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