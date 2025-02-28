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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class CompetitionController extends AbstractController
{
 //front   
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

            return $this->redirectToRoute('competitionlist');
        }

        return $this->render('competition/collaborateuraddcomp.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
    #[Route('/listcompetition', name: 'competitionlist')]
    public function list(Request $request, CompetitionRepository $competitionRepository): Response
    {
        $query = $request->query->get('query', '');
    
        if (!empty($query)) {
            $competitions = $competitionRepository->searchCompetitions($query);
        } else {
            $competitions = $competitionRepository->findAll();
        }
    
        return $this->render('competition/collaborateurmain.html.twig', [
            'competitions' => $competitions,
            'query' => $query,
        ]);
    }
    
    
    // src/Controller/CompetitionController.php
    #[Route('/competition/search/{query}', name: 'competition_search', methods: ['GET'])]
    public function search(string $query, CompetitionRepository $competitionRepository): JsonResponse
    {
        // Debugging: Log the query
        error_log("Search Query: " . $query);
    
        if (empty($query)) {
            return new JsonResponse([]);
        }
    
        // Get competitions
        $competitions = $competitionRepository->searchCompetitions($query);
    
        // Debugging: Log if competitions are found
        error_log("Competitions found: " . count($competitions));
    
        // Convert to JSON
        $results = [];
        foreach ($competitions as $competition) {
            $results[] = [
                'id' => $competition->getId(),
                'nomComp' => $competition->getNomComp(), // Ensure this matches your entity
                'dateDebut' => $competition->getDateDebut()->format('d/m/Y'),
                'dateFin' => $competition->getDateFin()->format('d/m/Y'),
                'description' => $competition->getDescription(),
                'nomEntreprise' => $competition->getNomEntreprise(), // Ensure this matches your entity
            ];
        }
    
        return new JsonResponse($results);
    }
    
    
    
    #[Route('/listcompetitionetudiant', name: 'competitionlistetudiant')]
    public function listcompetudiant(Request $request,CompetitionRepository $competitionRepository): Response
    {
        $competitions = $competitionRepository->findAll();
        $query = $request->query->get('query', '');
    
        if (!empty($query)) {
            $competitions = $competitionRepository->searchCompetitions($query);
        } else {
            $competitions = $competitionRepository->findAll();
        }

        return $this->render('competition/etudiantlist.html.twig', [
            'competitions' => $competitions,
            'query' => $query,
        ]);
        
    }

    #[Route('/competition/{id}', name: 'competition_details', methods: ['GET'])]
    public function details(Competition $competition): Response
    {
        return $this->render('competition/etudiantcompdetails.html.twig', [
            'competition' => $competition,
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


    #[Route('/competition/{id}/reservation', name: 'competition_reservation')]
    public function reservation(int $id, CompetitionRepository $competitionRepository): Response
    {
        $competition = $competitionRepository->find($id);

        if (!$competition) {
            throw $this->createNotFoundException("Cette compétition n'existe pas.");
        }

        return $this->render('competition/etudiantcompreserver.html.twig', [
            'competition' => $competition
        ]);
    }
    #[Route('/competition/{id}/participation', name: 'competition_participation')]
    public function participation(int $id, CompetitionRepository $competitionRepository): Response
    {
        $competition = $competitionRepository->find($id);

        if (!$competition) {
            throw $this->createNotFoundException("Cette compétition n'existe pas.");
        }

        return $this->render('competition/etudiantcomparticiper.html.twig', [
            'competition' => $competition
        ]);
    }



   //backoffice
   #[Route('/listcompadmin', name: 'list-comp')]
   public function listadmin(CompetitionRepository $competitionRepository): Response
   {
       $competitions = $competitionRepository->findAll();

       return $this->render('competition/back/listcomp.html.twig', [
           'competitions' => $competitions,
       ]);
   }

   #[Route('/competition/{id}/deleteadmin', name: 'delete_competition_admin', methods: ['POST'])]
    public function deleteadmin(Competition $competition, EntityManagerInterface $entityManager): Response
    {
        // Supprimer la compétition de la base de données
        $entityManager->remove($competition);
        $entityManager->flush();

        // Ajouter un message flash pour confirmer la suppression
        $this->addFlash('success', 'La compétition a été supprimée avec succès.');

        // Rediriger vers la liste des compétitions
        return $this->redirectToRoute('list-comp');   
    }
}
