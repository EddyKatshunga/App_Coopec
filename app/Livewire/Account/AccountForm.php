<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AccountForm extends Component
{
    public ?Account $account = null; // null pour création, rempli pour édition
    public $numero, $nom, $type, $parent_id, $est_actif = true;

    protected function rules()
    {
        return [
            'numero' => 'required|string|unique:accounts,numero,' . ($this->account->id ?? 'NULL'),
            'nom' => 'required|string|max:255',
            'type' => 'required|string',
            'parent_id' => 'nullable|exists:accounts,id',
            'est_actif' => 'boolean',
        ];
    }

    public function mount(Account $account = null)
    {
        if ($account && $account->exists) {
            $this->account = $account;
            $this->numero = $account->numero;
            $this->nom = $account->nom;
            $this->type = $account->type;
            $this->parent_id = $account->parent_id;
            $this->est_actif = $account->est_actif;
        }
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->account && $this->account->exists) {
            $this->account->update($data);
            session()->flash('message', 'Compte mis à jour avec succès.');
        } else {
            $data['uuid'] = (string) Str::uuid();
            Account::create($data);
            session()->flash('message', 'Compte créé avec succès.');
            return redirect()->route('accounts.index');
        }
    }

    public function render()
    {
        return view('livewire.account.account-form', [
            'availableParents' => Account::where('id', '!=', $this->account->id ?? 0)->orderBy('numero')->get()
        ]);
    }
}