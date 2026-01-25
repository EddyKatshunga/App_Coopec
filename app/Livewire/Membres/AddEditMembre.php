<?php

namespace App\Livewire\Membres;

use Livewire\Component;
use App\Models\Membre;
use App\Services\MembreService;
use App\Http\Requests\StoreMembreRequest;
use Illuminate\Validation\Rule;

class AddEditMembre extends Component
{
    public ?Membre $membre = null;
    public bool $isEdit = false;

    // Infos utilisateur
    public ?string $nom_complet = '';
    public ?string $email = '';
    public ?string $password = 'password123';

    // Infos membre
    public ?string $numero_identification = '';
    public ?string $sexe = 'M';
    public ?string $lieu_de_naissance = 'KIKWIT';
    public ?string $date_de_naissance = '';
    public ?string $qualite = 'Auxiliaire';
    public ?string $adresse = 'RDC';
    public ?string $telephone = '667788';
    public ?string $activites = 'Vente divers';
    public ?string $adresse_activite = 'Kikwit';
    public ?string $date_adhesion;

    public function mount(?Membre $membre = null)
    {
        $this->date_adhesion = now()->toDateString();

        if ($membre) {
            $this->isEdit = true;
            $this->membre = $membre;

            $this->fill([
                'nom_complet'           => $membre->nom,
                'email'                 => $membre->email,
                'numero_identification' => $membre->numero_identification,
                'sexe'                  => $membre->sexe,
                'lieu_de_naissance'     => $membre->lieu_de_naissance,
                'date_de_naissance'     => optional($membre->date_de_naissance)->format('Y-m-d'),
                'qualite'               => $membre->qualite,
                'adresse'               => $membre->adresse,
                'telephone'             => $membre->telephone,
                'activites'             => $membre->activites,
                'adresse_activite'      => $membre->adresse_activite,
                'date_adhesion'         => optional($membre->date_adhesion)->format('Y-m-d'),
            ]);
        }
    }

    protected function rules(): array
    {
        $rules = (new StoreMembreRequest())->rules();

        if ($this->isEdit) {
            // Email unique sauf utilisateur courant
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->membre->user_id),
            ];

            // Numéro d'identification : NON modifiable
            unset($rules['numero_identification']);

            // Mot de passe facultatif en édition
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        return $rules;
    }

    public function save(MembreService $membreService)
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            $membreService->updateMembre($this->membre->id, $validated);

            return redirect()
                ->route('membre.index')
                ->with('success', '✅ Membre modifié avec succès.');
        }

        $membreService->createMembre($validated);

        return redirect()
            ->route('membre.index')
            ->with('success', '✅ Membre créé avec succès.');
    }

    public function render()
    {
        return view('livewire.membres.addedit-membre');
    }
}
