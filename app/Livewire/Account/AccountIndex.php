<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AccountIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';

    protected $queryString = ['search', 'typeFilter'];

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $accounts = Account::with('parent')
            ->when($this->search, function($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('numero', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy('numero')
            ->get();

        return view('livewire.account.account-index', [
            'accounts' => $accounts
        ]);
    }
}