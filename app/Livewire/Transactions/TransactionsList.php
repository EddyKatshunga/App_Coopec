<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;

class TransactionsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $agenceId;

    public function mount()
    {
        $this->agenceId = 1;
    }

    public function render()
    {
        $transactions = Transaction::with([
                'compte.user',
                'agent_collecteur'
            ])
            ->where('agence_id', $this->agenceId)
            ->orderBy('created_at', 'asc') // 👈 chronologie comptable
            ->paginate(20);

        return view('livewire.transactions.transactions-list', [
            'transactions' => $transactions,
        ])->layout('layouts.app');
    }
}
