<?php
namespace App\Livewire\Clotures;

use App\Models\Agence;
use Livewire\Component;
use App\Models\CloturesComptable;
use App\Models\Transaction;
use App\Models\Credit;
use App\Models\CreditRemboursement;
use App\Models\Revenu;
use App\Models\Depense;
use App\Services\ClotureService;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CloturesForm extends Component
{
    public ?CloturesComptable $cloture = null;
    public bool $isOuverture = false;
    
    // Pour l'affichage lors de l'ouverture
    public $reportVeilleUsd = 0;
    public $reportVeilleCdf = 0;

    // Variables du Wizard (Clôture)
    public $step = 1;
    public $totalSteps = 7;
    public $physique_coffre_usd;
    public $physique_coffre_cdf;
    public $observation_cloture;
    public $ecart_constate = false;

    protected $rules = [
        'physique_coffre_usd' => 'required_if:ecart_constate,true|numeric|min:0|nullable',
        'physique_coffre_cdf' => 'required_if:ecart_constate,true|numeric|min:0|nullable',
        'observation_cloture' => 'required_if:ecart_constate,true|string|max:1000|nullable',
    ];

    public function mount($cloture = null, $agence = null)
    {
        // 1. Logique pour l'OUVERTURE (On reçoit une Agence ou un ID d'agence)
        if ($agence) {
            // Si c'est un ID qui est passé au lieu de l'objet
            $agenceModel = ($agence instanceof Agence) ? $agence : Agence::findOrFail($agence);
            
            $this->isOuverture = true;
            
            // On récupère la dernière clôture pour les reports
            $derniere = CloturesComptable::where('agence_id', $agenceModel->id)
                ->orderBy('date_cloture', 'desc')
                ->first();
                
            $this->reportVeilleUsd = $derniere->solde_coffre_usd ?? 0;
            $this->reportVeilleCdf = $derniere->solde_coffre_cdf ?? 0;
        } 
        // 2. Logique pour la CLÔTURE (On reçoit une Cloture existante)
        elseif ($cloture && $cloture instanceof CloturesComptable && $cloture->exists) {
            $this->cloture = $cloture;
            $this->isOuverture = false;
            
            $this->physique_coffre_usd = $cloture->solde_coffre_usd ?? 0;
            $this->physique_coffre_cdf = $cloture->solde_coffre_cdf ?? 0;
        }
    }

    // --- ACTIONS D'OUVERTURE ---
    public function validerOuverture(ClotureService $service)
    {
        try {
            $agenceId = auth()->user()->agence_id;
            $service->ouvrirJournee($agenceId);
            
            session()->flash('success', 'La journée a été ouverte avec succès.');
            return redirect()->route('clotures.index'); // Ou vers le dashboard
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // --- ACTIONS DE CLÔTURE (Wizard) ---
    public function nextStep()
    {
        if ($this->step < $this->totalSteps) $this->step++;
    }

    public function previousStep()
    {
        if ($this->step > 1) $this->step--;
    }

    public function validerCloture(ClotureService $service)
    {
        $this->validate();

        $usd = $this->ecart_constate ? $this->physique_coffre_usd : $this->cloture->solde_coffre_usd;
        $cdf = $this->ecart_constate ? $this->physique_coffre_cdf : $this->cloture->solde_coffre_cdf;

        try {
            $service->cloturerJournee($this->cloture, [
                'usd' => $usd,
                'cdf' => $cdf,
                'observation' => $this->observation_cloture,
            ]);

            session()->flash('success', 'Journée clôturée avec succès.');
            return redirect()->route('clotures.show', $this->cloture->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // --- COMPUTED PROPERTIES (Uniquement appelées si on est en clôture) ---
    public function getDepotsProperty() {
        return $this->isOuverture ? [] : Transaction::getDepotsGroupedByAgent($this->cloture->agence_id, $this->cloture->date_cloture);
    }
    public function getRetraitsProperty() {
        return $this->isOuverture ? [] : Transaction::getRetraitsGroupedByAgent($this->cloture->agence_id, $this->cloture->date_cloture);
    }
    public function getCreditsProperty() {
        return $this->isOuverture ? [] : Credit::getCreditGroupedByZone($this->cloture->agence_id, $this->cloture->date_cloture, 'date_deblocage');
    }
    public function getRemboursementsProperty() {
        return $this->isOuverture ? [] : CreditRemboursement::getGroupedByZone($this->cloture->agence_id, $this->cloture->date_cloture, 'date_paiement');
    }
    public function getRevenusProperty() {
        return $this->isOuverture ? [] : Revenu::getGroupedByType($this->cloture->agence_id, $this->cloture->date_cloture);
    }
    public function getDepensesProperty() {
        return $this->isOuverture ? [] : Depense::getGroupedByType($this->cloture->agence_id, $this->cloture->date_cloture);
    }

    public function render()
    {
        return view('livewire.clotures.clotures-form');
    }
}