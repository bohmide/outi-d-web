<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Form\CompetitionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CompetitionRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class CompetitionController extends AbstractController
{
    
    #[Route('/addcompetition', name: 'addcompetition')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du fichier uploadé
            $file = $form->get('fichierFile')->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                $competition->setFichier($fileName);
            } 
            $entityManager->persist($competition);
            $entityManager->flush();

            $this->addFlash('success', 'La compétition a été publiée avec succès!');

            return $this->redirectToRoute('addcompetition');
        }

        return $this->render('competition/collaborateuraddcomp.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
    #[Route('/listcompetition', name: 'competitionlist')]
    public function list(CompetitionRepository $competitionRepository): Response
    {
        $competitions = $competitionRepository->findAll();

        return $this->render('competition/collaborateurmain.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    #[Route('/competition/{id}/delete', name: 'app_competition_delete', methods: ['POST'])]
    public function delete(Competition $competition, EntityManagerInterface $entityManager): Response
    {
        // Supprimer la compétition de la base de données
        $entityManager->remove($competition);
        $entityManager->flush();

        // Ajouter un message flash pour confirmer la suppression
        $this->addFlash('success', 'La compétition a été supprimée avec succès.');

        // Rediriger vers la liste des compétitions
        return $this->redirectToRoute('competitionlist');
    }


    #[Route('/competition/{id}/edit', name: 'app_competition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Competition $competition, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire de mise à jour
        $form = $this->createForm(CompetitionType::class, $competition);
        $mediaFile = $form->get('fichierFile')->getData();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données
            if ($mediaFile) {
                $newFilename = uniqid() . '.' . $mediaFile->guessExtension();
                try {
                    $mediaFile->move($this->getParameter('upload_directory'), $newFilename);
                    $competition->setFichier($newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur
                }
                
            }
            $entityManager->flush();

            // Ajouter un message flash pour confirmer la mise à jour
            $this->addFlash('success', 'La compétition a été mise à jour avec succès.');

            // Rediriger vers la liste des compétitions
            return $this->redirectToRoute('competitionlist');
        }

        // Afficher le formulaire de mise à jour
        return $this->render('competition/collaborateureditcomp.html.twig', [
            'competition' => $competition,
            'form' => $form->createView(),
        ]);
    }
}
