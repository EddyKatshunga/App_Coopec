<?php

namespace App\Http\Controllers\Pdf;

use App\Models\Membre;
use App\Pdf\PdfManager;
use App\Services\MembreService;
use App\Pdf\Generators\MembreFichePdf;
use App\Pdf\Generators\MembreListPdf; // Importation du nouveau générateur
use App\Http\Controllers\Controller;

class MembrePdfController extends Controller
{
    /**
     * Télécharge la fiche individuelle d'un membre.
     */
    public function fiche(
        Membre $membre, 
        PdfManager $manager, 
        MembreService $service
    ) {
        $pdf = new MembreFichePdf($membre, $service);

        return $manager->download($pdf);
    }

    /**
     * Télécharge la liste complète des membres.
     */
    public function index(
        PdfManager $manager, 
        MembreService $service
    ) {
        // On instancie le générateur de liste que nous avons analysé précédemment
        $pdf = new MembreListPdf($service);

        return $manager->download($pdf);
    }
}