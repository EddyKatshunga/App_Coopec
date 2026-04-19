<?php

namespace App\Livewire\Depenses;

use App\Models\Depense;
use App\Models\TypesDepense;
use App\Models\Agent;
use App\Models\Traits\HasAgenceContext;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DepenseForm extends Component
{
    use HasAgenceContext;

    public $montant, $monnaie = 'CDF', $libelle, $reference, $description, $types_depense_id, $beneficiaire_id;

    protected $rules = [
        'montant' => 'required|numeric|min:0.01',
        'monnaie' => 'required|in:CDF,USD',
        'libelle' => 'required|string|min:3',
        'types_depense_id' => 'required|exists:types_depenses,id',
        'beneficiaire_id' => 'required|exists:agents,id',
    ];

    public function mount()
    {
        $this->secureAgenceContext();
        $this->secureJourneeContext();
    }

    public function save()
    {
        $validatedData = $this->validate();

        Depense::create($validatedData);

        session()->flash('message', 'Dépense enregistrée.');
        return redirect()->to('/depenses');
    }

    public function render()
    {
        return view('livewire.depenses.depense-form', [
            'types' => TypesDepense::all(),
            'agents' => Agent::all()
        ]);
    }
}