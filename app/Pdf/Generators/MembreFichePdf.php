<?php

namespace App\Pdf\Generators;

use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;
use App\Models\Membre;
use App\Services\MembreService;
use App\Pdf\Contracts\PdfGeneratorInterface;

class MembreFichePdf implements PdfGeneratorInterface
{
    public function __construct(
        protected Membre $membre,
        protected MembreService $service
    ) {}

    public function generate(): PDF
    {
        $data = $this->service->getFicheData($this->membre);

        return PdfFacade::loadView('pdf.membre.fiche', $data)
            ->setPaper('A4');
    }

    public function filename(): string
    {
        return 'fiche-membre-' . $this->membre->id . '.pdf';
    }
}
