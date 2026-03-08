<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;
use App\Models\Agence;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CloturesList extends Component
{
    use WithPagination;

    public $agenceId = null;
    public $dateDebut;
    public $dateFin;

    public function mount()
    {
        $this->agenceId = auth()->user()->agence_id ?? null;
        // Optionnel : Par défaut les 30 derniers jours
        $this->dateDebut = now()->subDays(30)->format('Y-m-d');
        $this->dateFin = now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['agenceId', 'dateDebut', 'dateFin'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = CloturesComptable::query()
            ->where('agence_id', $this->agenceId);

        if ($this->dateDebut) $query->whereDate('date_cloture', '>=', $this->dateDebut);
        if ($this->dateFin) $query->whereDate('date_cloture', '<=', $this->dateFin);

        // Calcul des agrégats pour la période filtrée
        $stats = (clone $query)->selectRaw('
            SUM(total_depot_usd) as depot_usd, SUM(total_depot_cdf) as depot_cdf,
            SUM(total_retrait_usd) as retrait_usd, SUM(total_retrait_cdf) as retrait_cdf,
            SUM(total_remboursement_usd) as rembourse_usd, SUM(total_remboursement_cdf) as rembourse_cdf,
            SUM(total_credit_usd) as credit_usd, SUM(total_credit_cdf) as credit_cdf,
            SUM(total_depense_usd) as depense_usd, SUM(total_depense_cdf) as depense_cdf
        ')->first();

        $clotures = $query->orderBy('date_cloture', 'desc')->paginate(15);
        $agences = auth()->user()->can('can.level6') ? Agence::all() : collect();

        return view('livewire.clotures.clotures-list', [
            'clotures' => $clotures,
            'agences' => $agences,
            'stats' => $stats
        ]);
    }
}