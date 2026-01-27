<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CreditRemboursement extends Model
{
    use Blameable;

    /* ================= MASS ASSIGNMENT ================= */

    protected $fillable = [
        'credit_id',
        'date_paiement',
        'montant',

        // ventilation financière
        'montant_penalite_payee',
        'montant_interet_payee',
        'montant_capital_payee',

        // snapshot comptable
        'report_avant',
        'reste_du_apres',
        'reference_paiement',

        'agent_id',
        'mode_paiement',
    ];

    /* ================= CASTS ================= */

    protected $casts = [
        'date_paiement' => 'date',

        'montant' => 'decimal:2',
        'montant_penalite_payee' => 'decimal:2',
        'montant_interet_payee' => 'decimal:2',
        'montant_capital_payee' => 'decimal:2',

        'report_avant' => 'decimal:2',
        'reste_du_apres' => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function zone(): BelongsTo
    {
        return $this->credit->zone();
    }

    /* ================= ACCESSEURS MÉTIER ================= */

    /**
     * Indique si ce remboursement couvre entièrement
     * le capital + intérêt du crédit
     */
    public function getSoldeCreditAttribute(): bool
    {
        return $this->reste_du_apres <= 0;
    }

    /**
     * Remboursement effectué en retard ?
     */
    public function getEstEnRetardAttribute(): bool
    {
        return $this->credit
            && $this->date_paiement->gt($this->credit->date_fin_prevue);
    }

    /**
     * Remboursement affectant des pénalités ?
     */
    public function getContientPenaliteAttribute(): bool
    {
        return $this->montant_penalite_payee > 0;
    }

    /**
     * Libellé lisible du mode de paiement
     */
    public function getModePaiementLabelAttribute(): string
    {
        return match ($this->mode_paiement) {
            'cash'   => 'Espèces',
            'mpesa'  => 'M-Pesa',
            'airtel' => 'Airtel Money',
            'banque' => 'Virement bancaire',
            default  => 'Inconnu',
        };
    }
}
