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
    public ?Agence $agence = null;
    public bool $isOuverture = false;
    
    // Pour l'affichage lors de l'ouverture
    public $reportVeilleCoffreUsd = 0;
    public $reportVeilleCoffreCdf = 0;
    public $reportVeilleEpargneUsd = 0;
    public $reportVeilleEpargneCdf = 0;

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
        // 1. Logique pour la CLÔTURE (On vérifie d'abord si c'est une clôture car c'est l'édition)
        // On vérifie si $cloture est un objet ou un ID numérique
        if ($cloture instanceof CloturesComptable || (is_numeric($cloture) && !empty($cloture))) {
            
            $this->cloture = ($cloture instanceof CloturesComptable) 
                ? $cloture 
                : CloturesComptable::findOrFail($cloture);

            $this->isOuverture = false;
            $this->physique_coffre_usd = $this->cloture->solde_coffre_usd ?? 0;
            $this->physique_coffre_cdf = $this->cloture->solde_coffre_cdf ?? 0;
            
            // Très important : charger l'agence liée à la clôture pour éviter les erreurs plus tard
            $this->agence = $this->cloture->agence;
        } 
        // 2. Logique pour l'OUVERTURE
        elseif ($agence) {
            $this->agence = ($agence instanceof Agence) ? $agence : Agence::findOrFail($agence);
            $this->isOuverture = true;
                
            $this->reportVeilleCoffreUsd = $this->agence->solde_actuel_coffre_usd ?? 0;
            $this->reportVeilleCoffreCdf = $this->agence->solde_actuel_coffre_cdf ?? 0;
            $this->reportVeilleEpargneUsd = $this->agence->solde_actuel_epargne_usd ?? 0;
            $this->reportVeilleEpargneCdf = $this->agence->solde_actuel_epargne_cdf ?? 0;
        }
    }

    // --- ACTIONS D'OUVERTURE ---
    public function validerOuverture(ClotureService $service)
    {
        try {
            $agence = auth()->user()->agence;
            $service->ouvrirJournee($agence);
            
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
            return redirect()->route('clotures.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // --- COMPUTED PROPERTIES (Uniquement appelées si on est en clôture) ---
    public function getDepotsProperty() {
        return $this->isOuverture ? [] : $this->cloture->depots()->with('agent_collecteur')
            ->get()->groupBy('agent_collecteur_id');
    }

    public function getRetraitsProperty() {
        return $this->isOuverture ? [] : $this->cloture->retraits()->with('creator')
            ->get()->groupBy('created_by');
    }
    public function getCreditsProperty() {
        return $this->isOuverture ? [] : $this->cloture->credits()->with('zone')
            ->get()->groupBy('zone_id');
    }
    public function getRemboursementsProperty() {
        return $this->isOuverture ? [] : $this->cloture->remboursements()->with('zone')
            ->get()->groupBy('zone_id');
    }
    public function getRevenusProperty() {
        return $this->isOuverture ? [] : $this->cloture->revenus()->with('typeRevenu')
            ->get()->groupBy('types_revenu_id');
    }
    public function getDepensesProperty() {
        return $this->isOuverture ? [] : $this->cloture->depenses()->with('typeDepense')
            ->get()->groupBy('types_depense_id');
    }

    public function render()
    {
        return view('livewire.clotures.clotures-form');
    }
}