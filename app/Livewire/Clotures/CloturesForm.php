<?php

namespace App\Livewire\Clotures;

use App\Models\Agence;
use App\Models\CloturesComptable;
use App\Services\ClotureService;
use App\Services\ClotureVerificationService;
use App\Services\ClotureStatisticsService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CloturesForm extends Component
{
    public ?CloturesComptable $cloture = null;
    public ?Agence $agence = null;
    public bool $isOuverture = true;

    public array $donneesPhysiques = [
        'observation' => '',
    ];

    public bool $verificationsOk = false;
    public array $checklist = [];
    public array $statistiques = [];

    protected function rules()
    {
        if (!$this->isOuverture) {
            return [
                'donneesPhysiques.observation' => 'nullable|string|max:1000',
            ];
        }
        return [];
    }

    public function mount($cloture = null, $agence = null)
    {
        if ($cloture instanceof CloturesComptable || (is_numeric($cloture) && !empty($cloture))) {
            $this->cloture = ($cloture instanceof CloturesComptable)
                ? $cloture
                : CloturesComptable::findOrFail($cloture);
            $this->isOuverture = false;
            $this->agence = $this->cloture->agence;
            $this->chargerPreCloture();
        } elseif ($agence) {
            $this->agence = ($agence instanceof Agence) ? $agence : Agence::findOrFail($agence);
            $this->isOuverture = true;
            $this->chargerPreOuverture();
        } else {
            abort(404, 'Aucune agence ou clôture spécifiée');
        }
    }

    protected function chargerPreOuverture()
    {
        $verifService = app(ClotureVerificationService::class);
        $result = $verifService->checkPreOuverture($this->agence);
        $this->checklist = $result['checklist'];
        $this->verificationsOk = $result['allOk'];
    }

    protected function chargerPreCloture()
    {
        $verifService = app(ClotureVerificationService::class);
        $result = $verifService->checkPreCloture($this->cloture);
        $this->checklist = $result['checklist'];
        $this->verificationsOk = $result['allOk'];

        $statsService = app(ClotureStatisticsService::class);
        $this->statistiques = $statsService->getStatistics($this->cloture);
    }

    public function ouvrir(ClotureService $clotureService)
    {
        $this->chargerPreOuverture(); // rafraîchit la checklist
        if (!$this->verificationsOk) {
            $this->dispatch('notify', type: 'error', message: 'Veuillez résoudre les vérifications avant d\'ouvrir.');
            return;
        }

        try {
            $nouvelleJournee = $clotureService->ouvrirJournee($this->agence);
            session()->flash('message', "Journée du {$nouvelleJournee->date_cloture->format('d/m/Y')} ouverte avec succès.");
            return redirect()->route('clotures.index');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function cloturer(ClotureService $clotureService)
    {
        $this->validate();

        $this->chargerPreCloture(); // re-vérifications
        if (!$this->verificationsOk) {
            $this->dispatch('notify', type: 'error', message: 'Veuillez résoudre toutes les vérifications avant de clôturer.');
            return;
        }

        try {
            $success = $clotureService->cloturerJournee($this->cloture, $this->donneesPhysiques);
            if ($success) {
                session()->flash('message', "Journée du {$this->cloture->date_cloture->format('d/m/Y')} clôturée avec succès.");
                return redirect()->route('clotures.index');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.clotures.clotures-form');
    }
}