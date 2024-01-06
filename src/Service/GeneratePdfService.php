<?php

namespace App\Service;
 
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface GeneratePdfInterface
{
    public function createPdf(array $context): bool;
}

class GeneratePdfService implements GeneratePdfInterface
{
    private string $dirname;
    private Pdf $pdf;
     
    public function __construct(
        string $dirname,
        Pdf $pdf
    )
    {
        $this->dirname = $dirname;
        $this->pdf = $pdf;
    }
 
    public function createPdf(array $context): bool
    {
        $file_path = $this->dirname . "/filename_1.pdf"; // Modifier le nom du pdf pour chaque génération (à toi de voir comment tu veux le faire)
        $this->pdf->generateFromHtml($this->twig->render('Pdf/your_tig_template.twig', // Ton template représentant le pdf à générer
            ['context' => $context, // Quelques variables à passer au template twig
            ]), $file_path); // Chemin où extraire le PDF une fois généré

            return true;
    }

}