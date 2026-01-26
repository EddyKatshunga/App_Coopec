<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Compte;
use App\Services\CompteReleveService;
use App\Pdf\Generators\ReleveComptePdf;
use Carbon\Carbon;

class ReleveComptePdfController extends Controller
{
    public function download(Compte $compte)
    {
        $dateDebut = request()->query('date_debut') 
            ? Carbon::parse(request()->query('date_debut')) 
            : Carbon::now()->startOfMonth();
        $dateFin = request()->query('date_fin') 
            ? Carbon::parse(request()->query('date_fin')) 
            : Carbon::now();

        $service = new CompteReleveService($compte);
        $data = $service->getReleveData($dateDebut, $dateFin);

        $pdf = new ReleveComptePdf($data);

        return $pdf->generate()->download("Releve_Compte_{$compte->numero_compte}.pdf");
    }
}
