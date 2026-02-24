<?php

namespace App\Livewire\Photos;

use App\Models\Photo;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PhotoList extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function delete($id)
    {
        $photo = Photo::where('user_id', $this->user->id)->findOrFail($id);
        $photo->delete(); // Le hook "deleting" dans le modèle supprimera le fichier du disque
        session()->flash('message', 'Photo supprimée.');
    }

    public function setProfile($id)
    {
        $photo = Photo::findOrFail($id);
        $photo->setAsProfile();
    }

    public function render()
    {
        return view('livewire.photos.photo-list', [
            'photos' => Photo::where('user_id', $this->user->id)->latest()->paginate(12)
        ])->layout('layouts.app');
    }
}