<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Services\AccountingService;
use App\Helpers\AccountingHelper;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ReglementTiersComponent extends Component
{
    public $type = 'dette'; // dette ou creance
    public $montant = '';
    public $monnaie = 'CDF';
    public $reference = '';

    public function rules()
    {
        return [
            'type'     => 'required|in:dette,creance',
            'montant'  => 'required|numeric|min:0.01',
            'monnaie'  => 'required|in:CDF,USD',
            'reference'=> 'nullable|string|max:255',
        ];
    }

    public function save(AccountingService $service)
    {
        $this->validate();

        $compteCaisse = Account::where('numero', '57')->firstOrFail();
        $montant = (float) $this->montant;

        if ($this->type === 'dette') {
            // Paiement d'une dette : Débit Dettes diverses / Crédit Caisse
            $compteTiers = Account::where('numero', '40')->firstOrFail();
            $lines = [
                AccountingHelper::debit($compteTiers->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteCaisse->id, $montant, $this->monnaie),
            ];
            $libelle = "Règlement dette - {$this->reference}";
        } else {
            // Encaissement d'une créance : Débit Caisse / Crédit Créances diverses
            $compteTiers = Account::where('numero', '45')->firstOrFail();
            $lines = [
                AccountingHelper::debit($compteCaisse->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteTiers->id, $montant, $this->monnaie),
            ];
            $libelle = "Encaissement créance - {$this->reference}";
        }

        $service->record($lines, $libelle);

        session()->flash('message', ucfirst($this->type) . ' enregistrée.');
        $this->reset(['montant', 'reference']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        return view('livewire.accounting.reglement-tiers-component', compact('agence', 'journee'));
    }
}