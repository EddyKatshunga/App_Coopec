<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Services\AccountingService;
use App\Helpers\AccountingHelper;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Models\Traits\HasAgenceContext;

#[Layout('layouts.app')]
class ChargeComponent extends Component
{
    use HasAgenceContext;
    
    public $account_id = '';
    public $montant = '';
    public $monnaie = 'CDF';
    public $mode = 'caisse'; // caisse ou credit
    public $libelle = '';

    public function rules()
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'montant'    => 'required|numeric|min:0.01',
            'monnaie'    => 'required|in:CDF,USD',
            'mode'       => 'required|in:caisse,credit',
            'libelle'    => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        $this->secureAgenceContext();
        $this->secureJourneeContext();
    }

    public function save(AccountingService $service)
    {
        $this->validate();

        $compteCaisse = Account::where('numero', '57')->firstOrFail();
        $compteDette  = Account::where('numero', '40')->firstOrFail();
        $compteCharge = Account::find($this->account_id);

        $montant = (float) $this->montant;

        if ($this->mode === 'caisse') {
            // Charge payée immédiatement : Débit Charge / Crédit Caisse
            $lines = [
                AccountingHelper::debit($compteCharge->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteCaisse->id, $montant, $this->monnaie),
            ];
            $libelleDetail = "paiement en espèces";
        } else {
            // Charge à crédit : Débit Charge / Crédit Dettes diverses
            $lines = [
                AccountingHelper::debit($compteCharge->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteDette->id, $montant, $this->monnaie),
            ];
            $libelleDetail = "facture à crédit";
        }

        $libelleFinal = $this->libelle ?: $compteCharge->nom . " - " . $libelleDetail;

        $service->record($lines, $libelleFinal);

        session()->flash('message', 'Charge enregistrée avec succès.');
        $this->reset(['account_id', 'montant', 'libelle']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        $comptesCharge = Account::where('type', 'charge')->where('est_actif', true)->get();

        return view('livewire.accounting.charge-component', [
            'agence' => $agence,
            'journee' => $journee,
            'comptesCharge' => $comptesCharge,
        ]);
    }
}