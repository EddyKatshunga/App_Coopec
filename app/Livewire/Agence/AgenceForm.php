<?php

namespace App\Livewire\Agence;

use Livewire\Component;
use App\Models\Agence;
use App\Models\Agent; // N'oubliez pas l'import pour le select
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AgenceForm extends Component
{
    public ?Agence $agence = null;

    // Propriétés du formulaire
    public $nom, $code, $ville, $pays;

    public bool $isEdit = false;

    public function rules()
    {
        // On passe l'ID de l'agence (si elle existe) pour l'exception "unique"
        return Agence::rules($this->agence?->id);
    }

    public function mount(?Agence $agence = null)
    {
        if ($agence && $agence->exists) {
            $this->isEdit = true;
            $this->agence = $agence;

            // Remplissage des champs pour l'édition
            $this->nom = $agence->nom;
            $this->code = $agence->code;
            $this->ville = $agence->ville;
            $this->pays = $agence->pays;
        }
    }

    public function save()
    {
        // Valide selon les règles du Model
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            if ($this->isEdit) {
                $this->agence->update($validated);
            } else {
                Agence::create($validated);
            }
        });

        session()->flash('success', 'Agence enregistrée avec succès.');
        return redirect()->route('agences.index');
    }

    public function render()
    {
        return view('livewire.agence.agence-form');
    }
}