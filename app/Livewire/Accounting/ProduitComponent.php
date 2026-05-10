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
class ProduitComponent extends Component
{
    use HasAgenceContext;
    
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

    public function mount()
    {
        $this->secureAgenceContext();
        $this->secureJourneeContext();
    }

    public function save(AccountingService $service)
    {
        $this->validate();

        $compteCaisse = Account::where('numero', '57')->firstOrFail();
        $compteCreance = Account::where('numero', '45')->firstOrFail();
        $compteProduit = Account::find($this->account_id);

        $montant = (float) $this->montant;

        if ($this->mode === 'caisse') {
            $lines = [
                AccountingHelper::debit($compteCaisse->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteProduit->id, $montant, $this->monnaie),
            ];
            $libelleDetail = "encaissement espèces";
        } else {
            $lines = [
                AccountingHelper::debit($compteCreance->id, $montant, $this->monnaie),
                AccountingHelper::credit($compteProduit->id, $montant, $this->monnaie),
            ];
            $libelleDetail = "créance client";
        }

        $libelleFinal = $this->libelle ?: $compteProduit->nom . " - " . $libelleDetail;
        $service->record($lines, $libelleFinal);

        session()->flash('message', 'Produit enregistré.');
        $this->reset(['account_id', 'montant', 'libelle']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        $comptesProduit = Account::where('type', 'produit')->where('est_actif', true)->get();

        return view('livewire.accounting.produit-component', compact('agence', 'journee', 'comptesProduit'));
    }
}
