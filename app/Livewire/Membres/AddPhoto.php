<?php

namespace App\Livewire\Membres;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Photo;

class AddPhoto extends Component
{
    use WithFileUploads;

    public User $user;
    public array $photos = [];

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function save()
    {
        $this->validate([
            'photos.*' => 'image|max:2048', // 2MB max
        ]);

        foreach ($this->photos as $photo) {
            $path = $photo->store('users/photos', 'public');

            Photo::create([
                'user_id'       => $this->user->id,
                'path'          => $path,
                'original_name' => $photo->getClientOriginalName(),
                'mime_type'     => $photo->getMimeType(),
                'size'          => $photo->getSize(),
                'disk'          => 'public',
            ]);
        }

        $this->dispatch('photo-added');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.membres.add-photo');
    }
}
