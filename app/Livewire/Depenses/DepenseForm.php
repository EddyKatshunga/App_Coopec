<?php

namespace App\Livewire\Depenses;

use App\Models\Depense;
use App\Models\TypesDepense;
use App\Models\Agent;
use Livewire\Component;

class DepenseForm extends Component
{
    public $montant, $monnaie = 'USD', $libelle, $reference, $description, $types_depense_id, $beneficiaire_id;

    protected $rules = [
        'montant' => 'required|numeric|min:1',
        'monnaie' => 'required|string|max:3',
        'libelle' => 'required|string|min:3',
        'types_depense_id' => 'required|exists:types_depenses,id',
        'beneficiaire_id' => 'nullable|exists:agents,id',
    ];

    public function save()
    {
        $validatedData = $this->validate();
        $validatedData['date_operation'] = now(); // Selon votre getDateColumnName()

        Depense::create($validatedData);

        session()->flash('message', 'Dépense enregistrée.');
        return redirect()->to('/depenses');
    }

    public function render()
    {
        return view('livewire.depenses.depense-form', [
            'types' => TypesDepense::all(),
            'agents' => Agent::all()
        ])->layout('layouts.app');
    }
}