<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CloturesShow extends Component
{
    public CloturesComptable $cloture;

    public function render()
    {
        // 1. Dépôts : Agent -> Monnaie
        $depotsGroupes = $this->cloture->depots()
            ->with('agent_collecteur.user')
            ->get()->groupBy(['agent_collecteur_id', 'monnaie']);

        // 2. Retraits : Créateur -> Monnaie
        $retraitsGroupes = $this->cloture->retraits()
            ->with('creator')
            ->get()->groupBy(['created_by', 'monnaie']);

        // 3. Revenus : Type -> Monnaie
        $revenusGroupes = $this->cloture->revenus()
            ->with('typeRevenu')
            ->get()->groupBy(['types_revenu_id', 'monnaie']);

        // 4. Dépenses : Type -> Monnaie
        $depensesGroupes = $this->cloture->depenses()
            ->with('typeDepense')
            ->get()->groupBy(['types_depense_id', 'monnaie']);

        // 5. Crédits, Remboursements, Intérêts : Zone -> Monnaie
        // Note : On utilise la même logique de regroupement par Zone
        $creditsGroupes = $this->cloture->credits()
            ->with('zone')
            ->get()->groupBy(['zone_id', 'monnaie']);

        $remboursementsGroupes = $this->cloture->remboursements()
            ->with('zone')
            ->get()->groupBy(['zone_id', 'monnaie']);

        return view('livewire.clotures.clotures-show', [
            'depotsGroupes' => $depotsGroupes,
            'retraitsGroupes' => $retraitsGroupes,
            'revenusGroupes' => $revenusGroupes,
            'depensesGroupes' => $depensesGroupes,
            'creditsGroupes' => $creditsGroupes,
            'remboursementsGroupes' => $remboursementsGroupes,
        ]);
    }
}