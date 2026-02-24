<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;
use App\Services\ClotureService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CloturesList extends Component
{
    use WithPagination;

    public $agenceId;

    public function mount()
    {
        $this->agenceId = auth()->user()->agence_id;
    }

    public function ouvrirJournee(ClotureService $service)
    {
        try {
            $service->ouvrirJournee($this->agenceId);
            session()->flash('success', 'La journée a été ouverte avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $clotures = CloturesComptable::where('agence_id', $this->agenceId)
            ->orderBy('date_cloture', 'desc')
            ->paginate(10);

        $journeeOuverte = CloturesComptable::where('agence_id', $this->agenceId)
            ->where('statut', 'ouverte')
            ->exists();

        return view('livewire.clotures.clotures-list', [
            'clotures' => $clotures,
            'journeeOuverte' => $journeeOuverte,
        ]);
    }
}