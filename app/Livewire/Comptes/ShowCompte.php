<?php

namespace App\Livewire\Comptes;

use Livewire\Component;
use App\Models\Compte;
use Carbon\Carbon;
use App\Services\CompteReleveService;
use App\Pdf\Generators\ReleveComptePdf;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ShowCompte extends Component
{
    public Compte $compte;

    public float $totalDepotCDF = 0;
    public float $totalRetraitCDF = 0;
    public float $totalDepotUSD = 0;
    public float $totalRetraitUSD = 0;

    public $dateDebut;
    public $dateFin;

    public $pdfUrl; // Pour l'aperçu

    public function mount(Compte $compte)
    {
        $this->compte = $compte->load(['membre', 'transactions' => fn($q) => $q->orderBy('date_transaction')]);

        $this->calculerStatistiques();

        // Dates par défaut : mois courant
        $this->dateDebut = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateFin = Carbon::now()->format('Y-m-d');

        $this->updatePdfPreview();
    }

    private function calculerStatistiques(): void
    {
        $this->totalDepotCDF = $this->compte->transactions->where('monnaie', 'CDF')->where('type_transaction', 'DEPOT')->sum('montant');
        $this->totalRetraitCDF = $this->compte->transactions->where('monnaie', 'CDF')->where('type_transaction', 'RETRAIT')->sum('montant');
        $this->totalDepotUSD = $this->compte->transactions->where('monnaie', 'USD')->where('type_transaction', 'DEPOT')->sum('montant');
        $this->totalRetraitUSD = $this->compte->transactions->where('monnaie', 'USD')->where('type_transaction', 'RETRAIT')->sum('montant');
    }

    // Génère une URL temporaire pour l'aperçu PDF
    public function updatePdfPreview()
    {
        $service = new CompteReleveService($this->compte);

        $data = $service->getReleveData(Carbon::parse($this->dateDebut), Carbon::parse($this->dateFin));

        $pdf = new ReleveComptePdf($data);

        // Génère le PDF et stocke dans /storage/app/public/temp pour l'aperçu
        $filename = "releve_preview_{$this->compte->id}.pdf";
        $path = storage_path("app/public/temp/{$filename}");
        $pdf->generate()->save($path);

        $this->pdfUrl = asset("storage/temp/{$filename}");
    }

    // Téléchargement final
    public function downloadReleve()
    {
        $service = new CompteReleveService($this->compte);
        $data = $service->getReleveData(Carbon::parse($this->dateDebut), Carbon::parse($this->dateFin));
        $pdf = new ReleveComptePdf($data);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->generate()->output();
        }, "Releve_Compte_{$this->compte->numero_compte}.pdf");
    }

    public function render()
    {
        return view('livewire.comptes.show-compte');
    }
}
