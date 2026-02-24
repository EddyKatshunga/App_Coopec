<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloturesComptable extends Model
{
    use Blameable;

    protected $fillable = [
        'agence_id',
        'date_cloture',
        'report_coffre_cdf',
        'report_coffre_usd',
        'report_epargne_cdf',
        'report_epargne_usd',
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
        'total_revenu_cdf',
        'total_revenu_usd',
        'physique_coffre_usd',
        'physique_coffre_cdf',
        'observation_cloture',
        'total_interet_generes_cdf',
        'total_interet_generes_usd',
        'statut',
        'solde_epargne_cdf',
        'solde_epargne_usd',
        'solde_coffre_cdf',
        'solde_coffre_usd',
    ];

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
        'total_revenu_cdf' => 'decimal:2',
        'total_revenu_usd' => 'decimal:2',
        'solde_epargne_cdf' => 'decimal:2',
        'solde_epargne_usd' => 'decimal:2',
        'solde_coffre_cdf' => 'decimal:2',
        'solde_coffre_usd' => 'decimal:2',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function revenus() { return $this->hasMany(Revenu::class, 'journee_comptable_id'); }
    public function depenses() { return $this->hasMany(Depense::class, 'journee_comptable_id'); }
    public function credits() { return $this->hasMany(Credit::class, 'journee_comptable_id'); }
    public function remboursements() { return $this->hasMany(CreditRemboursement::class, 'journee_comptable_id'); }
    public function transactionsEpargne() { return $this->hasMany(Transaction::class, 'journee_comptable_id'); }
    
    public function depots()
    {
        return $this->hasMany(Transaction::class, 'journee_comptable_id')->whereIn('type_transaction', ['depot', 'DEPOT']);
    }

    public function retraits()
    {
        return $this->hasMany(Transaction::class, 'journee_comptable_id')->whereIn('type_transaction', ['retrait', 'RETRAIT']);
    }

    public function estCloturee(): bool
    {
        return $this->statut === 'cloturee';
    }
}