<?php

namespace App\Pdf\Generators;

use App\Pdf\Contracts\PdfGeneratorInterface;
use Barryvdh\DomPDF\PDF;

class ReleveComptePdf implements PdfGeneratorInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function generate(): PDF
    {
        return app('dompdf.wrapper')->loadView('pdf.compte.releve', $this->data)
            ->setPaper('A4');
    }

    public function filename(): string
    {
        return 'releve-compte-' . $this->data['compte']->id . '.pdf';
    }
}
