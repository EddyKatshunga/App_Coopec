<?php

namespace App\Livewire\Photos;

use App\Models\Photo;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class PhotoForm extends Component
{
    use WithFileUploads;

    public ?User $user = null;
    public ?Photo $photo = null;

    public $file;
    public $caption;
    public $is_profile = false;

    public function mount(?User $user = null, ?Photo $photo = null)
    {
        if ($photo && $photo->exists) {
            $this->photo = $photo;
            $this->user = $photo->user;
            $this->caption = $photo->caption;
            $this->is_profile = $photo->is_profile;
        } else {
            $this->user = $user;
        }
    }

    /**
     * Définition des messages d'erreur personnalisés en français
     */
    protected function messages()
    {
        return [
            'file.required' => 'Veuillez sélectionner une image.',
            'file.image'    => 'Le fichier doit être une image (jpg, png, webp, etc.).',
            'file.max'      => 'L\'image est trop volumineuse (maximum 4 Mo).',
            'caption.max'   => 'La légende ne peut pas dépasser 255 caractères.',
        ];
    }

    public function save()
    {
        $rules = [
            'caption'    => 'nullable|string|max:255',
            'is_profile' => 'boolean',
        ];

        if (!$this->photo) {
            $rules['file'] = 'required|image|max:4096';
        }

        // La méthode validate() utilisera automatiquement les messages définis ci-dessus
        $this->validate($rules);

        if ($this->photo) {
            $this->photo->update([
                'caption' => $this->caption,
            ]);
            
            if ($this->is_profile) {
                $this->photo->setAsProfile();
            }
            
            session()->flash('message', 'Photo mise à jour avec succès.');
        } else {
            $path = $this->file->store('photos', 'public');

            $newPhoto = Photo::create([
                'user_id'       => $this->user->id,
                'path'          => $path,
                'original_name' => $this->file->getClientOriginalName(),
                'mime_type'     => $this->file->getMimeType(),
                'size'          => $this->file->getSize(),
                'caption'       => $this->caption,
                'disk'          => 'public',
                'is_profile'    => $this->is_profile,
            ]);

            if ($this->is_profile) {
                $newPhoto->setAsProfile();
            }

            session()->flash('message', 'Photo ajoutée avec succès.');
        }

        return redirect()->route('photos.index', ['user' => $this->user->id]);
    }

    public function render()
    {
        return view('livewire.photos.photo-form')
            ->layout('layouts.app');
    }
}