<?php

namespace App\Livewire\Credits;

use App\Models\Membre;
use App\Models\Agent;
use App\Models\Traits\HasAgenceContext;
use App\Models\Zone;
use App\Services\CreditService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreditCreate extends Component
{
    use HasAgenceContext;

    public Membre $membre;
    public $agent_id;
    public $zone_id;
    public $monnaie = 'CDF';
    public $capital = 0;
    public $interet = 0;
    public $taux_penalite_journalier = 1;
    public $unite_temps = 'mois';
    public $duree = 1;
    public $date_fin;
    public $date_credit;
    public $garant_nom;
    public $garant_adresse = '';
    public $garant_telephone = '';
    public $observation = '';

    protected $rules = [
        'agent_id' => 'required|exists:agents,id',
        'zone_id' => 'required|exists:zones,id',
        'monnaie' => 'required|in:CDF,USD',
        'capital' => 'required|numeric|min:1',
        'interet' => 'required|numeric|min:0',
        'taux_penalite_journalier' => 'required',
        'duree' => 'required|integer|min:1',
        'unite_temps' => 'required|in:jour,semaine,mois,annee',
        'date_fin' => 'required|date|after:date_credit',
        'garant_nom' => 'required|string',
        'garant_adresse' => 'nullable|string',
        'garant_telephone' => 'nullable|string',
        'observation' => 'nullable|string',
    ];

    protected function messages()
    {
        return [
            'agent_id.required' => 'Sélectionnez un agent.',
            'zone_id.required' => 'La zone est obligatoire.',
            'capital.min' => 'Le montant doit être > 0.',
            'date_fin.after' => 'La date doit être après le ' . \Carbon\Carbon::parse($this->date_credit)->format('d/m/Y'),
            'garant_nom.required' => 'Le nom du garant est requis.',
        ];
    }

    public function mount(Membre $membre)
    {
        $this->membre = $membre;
        if ($this->membre->hasActiveCredit()) {
            session()->flash('error', 'Ce membre possède déjà un crédit actif.');
            return redirect()->route('membre.show', $membre);
        }
        
        $journee = $this->secureJourneeContext();
        $this->date_credit = $journee->date_cloture;
        $this->recalculerDateFin();
        $this->agent_id = Auth::user()->agent?->id;
    }

    public function updated($property)
    {
        // 1. On recalcule d'abord si nécessaire
        if (in_array($property, ['unite_temps', 'duree', 'date_credit'])) {
            // On s'assure que duree est un entier pour éviter les bugs de calcul
            $this->duree = (int)$this->duree > 0 ? (int)$this->duree : 1;
            
            $this->recalculerDateFin();
        }

        // 2. On valide APRES le calcul pour que l'erreur s'affiche sur la nouvelle date
        $this->validateOnly($property);
        
        // Si on a changé l'unité ou la durée, on valide aussi la date_fin 
        // pour que l'icône de succès/erreur se mette à jour immédiatement
        if (in_array($property, ['unite_temps', 'duree'])) {
            $this->validateOnly('date_fin');
        }
    }

    private function recalculerDateFin()
    {
        $this->date_fin = $this->calculateEndDate();
    }

    private function calculateEndDate()
    {
        $date = \Carbon\Carbon::parse($this->date_credit);
        $dureeInt = (int)$this->duree;

        for ($i = 1; $i <= $dureeInt; $i++) {
            if ($this->unite_temps === 'jour') {
                $date->addDay();
                while ($date->isSunday()) { $date->addDay(); }
            } elseif ($this->unite_temps === 'semaine') {
                $date->addWeek();
            } elseif ($this->unite_temps === 'mois') {
                $date->addMonth();
            } elseif ($this->unite_temps === 'annee') { // Changement ici
                $date->addYear();
            }
        }
        return $date->format('Y-m-d');
    }

    public function save(CreditService $service)
    {
        $data = $this->validate();
        $service->creerCredit($this->membre, $data);
        session()->flash('success', 'Crédit créé avec succès.');
        return redirect()->route('credit.pret.index');
    }

    public function render()
    {
        return view('livewire.credits.credit-create', [
            'agents' => Agent::where('agence_id', Auth::user()->agence_id)->get(),
            'zones' => Auth::user()->agence->zones()->get(),
        ]);
    }
}