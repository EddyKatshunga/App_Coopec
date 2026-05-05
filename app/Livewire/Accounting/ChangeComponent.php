<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Models\TauxChange;
use App\Services\AccountingService;
use App\Helpers\AccountingHelper;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ChangeComponent extends Component
{
    public $devise_source = 'USD';
    public $devise_cible = 'CDF';
    public $montant_source = '';
    public $taux = null;

    public function mount()
    {
        $tauxActif = TauxChange::actuel();
        $this->taux = $tauxActif?->taux_vente ?? 2500;
    }

    public function rules()
    {
        return [
            'devise_source'  => 'required|in:CDF,USD',
            'devise_cible'   => 'required|in:CDF,USD|different:devise_source',
            'montant_source' => 'required|numeric|min:0.01',
            'taux'           => 'required|numeric|min:0.0001',
        ];
    }

    public function setSource($devise)
    {
        $this->devise_source = $devise;
        $this->devise_cible = $devise === 'CDF' ? 'USD' : 'CDF';
        $this->reset(['montant_source', 'taux']);
    }

    public function save(AccountingService $service)
    {
        $this->validate();

        $compteCaisse = Account::where('numero', '57')->firstOrFail();
        $compteVirement = Account::where('numero', '58')->firstOrFail();  // 'Virements internes'

        $montantSource = (float) $this->montant_source;
        $taux = (float) $this->taux;

        // Calcul correct du montant cible selon la devise source
        if ($this->devise_source === 'CDF') {
            $montantCible = round($montantSource / $taux, 2);
        } else {
            $montantCible = round($montantSource * $taux, 2);
        }

        if ($montantCible <= 0) {
            throw new \Exception("Le montant cible est nul ou négatif.");
        }

        // Construction des 4 lignes
        $lines = [];

        // 1. Sortie de la devise source (caisse)
        // 2. Compensation entrante sur le compte 58 (même devise)
        if ($this->devise_source === 'USD') {
            $lines[] = AccountingHelper::credit($compteCaisse->id, $montantSource, 'USD', $taux);
            $lines[] = AccountingHelper::debit($compteVirement->id, $montantSource, 'USD', $taux);
        } else {
            $lines[] = AccountingHelper::credit($compteCaisse->id, $montantSource, 'CDF');
            $lines[] = AccountingHelper::debit($compteVirement->id, $montantSource, 'CDF');
        }

        // 3. Entrée de la devise cible (caisse)
        // 4. Compensation sortante sur le compte 58 (même devise)
        if ($this->devise_cible === 'USD') {
            $lines[] = AccountingHelper::debit($compteCaisse->id, $montantCible, 'USD', $taux);
            $lines[] = AccountingHelper::credit($compteVirement->id, $montantCible, 'USD', $taux);
        } else {
            $lines[] = AccountingHelper::debit($compteCaisse->id, $montantCible, 'CDF');
            $lines[] = AccountingHelper::credit($compteVirement->id, $montantCible, 'CDF');
        }

        $libelle = "Change : {$montantSource} {$this->devise_source} → {$montantCible} {$this->devise_cible} (taux {$taux})";
        $service->record($lines, $libelle);

        session()->flash('message', 'Change enregistré.');
        $this->reset(['montant_source']);
    }

    public function render()
    {
        $agence = Auth::user()->agence;
        $journee = Auth::user()->journee_ouverte;
        return view('livewire.accounting.change-component', compact('agence', 'journee'));
    }
}