<?php

namespace App\Models;

use App\Models\Traits\AffectsCoffre;
use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    use VerifieClotureComptable;
    use AffectsCoffre;
    use Blameable;
    
    protected $fillable = [
        'date_credit',
        'numero_credit',
        'membre_id',
        'zone_id',
        'monnaie',
        'capital',
        'interet',
        'taux_penalite_journalier',
        'unite_temps',
        'duree',
        'date_fin_prevue',
        'garant_nom',
        'garant_adresse',
        'garant_telephone',
        'negocie',
        'note_negociation',
        'date_cloture_forcee'
    ];

    protected $casts = [
        'date_credit' => 'date',
        'date_fin_prevue' => 'date',
        'negocie' => 'boolean',
    ];

    /* ================= RELATIONS ================= */
    public function remboursements(): HasMany
    {
        return $this->hasMany(CreditRemboursement::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function agence() {
        return $this->zone->agence;
    }

    public function getAgenceIdAttribute() {
        return $this->agence?->id;
    }

    public function isAddition(): bool {
        return false; // Toujours une diminution
    }

    /* ================= ATTRIBUTS CALCULÉS ================= */

    public function getTotalAttribute()
    {
        return $this->capital + $this->interet;
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
        $debut = $this->date_fin_prevue->addDays(10);
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

        if ($this->jours_retard > 0 && $this->total_rembourse >= $this->total)
            return 'retard_penalite';

        if ($this->jours_retard > 0)
            return 'en_retard';

        return 'en_cours';
    }
}

