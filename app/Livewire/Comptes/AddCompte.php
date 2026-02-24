<?php

namespace App\Livewire\Comptes;

use Livewire\Component;
use App\Models\Compte;
use App\Models\Membre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AddCompte extends Component
{
    public $membre_id;
    public $intitule;

    protected $rules = [
        'membre_id' => 'required|exists:membres,id',
        'intitule'  => 'required|string|max:255',
    ];

    public function mount($membre)
    {
        $this->membre_id = $membre->id ?? $membre;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {

            $membre = Membre::findOrFail($this->membre_id);
            // Nombre de comptes existants pour ce membre
            $count = Compte::where('membre_id', $membre->id)->count();

            // Génération du numéro de compte : IDMEMBRE-01, 02, 03...
            $suffix = str_pad($count, 2, '0', STR_PAD_LEFT);
            $numero_compte = $membre->numero_identification . '-' . $suffix;

            Compte::create([
                'membre_id'     => $membre->id,
                'user_id' => $membre->user->id,
                'intitule'      => $this->intitule,
                'numero_compte' => $numero_compte,
                'solde_cdf'     => 0,
                'solde_usd'     => 0,
            ]);
        });

        session()->flash('success', 'Compte épargne ajouté avec succès.');

        return redirect()->route('membre.show', $this->membre_id);
    }

    public function render()
    {
        return view('livewire.comptes.add-compte');
    }
}
