<?php

namespace App\Livewire\Photos;

use App\Models\Photo;
use Livewire\Component;

class PhotoShow extends Component
{
    public Photo $photo;

    public function mount($id)
    {
        $this->photo = Photo::with('user')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.photos.photo-show')
            ->layout('layouts.app');
    }
}
