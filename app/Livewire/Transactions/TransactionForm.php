<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Compte;
use App\Models\Agent;
use App\Services\TransactionService;

class TransactionForm extends Component
{
    public Compte $compte;
    public string $type_transaction = 'DEPOT';
    public string $monnaie = 'CDF';
    public $montant;
    public ?int $agent_collecteur_id = null;

    protected function rules(): array
    {
        return [
            'monnaie'             => 'required|in:CDF,USD',
            'montant'             => 'required|numeric|min:0.01',
            'agent_collecteur_id' => 'nullable|exists:agents,id',
        ];
    }

    public function mount(Compte $compte, string $type = 'DEPOT')
    {
        $this->compte = $compte;
        $this->type_transaction = $type;
        $this->agent_collecteur_id = auth()->user()->agent?->id;
    }

    // Propriété calculée pour le solde actuel selon la monnaie
    public function getSoldeActuelProperty()
    {
        return $this->monnaie === 'CDF' ? $this->compte->solde_cdf : $this->compte->solde_usd;
    }

    public function submit(TransactionService $service)
    {
        $this->validate();

        // Vérification manuelle du solde pour le retrait
        if ($this->type_transaction === 'RETRAIT' && $this->montant > $this->soldeActuel) {
            $this->addError('montant', 'Solde insuffisant pour effectuer ce retrait.');
            return;
        }

        try {
            $service->enregistrerTransactionEpargne(
                $this->compte->id, 
                $this->type_transaction, 
                $this->monnaie,
                $this->montant,
                $this->agent_collecteur_id,
            );

            session()->flash('success', 'Transaction enregistrée avec succès.');
            return redirect()->route('transaction.list');

        } catch (\Exception $e) {
            $this->addError('montant', 'Erreur lors de l\'insertion : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.transactions.transaction-form', [
            // On s'assure de récupérer les agents de la même agence
            'agents' => Agent::where('agence_id', auth()->user()->agence_id)->get(),
        ])->layout('layouts.app');
    }
}