<?php

namespace App\Models;

use App\Helpers\AccountingHelper;
use App\Models\Traits\Blameable;
use App\Models\Traits\ManageClotureComptable;
use App\Services\AccountingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//GESTION DES TRANSACTIONS EPARGNES
class Transaction extends Model
{
    use ManageClotureComptable;
    Use Blameable;
    
    protected $hidden = ['id'];
    
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'compte_id',
        'agent_collecteur_id',
        'type_transaction',
        'montant',
        'monnaie',
        'solde_avant',
        'solde_apres',
        'status',
        'journal_entry_id',    
    ];

    // --------------------------------------------------------------
    // Génération automatique de l'écriture comptable
    // --------------------------------------------------------------
    protected static function booted()
    {
        static::created(function (Transaction $transaction) {
            $compteCaisseNumero = '57';
            $compteCaisse = Account::where('numero', $compteCaisseNumero)->firstOrFail();
            $compteEpargneNumero = '41';
            $compteEpargne = Account::where('numero', $compteEpargneNumero)->firstOrFail();

            if (!$compteEpargne) {
                throw new \RuntimeException("Le compte épargne #{$transaction->compte->id} n'est pas lié à un compte comptable.");
            }

            // Construction des lignes selon le type de transaction
            if ($transaction->type_transaction === 'DEPOT') {
                // Dépôt : on débite la caisse, on crédite le compte épargne
                $debit  = AccountingHelper::debit($compteCaisse->id, $transaction->montant, $transaction->monnaie);
                $credit = AccountingHelper::credit($compteEpargne->id, $transaction->montant, $transaction->monnaie);
                $libelle = "Dépôt épargne : compte #{$transaction->compte->numero_compte}";
            } else { // RETRAIT
                // Retrait : on débite le compte épargne, on crédite la caisse
                $debit  = AccountingHelper::debit($compteEpargne->id, $transaction->montant, $transaction->monnaie);
                $credit = AccountingHelper::credit($compteCaisse->id, $transaction->montant, $transaction->monnaie);
                $libelle = "Retrait épargne : compte #{$transaction->compte->numero_compte}";
            }

            app(AccountingService::class)->record(
                [$debit, $credit],
                $libelle,
                $transaction
            );
        });
    }

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

    /**
     * Retourne la colonne de date spécifique à ce modèle.
     */
    public function getDateColumnName(): string
    {
        return 'date_transaction';
    }

    public function compte(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }

    public function agent_collecteur(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_collecteur_id');
    }

    public function isAddition(): bool {
        return $this->type_transaction === 'DEPOT';
    }

    public static function getDepotsGroupedByAgent(int $agenceId, $date)
    {
        return self::with('agent_collecteur')
            ->selectRaw('agent_collecteur_id, monnaie, COUNT(*) as nbre_operations, SUM(montant) as total_montant')
            ->where('type_transaction', 'DEPOT')
            ->where('agence_id', $agenceId)
            ->whereDate('date_transaction', $date)
            ->groupBy('agent_collecteur_id', 'monnaie')
            ->get()
            ->groupBy('agent_collecteur_id'); // Permet de regrouper CDF et USD sous le même agent
    }

    public static function getRetraitsGroupedByAgent(int $agenceId, $date)
    {
        return self::with('creator')
            ->selectRaw('created_by, monnaie, COUNT(*) as nbre_operations, SUM(montant) as total_montant')
            ->where('type_transaction', 'RETRAIT')
            ->where('agence_id', $agenceId)
            ->whereDate('date_transaction', $date)
            ->groupBy('created_by', 'monnaie')
            ->get()
            ->groupBy('created_by'); // Permet de regrouper CDF et USD sous le même agent
    }
}
