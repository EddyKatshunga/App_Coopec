<?php

namespace App\Livewire\Depenses;

use App\Models\TypesDepense;
use Livewire\Component;

class TypesDepenseForm extends Component
{
    public ?TypesDepense $typesDepense = null;

    public $nom = '';
    public $code_comptable = '';

    public function mount(?TypesDepense $typesDepense = null)
    {
        if ($typesDepense && $typesDepense->exists) {
            $this->typesDepense = $typesDepense;
            $this->nom = $typesDepense->nom;
            $this->code_comptable = $typesDepense->code_comptable;
        }
    }

    protected function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'code_comptable' => 'required|string|max:100|unique:types_depenses,code_comptable,' . ($this->typesDepense->id ?? 'NULL'),
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->typesDepense) {
            $this->typesDepense->update([
                'nom' => $this->nom,
                'code_comptable' => $this->code_comptable,
            ]);
            session()->flash('success', 'Type de dépense modifié avec succès.');
        } else {
            TypesDepense::create([
                'nom' => $this->nom,
                'code_comptable' => $this->code_comptable,
            ]);
            session()->flash('success', 'Type de dépense créé avec succès.');
        }

        return redirect()->route('types-depense.index');
    }

    public function render()
    {
        return view('livewire.depenses.types-depense-form')
            ->layout('layouts.app');
    }
}
