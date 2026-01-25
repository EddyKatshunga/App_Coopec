<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Compte;
use App\Models\Agence;
use App\Models\Agent;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Validation\ValidationException;

class TransactionForm extends Component
{
    /** =======================
     *  Champs du formulaire
     *  ======================= */
    public string $searchCompte = '';
    public ?int $compte_id = null;

    public ?float $solde_cdf = null;
    public ?float $solde_usd = null;

    /** 
     * Le type est fixé par le formulaire parent
     * DEPOT ou RETRAIT — NON MODIFIABLE par l’utilisateur
     */
    public string $type_transaction = 'DEPOT';

    public string $monnaie = 'CDF';
    public float $montant = 0;

    public int $agence_id;
    public ?int $agent_collecteur_id = null;

    public string $date_transaction;

    /** =======================
     *  Validation
     *  ======================= */
    protected function rules(): array
    {
        return [
            'compte_id'            => 'required|exists:comptes,id',
            'type_transaction'     => 'required|in:DEPOT,RETRAIT',
            'monnaie'              => 'required|in:CDF,USD',
            'montant'              => 'required|numeric|min:0.01',
            'agence_id'            => 'required|exists:agences,id',
            'agent_collecteur_id'  => 'nullable|exists:agents,id',
            'date_transaction'     => 'required|date|before_or_equal:today',
        ];
    }

    /** =======================
     *  Initialisation
     *  ======================= */
    public function mount(string $type = 'DEPOT')
    {
        $this->type_transaction = $type;
        $this->date_transaction = now()->toDateString();

        /** 
         * Agence par défaut = agence de l’utilisateur connecté
         * (modifiable uniquement si autorisé par l’UI)
         */
        $this->agence_id = 1;
    }

    /** =======================
     *  Sélection du compte
     *  ======================= */
    public function selectCompte(int $id): void
    {
        $compte = Compte::with('membre')->findOrFail($id);

        $this->compte_id  = $compte->id;
        $this->solde_cdf  = $compte->solde_cdf;
        $this->solde_usd  = $compte->solde_usd;

        $this->searchCompte =
            $compte->numero_compte . ' — ' . $compte->membre->nom;
    }

    /** =======================
     *  Validation métier retrait
     *  ======================= */
    public function updatedMontant(): void
    {
        if ($this->type_transaction !== 'RETRAIT') {
            return;
        }

        if (
            ($this->monnaie === 'CDF' && $this->montant > $this->solde_cdf) ||
            ($this->monnaie === 'USD' && $this->montant > $this->solde_usd)
        ) {
            throw ValidationException::withMessages([
                'montant' => 'Solde insuffisant pour effectuer ce retrait.',
            ]);
        }
    }

    /** =======================
     *  Soumission
     *  ======================= */
    public function submit(TransactionService $service)
    {
        $this->validate();

        /** 
         * Vérification de clôture comptable
         * (date antérieure à la dernière transaction interdite)
         */
        $derniereTransaction = Transaction::where('agence_id', $this->agence_id)
            ->orderByDesc('date_transaction')
            ->first();

        if (
            $derniereTransaction &&
            $this->date_transaction < $derniereTransaction->date_transaction
        ) {
            throw ValidationException::withMessages([
                'date_transaction' =>
                    'Impossible d’enregistrer une transaction antérieure au '
                    . \Carbon\Carbon::parse($derniereTransaction->date_transaction)->format('d/m/Y'),
            ]);
        }

        /** 
         * Exécution de l’opération
         */
        $service->effectuerOperation([
            'compte_id'            => $this->compte_id,
            'type_transaction'     => $this->type_transaction,
            'monnaie'              => $this->monnaie,
            'montant'              => $this->montant,
            'agence_id'            => $this->agence_id,
            'agent_collecteur_id'  => $this->agent_collecteur_id,
            'date_transaction'     => $this->date_transaction,
        ]);

        session()->flash('success', 'Transaction enregistrée avec succès.');

        /** Reset partiel */
        //$this->reset(['montant', 'agent_collecteur_id']);
        return redirect()->route('transaction.list');
    }

    // Pour réinitialiser la recherche si l'utilisateur efface le champ
    public function updatedSearchCompte($value)
    {
        if (empty($value)) {
            $this->compte_id = null;
            $this->solde_cdf = null;
            $this->solde_usd = null;
        }
    }

    /** =======================
     *  Rendu
     *  ======================= */
    public function render()
    {
        $comptes = collect();

        // On ne recherche que si on a 5 caractères ET que l'utilisateur n'a pas encore validé un choix
        // (ou si vous voulez qu'il puisse rechercer après avoir effacé)
        if (strlen($this->searchCompte) >= 5 && !$this->compte_id) {
            $comptes = Compte::query()
                ->with('membre') // Eager loading pour éviter le problème N+1
                ->where(function ($query) {
                    $searchTerm = "%{$this->searchCompte}%";
                    
                    $query->where('numero_compte', 'like', $searchTerm)
                        ->orWhereHas('membre', function ($q) use ($searchTerm) {
                            // Utilisation de ilike si vous êtes sur PostgreSQL pour l'insensibilité à la casse
                            $q->where('nom', 'like', $searchTerm);
                        });
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.transactions.transaction-form', [
            'comptes' => $comptes,
            'agences' => Agence::all(),
            'agents'  => Agent::all(),
        ]);
    }
}
