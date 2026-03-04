<?php

namespace App\Livewire\Credits;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Credit;
use App\Models\Zone;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreditsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    /* ================= FILTRES ================= */
    public $search = '';
    public $zone_id = null;
    public $statut = null;
    public $date_debut = null;
    public $date_fin = null;
    public $negocie = null;

    public $zones;

    public function mount()
    {
        $this->zones = Zone::orderBy('nom')->get();
    }

    public function updating($property)
    {
        $this->resetPage();
    }

    public function render()
    {
        // On charge 'remboursements' pour que getSituationActuelle() soit rapide
        $query = Credit::query()->with(['user', 'zone', 'remboursements']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('numero_credit', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->zone_id) $query->where('zone_id', $this->zone_id);
        
        // Filtre de statut (Le statut est un attribut calculé ou stocké)
        if ($this->statut) $query->where('statut', $this->statut);

        if (!is_null($this->negocie)) $query->where('negocie', $this->negocie);
        
        if ($this->date_debut) $query->whereDate('date_credit', '>=', $this->date_debut);
        if ($this->date_fin) $query->whereDate('date_credit', '<=', $this->date_fin);
        
        $credits = $query->orderBy('date_credit', 'desc')->paginate(12);

        return view('livewire.credits.credits-list', compact('credits'));
    }
}