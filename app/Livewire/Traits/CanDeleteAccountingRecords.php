<?php

namespace App\Livewire\Traits;

trait CanDeleteAccountingRecords
{
    public function deleteRecord($modelClass, $id)
    {
        try {
            $record = $modelClass::findOrFail($id);

            // La sécurité Spatie (à adapter selon vos noms de permissions)
            if (!auth()->user()->can('can.level4')) { //Une permission spécifique au sup et chef d'agence
                throw new \Exception("Vous n'avez pas la permission de supprimer.");
            }

            // La suppression déclenchera le 'deleting' event
            $record->delete();

            session()->flash('success', 'Suppression effectuée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}