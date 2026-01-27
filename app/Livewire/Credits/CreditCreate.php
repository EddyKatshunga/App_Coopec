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
    public $membre_id;
    public $zone_id;

    public $capital;
    public $interet;
    public $taux_penalite_journalier;

    public $unite_temps = 'mois';
    public $duree;
    
    public $date_fin_proposee; // Date calculée automatiquement
    public $date_fin_confirmee; // Date saisie/confirmée par l'utilisateur

    public $garant_nom;
    public $garant_adresse;
    public $garant_telephone;

    /* ================= DATA ================= */

    public $membres = [];
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

    public function mount()
    {
        $this->date_credit = now()->format('Y-m-d');
        $this->membres = Membre::orderBy('nom')->get();
        $this->zones = Zone::orderBy('nom')->get();
        
        // Initialiser la date de fin proposée
        $this->calculateDateFinProposee();
    }

    /* ================= CALCUL DATE FIN PROPOSÉE ================= */

    public function calculateDateFinProposee()
    {
        if ($this->date_credit && $this->duree && $this->unite_temps) {
            try {
                $dateFin = Carbon::parse($this->date_credit)
                    ->add((int) $this->duree, $this->unite_temps);
                
                $this->date_fin_proposee = $dateFin->format('Y-m-d');
                
                // Si l'utilisateur n'a pas encore saisi de date, pré-remplir avec la date calculée
                if (!$this->date_fin_confirmee) {
                    $this->date_fin_confirmee = $this->date_fin_proposee;
                }
            } catch (\Exception $e) {
                $this->date_fin_proposee = null;
            }
        } else {
            $this->date_fin_proposee = null;
        }
    }

    /* ================= LISTENERS ================= */

    protected $listeners = [
        'updateDateFinProposee' => 'calculateDateFinProposee'
    ];

    /* ================= UPDATED HOOKS ================= */

    public function updated($propertyName)
    {
        // Lorsque la date de début, la durée ou l'unité change, recalculer la date de fin proposée
        if (in_array($propertyName, ['date_credit', 'duree', 'unite_temps'])) {
            $this->calculateDateFinProposee();
        }
    }

    /* ================= ACTION ================= */

    public function save()
    {
        $this->validate();

        Credit::create([
            'date_credit' => $this->date_credit,
            'numero_credit' => strtoupper(Str::uuid()),

            'membre_id' => $this->membre_id,
            'zone_id' => $this->zone_id,

            'capital' => $this->capital,
            'interet' => $this->interet,
            'taux_penalite_journalier' => $this->taux_penalite_journalier,

            'unite_temps' => $this->unite_temps,
            'duree' => $this->duree,
            'date_fin_prevue' => $this->date_fin_confirmee, // Utiliser la date confirmée par l'utilisateur

            'garant_nom' => $this->garant_nom,
            'garant_adresse' => $this->garant_adresse,
            'garant_telephone' => $this->garant_telephone,

            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        session()->flash('success', 'Crédit octroyé avec succès.');

        return redirect()->route('credits.index');
    }

    public function render()
    {
        return view('livewire.credits.credit-create');
    }
}