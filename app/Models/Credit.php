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
    
    protected $fillable = [
        'numero_credit',
        'membre_id',
        'user_id',
        'agent_id', //Agent ayant validé le crédit
        'zone_id',
        'monnaie',
        'capital',
        'interet',
        'total_remboursement',
        'taux_penalite_journalier',
        'unite_temps',
        'duree', //Echeance
        'date_fin_prevue',
        'garant_nom',
        'garant_adresse',
        'garant_telephone',
        'negocie',
        'note_negociation',
        'date_cloture_forcee',
        'observation',
    ];

    protected $casts = [
        'date_credit' => 'date',
        'date_fin_prevue' => 'date',
        'negocie' => 'boolean',
    ];

    /* ================= RELATIONS ================= */
    public function journeeComptable(): BelongsTo
    {
        return $this->belongsTo(CloturesComptable::class, 'journee_comptable_id');
    }

    /**
     * Retourne la colonne de date spécifique à ce modèle.
     */
    public function getDateColumnName(): string
    {
        return 'date_credit';
    }
    
    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class);
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function agent(): BelongsTo //L'agent ayant validé le crédit
    {
        return $this->belongsTo(Agent::class);
    }

    public function isAddition(): bool {
        return false; // Toujours une diminution
    }

    /* ================= ATTRIBUTS CALCULÉS ================= */

    public function getTotalAttribute()
    {
        return $this->capital + $this->interet;
    }

    public function getMontantEcheanceAttribute() //Le montant à payer par échéance
    {
        return ($this->capital + $this->interet) / $this->duree;
    }

    public function getTotalRembourseAttribute()
    {
        return $this->remboursements()->sum('montant');
    }

    public function getResteDuAttribute()
    {
        return max(0, $this->total + $this->penalites_courantes - $this->total_rembourse);
    }

    public function getJoursRetardAttribute()
    {
        $debut = $this->date_fin_prevue->addDays(10); //Mise en démeure
        return now()->greaterThan($debut)
            ? $debut->diffInDays(now())
            : 0;
    }

    public function getPenalitesCourantesAttribute()
    {
        if ($this->jours_retard <= 0) return 0;

        return ($this->reste_capital_penalisable *
            $this->taux_penalite_journalier / 100) *
            $this->jours_retard;
    }

    public function getStatutAttribute()
    {
        if ($this->date_cloture_forcee) return 'termine_negocie';

        if ($this->reste_du <= 0 && now()->lte($this->date_fin_prevue))
            return 'termine';

        if ($this->reste_du <= 0 && now()->gt($this->date_fin_prevue))
            return 'termine_en_retard';

        if ($this->jours_retard > 0)
            return 'en_retard';

        return 'en_cours';
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

