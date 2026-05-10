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
class TransfertComponent extends Component
{
    use HasAgenceContext;
    
    public $sens = 'caisse_vers_banque'; // ou banque_vers_caisse
    public $montant = '';
    public $monnaie = 'CDF';

    public function rules()
    {
        return [
            'sens'    => 'required|in:caisse_vers_banque,banque_vers_caisse',
            'montant' => 'required|numeric|min:0.01',
            'monnaie' => 'required|in:CDF,USD',
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
        $compteBanque = Account::where('numero', '52')->firstOrFail();
        $montant = (float) $this->montant;

        if ($this->sens === 'caisse_vers_banque') {
            $lines = [
                AccountingHelper::debit($compteBanque->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteCaisse->id, $montant, $this->monnaie),
            ];
            $libelle = "Transfert Caisse → Banque";
        } else {
            $lines = [
                AccountingHelper::debit($compteCaisse->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteBanque->id, $montant, $this->monnaie),
            ];
            $libelle = "Transfert Banque → Caisse";
        }

        $service->record($lines, $libelle);

        session()->flash('message', 'Transfert effectué.');
        $this->reset(['montant']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        return view('livewire.accounting.transfert-component', compact('agence', 'journee'));
    }
}
