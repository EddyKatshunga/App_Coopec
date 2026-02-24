<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TransactionShow extends Component
{
    public Transaction $transaction;

    public function mount(Transaction $transaction)
    {
        // Chargement des relations nécessaires pour éviter les requêtes N+1
        $this->transaction = $transaction->load([
            'compte.user', 
            'compte.membre', 
            'agent_collecteur.user', 
            'agence'
        ]);
    }

    public function render()
    {
        return view('livewire.transactions.transaction-show');
    }
}