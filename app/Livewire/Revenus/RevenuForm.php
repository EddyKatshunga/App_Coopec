<?php

namespace App\Livewire\Revenus;

use App\Models\Revenu;
use App\Models\TypesRevenu;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RevenuForm extends Component
{
    public $montant, $monnaie = 'USD', $libelle, $reference, $description, $types_revenu_id;

    protected $rules = [
        'montant' => 'required|numeric|min:0.01',
        'monnaie' => 'required|string|max:3',
        'libelle' => 'required|string|min:3',
        'types_revenu_id' => 'required|exists:types_revenus,id',
        'reference' => 'nullable|string',
        'description' => 'nullable|string',
    ];

    public function save()
    {
        $validated = $this->validate();
        $validated['date_operation'] = now();

        Revenu::create($validated);

        session()->flash('message', 'Revenu enregistré et ajouté au coffre.');
        return redirect()->route('revenus.index');
    }

    public function render()
    {
        return view('livewire.revenus.revenu-form', [
            'types' => TypesRevenu::all()
        ]);
    }
}