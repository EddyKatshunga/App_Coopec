<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloturesComptable extends Model
{
    use Blameable;

    /**
     * Champs autorisés en écriture
     */
    protected $fillable = [

        // Identité
        'agence_id',
        'date_cloture',

        // Reports
        'report_coffre_cdf',
        'report_coffre_usd',
        'report_epargne_cdf',
        'report_epargne_usd',

        // Totaux du jour
        'total_depot_cdf',
        'total_depot_usd',
        'total_retrait_cdf',
        'total_retrait_usd',
        'total_credit_cdf',
        'total_credit_usd',
        'total_remboursement_cdf',
        'total_remboursement_usd',
        'total_depense_cdf',
        'total_depense_usd',
        'total_autre_entree_cdf',
        'total_autre_entree_usd',

        // Soldes finaux
        'solde_epargne_cdf',
        'solde_epargne_usd',
        'solde_coffre_cdf',
        'solde_coffre_usd',
    ];

    /**
     * Casts (TRÈS IMPORTANT en finance)
     */
    protected $casts = [
        'date_cloture' => 'date',

        'report_coffre_cdf' => 'decimal:2',
        'report_coffre_usd' => 'decimal:2',
        'report_epargne_cdf' => 'decimal:2',
        'report_epargne_usd' => 'decimal:2',

        'total_depot_cdf' => 'decimal:2',
        'total_depot_usd' => 'decimal:2',
        'total_retrait_cdf' => 'decimal:2',
        'total_retrait_usd' => 'decimal:2',
        'total_credit_cdf' => 'decimal:2',
        'total_credit_usd' => 'decimal:2',
        'total_remboursement_cdf' => 'decimal:2',
        'total_remboursement_usd' => 'decimal:2',
        'total_depense_cdf' => 'decimal:2',
        'total_depense_usd' => 'decimal:2',
        'total_autre_entree_cdf' => 'decimal:2',
        'total_autre_entree_usd' => 'decimal:2',

        'solde_epargne_cdf' => 'decimal:2',
        'solde_epargne_usd' => 'decimal:2',
        'solde_coffre_cdf' => 'decimal:2',
        'solde_coffre_usd' => 'decimal:2',
    ];

    /* =====================================================
     * RELATIONS
     * ===================================================== */

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* =====================================================
     * SCOPES MÉTIER
     * ===================================================== */

    /**
     * Vérifie si une date est clôturée (ou antérieure)
     */
    public function scopeDateCloturee($query, int $agenceId, string $date)
    {
        return $query
            ->where('agence_id', $agenceId)
            ->where('date_cloture', '>=', $date);
    }

    /* =====================================================
     * HELPERS MÉTIER
     * ===================================================== */

    public function estCloturee(): bool
    {
        return true; // par définition
    }
}
