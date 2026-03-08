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
    // Formulaire
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
        'garant_adresse' => 'string',
        'garant_telephone' => 'string',
        'observation' => 'string',
    ];

    public function mount(Membre $membre)
    {
        if ($this->membre->hasActiveCredit()) {
            session()->flash('error', 'Opération impossible : Ce membre possède déjà un crédit actif.');
            return redirect()->route('membre.show', $membre);
        }
        $this->membre = $membre;
        $journee = $this->secureJourneeContext();
        $this->date_credit = $journee->date_cloture;
        $this->recalculerDateFin();
        $this->agent_id = Auth::user()->agent?->id;
    }

    public function save(CreditService $service)
    {
        $data = $this->validate();
        $data['date_fin'] = $this->date_fin; // Valeur déjà présente, éventuellement modifiée
        $service->creerCredit($this->membre, $data);

        session()->flash('success', 'Crédit créé avec succès.');
        return redirect()->route('credit.pret.index');
    }

    private function recalculerDateFin()
    {
        $this->date_fin = $this->calculateEndDate();
    }

    private function calculateEndDate()
    {
        $date = \Carbon\Carbon::parse($this->date_credit);
        for ($i = 1; $i <= $this->duree; $i++) {
            if ($this->unite_temps === 'jour') {
                $date->addDay();
                while ($date->isSunday()) { $date->addDay(); }
            } elseif ($this->unite_temps === 'semaine') {
                $date->addWeek();
            } elseif ($this->unite_temps === 'mois') {
                $date->addMonth();
            } else {
                $date->addYear();
            }
        }
        return $date->format('Y-m-d');
    }

    public function updated($property)
    {
        // On écoute tout changement sur ces trois propriétés
        if (in_array($property, ['unite_temps', 'duree', 'date_credit'])) {
            $this->recalculerDateFin();
        }
    }

    public function render()
    {
        return view('livewire.credits.credit-create', [
            'agents' => Agent::where('agence_id', Auth::user()->agence_id)->get(),
            'zones' => Zone::all(),
        ]);
    }
}