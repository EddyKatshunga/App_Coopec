<?php

namespace App\Livewire\Remboursements;

use App\Models\Agent;
use App\Models\Credit;
use App\Models\CreditRemboursement;
use App\Models\User;
use App\Services\CreditRemboursementService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RemboursementForm extends Component
{
    public Credit $credit;

    // Champs du formulaire
    public $montant;
    public $mode_paiement = 'cash';
    public $reference_paiement;
    public $agent_id;

    protected function rules()
    {
        return [
            'montant' => 'required|numeric|min:1',
            'mode_paiement' => 'required|in:cash,mpesa,airtel,banque',
            'reference_paiement' => 'nullable|string|max:100',
            'agent_id' => 'required|exists:users,id',
        ];
    }

    protected function messages()
    {
        return [
            'montant.min' => 'Le montant doit être supérieur à zéro.',
            'agent_id.required' => 'Veuillez sélectionner l\'agent qui perçoit les fonds.',
        ];
    }

    public function mount(Credit $credit)
    {
        $this->credit = $credit;
        $this->agent_id = auth()->user()->agent?->id;
    }

    public function save(CreditRemboursementService $service)
    {
        $data = $this->validate();

        try {
            $service->enregistrer($this->credit, $data);
            $message = "Remboursement enregistré avec succès.";

            session()->flash('message', $message);
            return redirect()->route('credit.show', $this->credit);

        } catch (\Exception $e) {
            $this->addError('montant', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.remboursements.remboursement-form', [
            'agents' => Agent::where('agence_id', auth()->user()->agence_id)->get() // Idéalement filtrer par agence
        ]);
    }
}