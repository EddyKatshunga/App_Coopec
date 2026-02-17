<?php

namespace App\Livewire\Credits;

use Livewire\Component;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CreditCreate extends Component
{
    /* ================= FORM FIELDS ================= */

    public $date_credit;
    public $zone_id;

    public $capital;
    public $interet;
    public $taux_penalite_journalier;

    public $unite_temps = 'mois';
    public $duree;
    public $date_fin; // Date saisie/confirmée par l'utilisateur

    public $garant_nom;
    public $garant_adresse;
    public $garant_telephone;

    public Membre $membre;

    /* ================= DATA ================= */
    public $zones = [];

    /* ================= VALIDATION ================= */

    protected function rules()
    {
        return [
            'date_credit' => 'required|date',
            'membre_id' => 'required|exists:membres,id',
            'zone_id' => 'required|exists:zones,id',

            'capital' => 'required|numeric|min:1',
            'interet' => 'required|numeric|min:0',
            'taux_penalite_journalier' => 'required|numeric|min:0',

            'unite_temps' => 'required|in:jour,semaine,mois,annee',
            'duree' => 'required|integer|min:1',

            'date_fin_confirmee' => 'required|date',

            'garant_nom' => 'required|string|max:255',
            'garant_adresse' => 'nullable|string|max:255',
            'garant_telephone' => 'nullable|string|max:50',
        ];
    }

    /* ================= LIFECYCLE ================= */

    public function mount(Membre $membre)
    {
        $this->date_credit = now()->format('Y-m-d');
        $this->zones = Zone::orderBy('nom')->get();
        $this->membre = $membre;
    }

    /* ================= ACTION ================= */

    public function save()
    {
        $this->validate();

        Credit::create([
            'date_credit' => $this->date_credit,
            'numero_credit' => strtoupper(Str::uuid()),

            'membre_id' => $this->membre->id,
            'zone_id' => $this->zone_id,

            'capital' => $this->capital,
            'interet' => $this->interet,
            'taux_penalite_journalier' => $this->taux_penalite_journalier,

            'unite_temps' => $this->unite_temps,
            'duree' => $this->duree,
            'date_fin_prevue' => $this->date_fin,

            'garant_nom' => $this->garant_nom,
            'garant_adresse' => $this->garant_adresse,
            'garant_telephone' => $this->garant_telephone,
        ]);

        session()->flash('success', 'Crédit octroyé avec succès.');

        return redirect()->route('credits.index');
    }

    public function render()
    {
        return view('livewire.credits.credit-create')
            ->layout('layouts.app');
    }
}