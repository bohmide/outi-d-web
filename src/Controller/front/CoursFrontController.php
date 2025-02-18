<?php

namespace App\Controller\front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CoursRepository;
use App\Entity\Cours;
final class CoursFrontController extends AbstractController
{
    #[Route('/cours', name: 'app_showcours')]
    public function index(CoursRepository $coursRepository): Response
    {
        // Récupérer tous les cours pour les afficher
        $cours = $coursRepository->findAll();

        return $this->render('front/cours/showCours.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/{id}', name: 'front_cours_show')]
    public function show(Cours $cours): Response
    {
        // Afficher les détails d'un cours spécifique
        return $this->render('front/cours/showCoursDetails.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/{id}/inscrire', name: 'front_cours_inscrire')]
    public function inscrire(Cours $cours): Response
    {
        // Logique pour inscrire l'utilisateur au cours

        $this->addFlash('success', 'Vous êtes inscrit au cours avec succès.');
        return $this->redirectToRoute('app_chapitre_front', ['id' => $cours->getId()]);
    }
}
