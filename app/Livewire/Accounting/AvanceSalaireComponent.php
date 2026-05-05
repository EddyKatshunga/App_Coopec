<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Services\AccountingService;
use App\Helpers\AccountingHelper;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AvanceSalaireComponent extends Component
{
    public $montant = '';
    public $monnaie = 'CDF';
    public $employe_nom = '';
    public $libelle = '';

    public function rules()
    {
        return [
            'montant'      => 'required|numeric|min:0.01',
            'monnaie'      => 'required|in:CDF,USD',
            'employe_nom'  => 'nullable|string|max:100',
            'libelle'      => 'nullable|string|max:255',
        ];
    }

    public function save(AccountingService $service)
    {
        $this->validate();

        $compteCaisse = Account::where('numero', '57')->firstOrFail();
        $compteCreance = Account::where('numero', '45')->firstOrFail(); // Créances diverses
        $montant = (float) $this->montant;

        $lines = [
            AccountingHelper::debit($compteCreance->id, $montant, $this->monnaie),
            AccountingHelper::credit($compteCaisse->id, $montant, $this->monnaie),
        ];

        $libelleFinal = $this->libelle ?: "Avance sur salaire - {$this->employe_nom}";
        $service->record($lines, $libelleFinal);

        session()->flash('message', 'Avance enregistrée (créance).');
        $this->reset(['montant', 'employe_nom', 'libelle']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        return view('livewire.accounting.avance-salaire-component', compact('agence', 'journee'));
    }
}
