<?php

namespace App\Livewire\Credits;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Credit;
use App\Models\Zone;
use App\Models\User;
use Carbon\Carbon;

class CreditsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    /* ================= FILTRES ================= */
    public $search = '';
    public $zone_id = null;
    public $membre_id = null;
    public $statut = null;
    public $date_debut = null;
    public $date_fin = null;
    public $negocie = null;

    public $zones;
    public $membres;

    public function mount()
    {
        $this->zones = Zone::orderBy('nom')->get();
        $this->membres = User::where('role', 'membre')->orderBy('name')->get();
    }

    public function updating($property)
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Credit::query()->with('remboursements', 'membre', 'zone');

        /* ================= FILTRE RECHERCHE ================= */
        if ($this->search) {
            $query->where('numero_credit', 'like', "%{$this->search}%")
                  ->orWhereHas('membre', function ($q) {
                      $q->where('name', 'like', "%{$this->search}%");
                  });
        }

        /* ================= FILTRE ZONE ================= */
        if ($this->zone_id) {
            $query->where('zone_id', $this->zone_id);
        }

        /* ================= FILTRE MEMBRE ================= */
        if ($this->membre_id) {
            $query->where('membre_id', $this->membre_id);
        }

        /* ================= FILTRE STATUT ================= */
        if ($this->statut) {
            $query->get()->filter(function ($credit) {
                return $credit->statut === $this->statut;
            });
        }

        /* ================= FILTRE NÉGOCIÉ ================= */
        if (!is_null($this->negocie)) {
            $query->where('negocie', $this->negocie);
        }

        /* ================= FILTRE DATE ================= */
        if ($this->date_debut) {
            $query->whereDate('date_credit', '>=', Carbon::parse($this->date_debut));
        }
        if ($this->date_fin) {
            $query->whereDate('date_credit', '<=', Carbon::parse($this->date_fin));
        }

        $credits = $query->orderBy('date_credit', 'desc')->paginate(15);

        return view('livewire.credits.credits-list', compact('credits'));
    }
}
