<?php

namespace App\Livewire\Membres;

use Livewire\Component;
use App\Models\Membre;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class ChangePassword extends Component
{
    public Membre $membre;
    
    public string $current_password = '';
    
    #[Validate('required|digits:6|confirmed|different:current_password')]
    public string $password = '';
    
    public string $password_confirmation = '';

    public function mount(Membre $membre)
    {
        $this->authorize('update', $this->membre);
        $this->membre = $membre;
    }

    protected function rules()
    {
        return [
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('Mot de passe incorrect.');
                }
            }],
        ];
    }

    protected function messages()
    {
        return [
            'current_password.required' => 'Saisissez votre code.',
            'password.required' => 'Définissez un nouveau code.',
            'password.digits' => 'Le code doit contenir exactement 6 chiffres.',
            'password.confirmed' => 'Les deux codes ne sont pas identiques.',
            'password.different' => 'Le nouveau code doit être différent de l\'ancien.',
        ];
    }

    public function updatePassword()
    {
        $this->validate();

        $this->membre->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('success', 'Votre code de sécurité a été mis à jour.');

        return redirect()->route('membre.show', $this->membre);
    }

    public function render()
    {
        return view('livewire.membres.change-password');
    }
}