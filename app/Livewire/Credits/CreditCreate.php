<?php

namespace App\Livewire\Credits;

use Livewire\Component;
use App\Models\Membre;
use App\Services\CreditService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreditCreate extends Component
{
    /* ================= FORM FIELDS ================= */
    public $date_credit;
    public $zone_id;
    public $agent_id; //L'agent ayant validé le crédit
    public $capital;
    public $interet;
    public $taux_penalite_journalier;
    public $unite_temps = 'mois';
    public $duree;
    public $monnaie;
    public $date_fin;
    public $garant_nom;
    public $garant_adresse;
    public $garant_telephone;
    public $observation = 'Rien à signaler';

    public Membre $membre;

    /* ================= DATA ================= */
    public $zones = [];
    public $agents = [];

    protected function rules()
    {
        return [
            'agent_id' => 'required|exists:agents,id',
            'zone_id' => 'required|exists:zones,id',
            'capital' => 'required|numeric|min:1',
            'interet' => 'required|numeric|min:0',
            'taux_penalite_journalier' => 'required|numeric|min:0',
            'unite_temps' => 'required|in:jour,semaine,mois,annee',
            'monnaie' => 'required|in:CDF,USD',
            'duree' => 'required|integer|min:1',
            'date_fin' => 'required|date',
            'garant_nom' => 'required|string|max:255',
            'garant_adresse' => 'nullable|string|max:255',
            'garant_telephone' => 'nullable|string|max:50',
            'observation' => 'nullable|string',
        ];
    }

    public function mount(Membre $membre)
    {
        $this->date_credit = auth()->user()->journee_ouverte?->date_cloture;
        $this->membre = $membre;
        
        // Chargement des données initiales
        $agence = Auth::user()->agence;
        $this->zones = $agence->zones()->orderBy('nom')->get();
        $this->agents = $agence->agents()->orderBy('nom')->get();
        $this->agent_id = auth()->user()->agent?->id;
    }

    public function save(CreditService $creditService)
    {
        $validatedData = $this->validate();

        try {
            // Appel au service pour la création
            $creditService->creerCredit($this->membre, $validatedData);

            session()->flash('success', 'Crédit octroyé avec succès.');
            return redirect()->route('credit.pret.index');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.credits.credit-create');
    }
}