<?php

namespace App\Pdf\Generators;

use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;
use App\Models\Membre;
use App\Services\MembreService;
use App\Pdf\Contracts\PdfGeneratorInterface;

class MembreListPdf implements PdfGeneratorInterface
{
    public function __construct(
        protected MembreService $service
    ) {}

    public function generate(): PDF
    {
        $data = $this->service->getMembreData();

        return PdfFacade::loadView('pdf.membre.list', $data)
            ->setPaper('A4');
    }

    public function filename(): string
    {
        // Format plus sûr : liste-membres-2026-02-23_19-30.pdf
        return 'liste-membres-' . now()->format('Y-m-d_H-i') . '.pdf';
    }
}
