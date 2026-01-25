<?php
namespace App\Pdf;

use App\Pdf\Contracts\PdfGeneratorInterface;

class PdfManager
{
    public function download(PdfGeneratorInterface $generator)
    {
        return $generator->generate()
            ->download($generator->filename());
    }

    public function stream(PdfGeneratorInterface $generator)
    {
        return $generator->generate()
            ->stream($generator->filename());
    }
}
