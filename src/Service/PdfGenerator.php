<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use App\Entity\Cours;

class PdfGenerator
{
    private string $pdfDirectory;

    public function __construct(string $pdfDirectory)
    {
        $this->pdfDirectory = $pdfDirectory;
    }

    public function generateCertificationPdf(Cours $cours, Environment $twig): string
    {
        // Générer le HTML en utilisant Twig
          $html = $twig->render('etudiantFrontCours/certification/certification.html.twig', [
            'cours' => $cours,
            'certificationName' => $cours->getNom()
        ]);

     // Initialisation de Dompdf
     $options = new Options();
     $options->set('defaultFont', 'Arial');
 
     $dompdf = new Dompdf($options);
     $dompdf->loadHtml($html);
     $dompdf->setPaper('A4', 'landscape');
     $dompdf->render();
 
     // Sauvegarde du fichier PDF
     if (!$cours->getId()) {
        throw new \Exception("Impossible de générer le PDF : l'ID du cours est manquant.");
    }
    
    // 🔹 Génère le chemin du fichier avec l'ID du cours
    $filePath = 'uploads/certifications/certif_' . $cours->getId() . '.pdf';
    file_put_contents($filePath, $dompdf->output());
    
    return $filePath;
 }
}
