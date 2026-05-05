<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Services\AccountingService;
use App\Helpers\AccountingHelper;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ImmobilisationComponent extends Component
{
    public $account_id = '';
    public $montant = '';
    public $monnaie = 'CDF';
    public $mode = 'caisse';
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

    public function save(AccountingService $service)
    {
        $this->validate();

        $compteCaisse = Account::where('numero', '57')->firstOrFail();
        $compteDette  = Account::where('numero', '40')->firstOrFail();
        $compteImmo = Account::find($this->account_id);

        $montant = (float) $this->montant;

        if ($this->mode === 'caisse') {
            $lines = [
                AccountingHelper::debit($compteImmo->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteCaisse->id, $montant, $this->monnaie),
            ];
            $libelleDetail = "achat au comptant";
        } else {
            $lines = [
                AccountingHelper::debit($compteImmo->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteDette->id, $montant, $this->monnaie),
            ];
            $libelleDetail = "achat à crédit";
        }

        $libelleFinal = $this->libelle ?: $compteImmo->nom . " - " . $libelleDetail;
        $service->record($lines, $libelleFinal);

        session()->flash('message', 'Immobilisation enregistrée.');
        $this->reset(['account_id', 'montant', 'libelle']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        $comptesImmo = Account::whereIn('numero', ['21', '22'])->where('est_actif', true)->get();

        return view('livewire.accounting.immobilisation-component', compact('agence', 'journee', 'comptesImmo'));
    }
}