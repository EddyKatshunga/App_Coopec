<?php

namespace App\Livewire\Admin;

use App\Models\Agence;
use App\Models\Credit;
use App\Models\CreditRemboursement;
use App\Models\Membre;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PerformanceSuivi extends Component
{
    public $agence_id = '';
    public $date_debut = '';
    public $date_fin = '';

    public function mount()
    {
        $this->date_debut = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_fin = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $agences = Agence::orderBy('nom')->get();

        $topAgentsEpargne = [];
        $topZonesCredits = [];
        $topZonesRemboursements = [];
        $topAgentsParrainage = [];

        if ($this->agence_id && $this->date_debut && $this->date_fin) {
            $debut = Carbon::parse($this->date_debut)->startOfDay();
            $fin   = Carbon::parse($this->date_fin)->endOfDay();

            $topAgentsEpargne = $this->getTopAgentsEpargne($debut, $fin);
            $topZonesCredits = $this->getTopZonesCredits($debut, $fin);
            $topZonesRemboursements = $this->getTopZonesRemboursements($debut, $fin);
            $topAgentsParrainage = $this->getTopAgentsParrainage($debut, $fin);
        }

        return view('livewire.admin.performance-suivi', [
            'agences' => $agences,
            'topAgentsEpargne' => $topAgentsEpargne,
            'topZonesCredits' => $topZonesCredits,
            'topZonesRemboursements' => $topZonesRemboursements,
            'topAgentsParrainage' => $topAgentsParrainage,
        ]);
    }

    private function getTopAgentsEpargne($debut, $fin)
    {
        $transactions = Transaction::with('agent_collecteur')
            ->where('agence_id', $this->agence_id)
            ->where('type_transaction', 'DEPOT')
            ->whereBetween('date_transaction', [$debut, $fin])
            ->get();

        $agentsData = [];
        foreach ($transactions as $t) {
            $agentId = $t->agent_collecteur_id;
            if (!$agentId) continue;

            if (!isset($agentsData[$agentId])) {
                $agentsData[$agentId] = [
                    'agent' => $t->agent_collecteur,
                    'total_cdf' => 0,
                    'total_usd' => 0,
                    'nbre_ops' => 0,
                ];
            }
            if ($t->monnaie === 'CDF') {
                $agentsData[$agentId]['total_cdf'] += (float) $t->montant;
            } else {
                $agentsData[$agentId]['total_usd'] += (float) $t->montant;
            }
            $agentsData[$agentId]['nbre_ops']++;
        }

        // Tri par somme (CDF+USD converti pour le classement, mais on garde les colonnes séparées)
        usort($agentsData, function($a, $b) {
            $totalA = $a['total_cdf'] + ($a['total_usd'] * 2500); // taux indicatif pour tri
            $totalB = $b['total_cdf'] + ($b['total_usd'] * 2500);
            return $totalB <=> $totalA;
        });

        return $agentsData;
    }

    private function getTopZonesCredits($debut, $fin)
    {
        $credits = Credit::with('zone.gerant')
            ->where('agence_id', $this->agence_id)
            ->whereBetween('date_credit', [$debut, $fin])
            ->get();

        $zonesData = [];
        foreach ($credits as $credit) {
            $zoneId = $credit->zone_id;
            if (!$zoneId) continue;

            if (!isset($zonesData[$zoneId])) {
                $zonesData[$zoneId] = [
                    'zone' => $credit->zone,
                    'chef' => $credit->zone?->gerant,
                    'capital_cdf' => 0,
                    'capital_usd' => 0,
                    'interets_cdf' => 0,
                    'interets_usd' => 0,
                    'nbre_credits' => 0,
                ];
            }
            if ($credit->monnaie === 'CDF') {
                $zonesData[$zoneId]['capital_cdf'] += (float) $credit->capital;
                $zonesData[$zoneId]['interets_cdf'] += (float) $credit->interet;
            } else {
                $zonesData[$zoneId]['capital_usd'] += (float) $credit->capital;
                $zonesData[$zoneId]['interets_usd'] += (float) $credit->interet;
            }
            $zonesData[$zoneId]['nbre_credits']++;
        }

        // Tri par capital total (CDF + USD converti)
        usort($zonesData, function($a, $b) {
            $totalA = $a['capital_cdf'] + ($a['capital_usd'] * 2500);
            $totalB = $b['capital_cdf'] + ($b['capital_usd'] * 2500);
            return $totalB <=> $totalA;
        });

        return $zonesData;
    }

    private function getTopZonesRemboursements($debut, $fin)
    {
        $remboursements = CreditRemboursement::with('zone.gerant')
            ->where('agence_id', $this->agence_id)
            ->whereBetween('date_paiement', [$debut, $fin])
            ->get();

        $zonesData = [];
        foreach ($remboursements as $remb) {
            $zoneId = $remb->zone_id;
            if (!$zoneId) continue;

            if (!isset($zonesData[$zoneId])) {
                $zonesData[$zoneId] = [
                    'zone' => $remb->zone,
                    'chef' => $remb->zone?->gerant,
                    'total_cdf' => 0,
                    'total_usd' => 0,
                    'nbre_remboursements' => 0,
                ];
            }
            if ($remb->monnaie === 'CDF') {
                $zonesData[$zoneId]['total_cdf'] += (float) $remb->montant;
            } else {
                $zonesData[$zoneId]['total_usd'] += (float) $remb->montant;
            }
            $zonesData[$zoneId]['nbre_remboursements']++;
        }

        usort($zonesData, function($a, $b) {
            $totalA = $a['total_cdf'] + ($a['total_usd'] * 2500);
            $totalB = $b['total_cdf'] + ($b['total_usd'] * 2500);
            return $totalB <=> $totalA;
        });

        return $zonesData;
    }

    private function getTopAgentsParrainage($debut, $fin)
    {
        $membres = Membre::with('agentParrain')
            ->where('agence_id', $this->agence_id)
            ->whereBetween('date_adhesion', [$debut, $fin])
            ->get();

        $agentsData = [];
        foreach ($membres as $membre) {
            $agentId = $membre->agent_parrain_id;
            if (!$agentId) continue;

            if (!isset($agentsData[$agentId])) {
                $agentsData[$agentId] = [
                    'agent' => $membre->agentParrain,
                    'total_membres' => 0,
                ];
            }
            $agentsData[$agentId]['total_membres']++;
        }

        usort($agentsData, fn($a, $b) => $b['total_membres'] <=> $a['total_membres']);

        return $agentsData;
    }
}