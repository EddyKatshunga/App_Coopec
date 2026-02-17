<?php

namespace App\Livewire\Agence;

use Livewire\Component;
use App\Models\Agence;
use Illuminate\Support\Facades\DB;

class AgenceForm extends Component
{
    public ?Agence $agence = null;

    public $nom;
    public $code;
    public $ville;
    public $pays;
    public $solde_initial_coffre;

    public $isEdit = false;

    protected function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:agences,code,' . ($this->agence->id ?? 'NULL'),
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'solde_initial_coffre' => $this->isEdit ? 'nullable' : 'required|numeric|min:0'
        ];
    }

    public function mount(?Agence $agence = null)
    {
        if ($agence) {
            $this->isEdit = true;
            $this->agence = $agence;

            $this->nom = $agence->nom;
            $this->code = $agence->code;
            $this->ville = $agence->ville;
            $this->pays = $agence->pays;
        }
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {

            if ($this->isEdit) {
                $this->agence->update([
                    'nom' => $this->nom,
                    'code' => $this->code,
                    'ville' => $this->ville,
                    'pays' => $this->pays,
                ]);
            } else {
                Agence::create([
                    'nom' => $this->nom,
                    'code' => $this->code,
                    'ville' => $this->ville,
                    'pays' => $this->pays,
                    'directeur_id' => null,
                    'solde_actuel_coffre' => $this->solde_initial_coffre,
                    'solde_actuel_epargne' => 0,
                ]);
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
