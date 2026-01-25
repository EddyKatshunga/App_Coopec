<?php
namespace App\Http\Controllers\Pdf;

use App\Models\Membre;
use App\Pdf\PdfManager;
use App\Services\MembreService;
use App\Pdf\Generators\MembreFichePdf;

class MembrePdfController
{
    public function __invoke(
        Membre $membre,
        PdfManager $manager,
        MembreService $service
    ) {
        $pdf = new MembreFichePdf($membre, $service);

        return $manager->download($pdf);
    }
}
