<?php

namespace App\Livewire\Account;

use App\Models\Agence;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Carbon;

#[Layout('layouts.app')]
class JournalIndex extends Component
{
    use WithPagination;

    public $date_debut;
    public $date_fin;
    public $agence_id = null;

    // Gestion des droits
    public $isSuperAdmin = false;

    protected $queryString = ['date_debut', 'date_fin', 'agence_id'];

    public function mount()
    {
        $user = Auth::user();
        // Vérifier la permission Spatie "can.level6"
        $this->isSuperAdmin = $user->hasPermissionTo('can.level6');

        // Initialisation des dates à aujourd'hui
        $this->date_debut = now()->format('Y-m-d');
        $this->date_fin = now()->format('Y-m-d');

        if ($this->isSuperAdmin) {
            // Super admin : agence par défaut = son agence (si existante), sinon la première agence de la base
            if ($user->agence_id) {
                $this->agence_id = $user->agence_id;
            } else {
                $firstAgence = Agence::first();
                $this->agence_id = $firstAgence ? $firstAgence->id : null;
            }
        } else {
            // Non super admin : son agence est fixée (pas de choix en vue)
            $this->agence_id = $user->agence_id;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateDebut()
    {
        $this->resetPage();
    }

    public function updatingDateFin()
    {
        $this->resetPage();
    }

    public function updatingAgenceId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = JournalEntry::with(['agence', 'lines']);

        // Filtre par agence (obligatoire)
        if ($this->agence_id) {
            $query->where('agence_id', $this->agence_id);
        } elseif (!$this->isSuperAdmin) {
            // Sécurité : même si l'utilisateur n'a pas d'agence, il ne voit rien
            $query->whereRaw('1 = 0');
        }

        // Filtre par plage de dates
        if ($this->date_debut && $this->date_fin) {
            $query->whereBetween('date_operation', [
                    Carbon::parse($this->date_debut)->startOfDay(),
                    Carbon::parse($this->date_fin)->endOfDay()
                ]);
        }

        $entries = $query->orderBy('date_operation', 'desc')
                         ->paginate(15);

        // Récupération des agences pour le super admin
        $agences = $this->isSuperAdmin ? Agence::orderBy('nom')->get() : collect();

        return view('livewire.account.journal-index', [
            'entries'   => $entries,
            'agences'   => $agences,
        ]);
    }
}