<?php

namespace App\Livewire\Membres;

use Livewire\Component;
use App\Models\Membre;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class ChangePassword extends Component
{
    public Membre $membre;
    
    // Initialisation explicite pour éviter les erreurs de type "null"
    #[Validate]
    public string $current_password = '';
    #[Validate]
    public string $password = '';
    #[Validate]
    public string $password_confirmation = '';

    public function mount(Membre $membre)
    {
        if (auth()->id() !== $membre->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce mot de passe.');
        }
        $this->membre = $membre;
    }

    protected function rules()
    {
        return [
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('Le mot de passe actuel est incorrect.');
                }
            }],
            'password' => [
                'required', 
                'confirmed', 
                'different:current_password', 
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }

    protected function messages()
    {
        return [
            'current_password.required' => 'Veuillez saisir votre mot de passe actuel.',
            'password.required' => 'Veuillez définir un nouveau mot de passe.',
            'password.confirmed' => 'Les deux nouveaux mots de passe ne sont pas identiques.',
            'password.different' => 'Le nouveau mot de passe doit être différent de l\'ancien.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.letters' => 'Le mot de passe doit contenir au moins une lettre.',
            // Correction ici : mixedCase au lieu de mixed
            'password.mixedCase' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols' => 'Le mot de passe doit contenir au moins un caractère spécial.',
            'password.uncompromised' => 'Ce mot de passe est peu sûr (fuite de données), choisissez-en un autre.',
        ];
    }

    public function updatePassword()
    {
        $this->validate();

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('success', 'Votre mot de passe a été mis à jour avec succès.');

        return redirect()->route('membre.show');
    }

    public function render()
    {
        return view('livewire.membres.change-password');
    }
}