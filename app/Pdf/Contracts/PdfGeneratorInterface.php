<?php
namespace App\Pdf\Contracts;

use Barryvdh\DomPDF\PDF;

interface PdfGeneratorInterface
{
    public function generate(): PDF;

    public function filename(): string;
}
