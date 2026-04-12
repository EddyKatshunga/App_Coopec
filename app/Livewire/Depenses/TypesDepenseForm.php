<?php

namespace App\Livewire\Depenses;

use App\Models\TypesDepense;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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
        // Récupère l'ID si on est en édition, sinon null pour la création
        $id = $this->typesDepense?->id;

        return [
            // Ajoute l'unicité ici aussi pour le champ 'nom'
            'nom' => "required|string|max:255|unique:types_depenses,nom,{$id}",
            
            'code_comptable' => "required|string|max:100|unique:types_depenses,code_comptable,{$id}",
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
        return view('livewire.depenses.types-depense-form');
    }
}
