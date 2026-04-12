<?php

namespace App\Livewire\Revenus;

use App\Models\TypesRevenu;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TypesRevenuForm extends Component
{
    public ?TypesRevenu $typesRevenu = null;

    public $nom = '';
    public $code_comptable = '';

    public function mount(?TypesRevenu $typesRevenu = null)
    {
        if ($typesRevenu && $typesRevenu->exists) {
            $this->typesRevenu = $typesRevenu;
            $this->nom = $typesRevenu->nom;
            $this->code_comptable = $typesRevenu->code_comptable;
        }
    }

    protected function rules()
    {
        // On récupère l'ID si on est en édition, sinon null pour la création
        $id = $this->typesRevenu?->id;

        return [
            // Ajout de 'unique' sur le nom pour éviter l'erreur SQLSTATE[23000]
            'nom' => "required|string|max:255|unique:types_Revenus,nom,{$id}",
            
            // Correction de la syntaxe pour le code_comptable
            'code_comptable' => "required|string|max:100|unique:types_Revenus,code_comptable,{$id}",
        ];
    }


    public function save()
    {
        $this->validate();

        if ($this->typesRevenu) {
            $this->typesRevenu->update([
                'nom' => $this->nom,
                'code_comptable' => $this->code_comptable,
            ]);
            session()->flash('success', 'Type de dépense modifié avec succès.');
        } else {
            TypesRevenu::create([
                'nom' => $this->nom,
                'code_comptable' => $this->code_comptable,
            ]);
            session()->flash('success', 'Type de dépense créé avec succès.');
        }

        return redirect()->route('types-revenu.index');
    }

    public function render()
    {
        return view('livewire.revenus.types-revenu-form');
    }
}
