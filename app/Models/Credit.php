<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    use ManageClotureComptable;
    use AffectsCoffre;
    use Blameable;
    
    protected $hidden = ['id'];
    
    public function getRouteKeyName()
    {
        return 'uuid';
    }
    
    protected $fillable = [
        'numero_credit', 'membre_id', 'user_id', 'agent_id', 'zone_id',
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
    ];

    /* ================= RELATIONS ================= */
    
    public function journeeComptable(): BelongsTo
    {
        return $this->belongsTo(CloturesComptable::class, 'journee_comptable_id');
    }

    public function getDateColumnName(): string
    {
        return 'date_credit';
    }

    public function getDateCalculAttribute()
    {
        return auth()->user()->journee_ouverte->date_cloture ?? now();
    }
    
    public function remboursements(): HasMany
    {
        // Important : Toujours ordonner chronologiquement pour le calcul séquentiel
        return $this->hasMany(CreditRemboursement::class)->orderBy('date_paiement', 'asc');
    }

    public function membre(): BelongsTo { return $this->belongsTo(Membre::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function zone(): BelongsTo { return $this->belongsTo(Zone::class); }
    public function agence(): BelongsTo { return $this->belongsTo(Agence::class); }
    public function agent(): BelongsTo { return $this->belongsTo(Agent::class); }

    public function isAddition(): bool {
        return false;
    }

    /* ================= ATTRIBUTS CALCULÉS ================= */

    public function getTotalAttribute()
    {
        return $this->capital + $this->interet;
    }

    public function getMontantEcheanceAttribute()
    {
        return $this->total / $this->duree;
    }

    public function getTotalRembourseAttribute()
    {
        return $this->remboursements()->sum('montant');
    }

    /**
     * Moteur de calcul séquentiel (Cœur de votre logique métier)
     * Retourne un tableau avec l'état exact du crédit à l'instant T (ou aujourd'hui).
     */
    public function getSituationActuelle($dateConsultation = null)
    {
        $dateRef = $dateConsultation ?? $this->date_calcul;
        $resteDu = $this->total;
        $dateDernierCalcul = $this->date_fin_prevue->copy(); // Les pénalités commencent après cette date

        $remboursements = $this->remboursements;
        $totalPenalitesPayees = 0;

        foreach ($remboursements as $paiement) {
            $datePaiement = $paiement->date_paiement; // Assurez-vous que c'est casté en Carbon dans CreditRemboursement

            // 1. Calcul des pénalités générées entre le dernier calcul et ce paiement
            $penalitesDeCePaiement = 0;
            if ($datePaiement->gt($dateDernierCalcul) && $datePaiement->gt($this->date_fin_prevue)) {
                $joursRetard = $datePaiement->diffInDays($dateDernierCalcul);
                $penalitesDeCePaiement = $joursRetard * $resteDu * ($this->taux_penalite_journalier / 100);
            }

            // 2. Le paiement absorbe d'abord les pénalités du moment, puis fait baisser le reste dû
            $montantPaye = $paiement->montant;
            
            // Si le paiement couvre les pénalités
            if ($montantPaye >= $penalitesDeCePaiement) {
                $montantPourResteDu = $montantPaye - $penalitesDeCePaiement;
                $totalPenalitesPayees += $penalitesDeCePaiement;
            } else {
                // S'il ne paie pas assez, le reste dû ne baisse pas, mais on ne cumule pas les pénalités
                // selon votre règle : "Il n’y a pas de cumul de pénalités"
                $montantPourResteDu = 0;
                $totalPenalitesPayees += $montantPaye; 
            }

            $resteDu -= $montantPourResteDu;
            
            // Mise à jour de la date de référence pour le prochain calcul
            // On ne met à jour que si on a dépassé la date de fin prévue
            if ($datePaiement->gt($this->date_fin_prevue)) {
                $dateDernierCalcul = $datePaiement->copy();
            }
        }

        // 3. Calcul des pénalités non payées (courantes) entre le dernier paiement et la date de consultation
        $penalitesCourantes = 0;
        if ($dateRef->gt($dateDernierCalcul) && $dateRef->gt($this->date_fin_prevue) && $resteDu > 0) {
            $joursRetardCourants = $dateRef->diffInDays($dateDernierCalcul);
            $penalitesCourantes = $joursRetardCourants * $resteDu * ($this->taux_penalite_journalier / 100);
        }

        return [
            'reste_du_base' => round($resteDu, 5), // Le capital + intérêts restants (ex: 125.75)
            'penalites_courantes' => round($penalitesCourantes, 5), // Pénalités générées depuis le dernier paiement (ex: 1.88625)
            'total_a_payer' => round($resteDu + $penalitesCourantes, 5), // Total absolu (ex: 127.63625)
            'jours_retard_courants' => isset($joursRetardCourants) ? $joursRetardCourants : 0,
        ];
    }

    public function getResteDuAttribute()
    {
        return $this->getSituationActuelle()['reste_du_base'];
    }

    public function getPenalitesCourantesAttribute()
    {
        return $this->getSituationActuelle()['penalites_courantes'];
    }

    public function getJoursRetardAttribute()
    {
        // Total des jours depuis la date de fin (pour l'affichage global)
        if ($this->date_calcul->gt($this->date_fin_prevue) && $this->getSituationActuelle()['total_a_payer'] > 0) {
            return $this->date_calcul->diffInDays($this->date_fin_prevue);
        }
        return 0;
    }

    public function getStatutAttribute()
    {
        if ($this->date_cloture_forcee && $this->negocie) return 'termine_negocie';

        $situation = $this->getSituationActuelle();
        
        // S'il ne doit plus rien (y compris les pénalités)
        if ($situation['total_a_payer'] <= 0) {
            // Est-ce qu'il a terminé avec ou sans retard par rapport à la date de fin ?
            // On regarde la date du dernier paiement
            $dernierPaiement = $this->remboursements()->latest('date_paiement')->first();
            $dateFinEffective = $dernierPaiement ? $dernierPaiement->date_paiement : $this->date_calcul;

            return $dateFinEffective->gt($this->date_fin_prevue) ? 'termine_en_retard' : 'termine';
        }

        // S'il doit encore de l'argent et qu'on a dépassé la date de fin
        if ($this->date_calcul->gt($this->date_fin_prevue)) return 'en_retard';

        return 'en_cours';
    }

    /**
     * Génère l'historique détaillé des paliers de pénalités pour le client.
     */
    public function getHistoriquePenalites()
    {
        $historique = [];
        $resteDu = $this->total;
        $dateRef = $this->date_fin_prevue->copy();
        $remboursements = $this->remboursements()->orderBy('date_paiement', 'asc')->get();

        foreach ($remboursements as $paiement) {
            if ($paiement->date_paiement->gt($this->date_fin_prevue)) {
                $jours = $paiement->date_paiement->diffInDays($dateRef);
                
                if ($jours > 0) {
                    $montantPenalite = $jours * $resteDu * ($this->taux_penalite_journalier / 100);
                    
                    $historique[] = [
                        'date_debut' => $dateRef->format('d/m/Y'),
                        'date_fin'   => $paiement->date_paiement->format('d/m/Y'),
                        'jours'      => $jours,
                        'base'       => $resteDu,
                        'taux'       => $this->taux_penalite_journalier,
                        'total'      => $montantPenalite,
                        'type'       => 'Paiement effectué'
                    ];
                    
                    $dateRef = $paiement->date_paiement->copy();
                }
            }
            // On déduit le capital/intérêt payé pour le prochain palier
            $resteDu -= ($paiement->montant_capital_payee + $paiement->montant_interet_payee);
        }

        // Ajouter le palier "en cours" (entre le dernier paiement et aujourd'hui)
        $today = now();
        if ($today->gt($dateRef) && $resteDu > 0) {
            $joursCourants = $today->diffInDays($dateRef);
            if ($joursCourants > 0) {
                $historique[] = [
                    'date_debut' => $dateRef->format('d/m/Y'),
                    'date_fin'   => $today->format('d/m/Y'),
                    'jours'      => $joursCourants,
                    'base'       => $resteDu,
                    'taux'       => $this->taux_penalite_journalier,
                    'total'      => $joursCourants * $resteDu * ($this->taux_penalite_journalier / 100),
                    'type'       => 'Période en cours'
                ];
            }
        }

        return collect($historique);
    }

    public static function getCreditGroupedByZone(int $agenceId, $date)
    {
        return self::with('zone')
            ->selectRaw('zone_id, monnaie, COUNT(*) as nbre_operations, SUM(capital) as total_montant')
            ->where('agence_id', $agenceId)
            ->whereDate('date_credit', $date)
            ->groupBy('zone_id', 'monnaie')
            ->get()
            ->groupBy('zone_id');
    }

    public static function getInteretGroupedByZone(int $agenceId, $date)
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

