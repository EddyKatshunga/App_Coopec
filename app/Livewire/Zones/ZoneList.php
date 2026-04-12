<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class ZoneList extends Component
{
    use WithPagination;

    public $selectedAgenceId = null;
    public $date_debut;
    public $date_fin;

    public function mount()
    {
        $this->selectedAgenceId = auth()->user()->agence_id ?? null;

        //Initialisation des dates
        $derniereCloture = \App\Models\CloturesComptable::latest('date_cloture')->first();
        $dateParDefaut = $derniereCloture 
            ? $derniereCloture->date_cloture->format('Y-m-d') 
            : now()->format('Y-m-d');

        $this->date_debut = $dateParDefaut;
        $this->date_fin = $dateParDefaut;
    }

    public function render()
    {
        $agenceId = auth()->user()->can('can.level6') 
            ? $this->selectedAgenceId 
            : auth()->user()->agence_id;

        $zones = Zone::where('agence_id', $agenceId)
            ->with(['gerant'])
            // Agrégation des Crédits (Capital et Intérêt) sur la période
            ->withSum(['credits as total_capital_usd' => function($q) {
                $q->whereBetween('date_credit', [$this->date_debut, $this->date_fin])->where('monnaie', 'USD');
            }], 'capital')
            ->withSum(['credits as total_capital_cdf' => function($q) {
                $q->whereBetween('date_credit', [$this->date_debut, $this->date_fin])->where('monnaie', 'CDF');
            }], 'capital')
            ->withSum(['credits as total_interet_usd' => function($q) {
                $q->whereBetween('date_credit', [$this->date_debut, $this->date_fin])->where('monnaie', 'USD');
            }], 'interet')
            ->withSum(['credits as total_interet_cdf' => function($q) {
                $q->whereBetween('date_credit', [$this->date_debut, $this->date_fin])->where('monnaie', 'CDF');
            }], 'interet')
            // Agrégation des Remboursements sur la période
            ->withSum(['remboursement as total_rembourse_usd' => function($q) {
                $q->whereBetween('date_paiement', [$this->date_debut, $this->date_fin])->where('monnaie', 'USD');
            }], 'montant')
            ->withSum(['remboursement as total_rembourse_cdf' => function($q) {
                $q->whereBetween('date_paiement', [$this->date_debut, $this->date_fin])->where('monnaie', 'CDF');
            }], 'montant')
            ->latest()
            ->paginate(15);

        $agences = auth()->user()->can('can.level6') ? Agence::orderBy('nom')->get() : collect();

        return view('livewire.zones.zone-list', [
            'zones' => $zones,
            'agences' => $agences
        ]);
    }
}