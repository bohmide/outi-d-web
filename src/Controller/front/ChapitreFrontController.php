<?php

namespace App\Controller\front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ChapitreRepository;
use App\Entity\Cours;
use App\Entity\Chapitre;

final class ChapitreFrontController extends AbstractController
{
    #[Route('/cour/{id}/chapitre', name: 'app_chapitre_front')]
    public function index(Cours $cours, ChapitreRepository $chapitreRepository): Response
    {
        // Récupérer les chapitres du cours spécifique
        $chapitres = $chapitreRepository->findBy(['cours' => $cours]);

        return $this->render('front/chapitre/showChapitre.html.twig', [
            'cours' => $cours,
            'chapitres' => $chapitres,
        ]);
    }
    #[Route('/chapitre/{id}', name: 'show_chapitre_details')]
     public function showDetails(Chapitre $chapitre): Response
{
    return $this->render('front/chapitre/showChapitreDetails.html.twig', [
        'chapitre' => $chapitre,
    ]);
}

}
