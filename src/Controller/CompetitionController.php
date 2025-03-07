<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Equipe;
use App\Form\CompetitionType;
use App\Form\EquipeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CompetitionRepository;
use App\Repository\EquipeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use App\Service\MailerService;
use Symfony\Component\String\Slugger\SluggerInterface;

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
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                $competition->setFichier($fileName);
            }

            // 🔹 Enregistrement de la localisation
            $competition->setLocalisation($form->get('localisation')->getData());
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
    // Return an empty result if the query is empty
    if (empty($query)) {
        return new JsonResponse([]);
    }

    try {
        // Get the competitions based on the query
        $competitions = $competitionRepository->searchCompetitions($query);

        // Map the results
        $results = array_map(function($competition) {
            return [
                'id' => $competition->getId(),
                'nomComp' => $competition->getNomComp(),
                'dateDebut' => $competition->getDateDebut()->format('Y-m-d'),
                'dateFin' => $competition->getDateFin()->format('Y-m-d'),
                'description' => $competition->getDescription(),
                'nomEntreprise' => $competition->getNomEntreprise(),
            ];
        }, $competitions);

        return new JsonResponse($results);

    } catch (\Exception $e) {
        // In case of an error, log and return an empty array or a specific error message
        // Log error (optional)
        return new JsonResponse([], 500); // Return HTTP 500 if there is an error
    }
}




    #[Route('/listcompetitionetudiant', name: 'competitionlistetudiant')]
    public function listcompetudiant(Request $request, CompetitionRepository $competitionRepository): Response
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

    #[Route('/competition/{id}/pdf', name: 'competition_pdf')]
public function generatePdf(int $id, EntityManagerInterface $entityManager, Environment $twig): Response
{
    // Récupérer la compétition depuis la base de données
    $competition = $entityManager->getRepository(Competition::class)->find($id);

    if (!$competition) {
        throw $this->createNotFoundException('Compétition non trouvée');
    }

    // Charger l'image en Base64
    $path = $this->getParameter('kernel.project_dir') . '/public/img/LOGO.png';
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64 = '';
    }

    // Options de Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);

    // Génération du HTML pour le PDF
    $html = $twig->render('competition/competitionpdf.html.twig', [
        'competition' => $competition,
        'logo_base64' => $base64 // Passer l'image au template
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="competition_' . $competition->getId() . '.pdf"',
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
    public function reservation(
        int $id, 
        CompetitionRepository $competitionRepository, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        MailerService $mailerService
    ): Response {
        $competition = $competitionRepository->find($id);
    
        if (!$competition) {
            throw $this->createNotFoundException("Cette compétition n'existe pas.");
        }
    
        // Création d'une nouvelle équipe
        $equipe = new Equipe();
        $form = $this->createForm(EquipeFormType::class, $equipe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer la compétition à l'équipe
            $equipe->addCompetition($competition);
    
            // Récupérer les membres du formulaire
            $membres = $form->get('membres')->getData();
    
            // Conversion en tableau si c'est une chaîne de caractères
            if (is_string($membres)) {
                $membres = explode(',', $membres);
            }
    
            // Assurer que les membres sont bien un tableau
            $equipe->setMembres(is_array($membres) ? $membres : []);
    
            // Persister l'équipe et la compétition
            $entityManager->persist($equipe);
            $entityManager->persist($competition);
            $entityManager->flush();
    
            // 🔹 Envoi d'un email après la création de l'équipe
            $nomComp = $competition->getNomComp();
            $nomEquipe = $equipe->getNomEquipe();
            $nomAmbassadeur = $equipe->getAmbassadeur();
            $membresEmails = $equipe->getMembres();
    
            $mailerService->sendEquipeCreatedEmail($nomComp, $nomEquipe, $nomAmbassadeur, $membresEmails);
    
            // Ajout d'un message flash pour informer que l'équipe est créée et que l'email a été envoyé
            $this->addFlash('success', 'Équipe créée avec succès ! Un email de confirmation a été envoyé.');
    
            return $this->redirectToRoute('equipe_list');
        }
    
        return $this->render('equipe/reserver.html.twig', [
            'competition' => $competition,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/competition/{id}/participation', name: 'competition_participation')]
    public function participation(
        int $id, 
        CompetitionRepository $competitionRepository, 
        EquipeRepository $equipeRepository,
        Request $request, 
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager
    ): Response {
        $competition = $competitionRepository->find($id);
    
        if (!$competition) {
            throw $this->createNotFoundException("Cette compétition n'existe pas.");
        }
    
        $equipes = $equipeRepository->findAll(); // Récupérer toutes les équipes de la compétition
    
        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');
            $equipeId = $request->request->get('equipe'); // Récupérer l'équipe sélectionnée
    
            if ($file && $equipeId) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
    
                try {
                    $file->move(
                        $this->getParameter('upload_Travail'), // Dossier configuré
                        $newFilename
                    );
    
                    // Récupérer l'équipe choisie
                    $equipe = $equipeRepository->find($equipeId);
                    if ($equipe) {
                        $equipe->setTravail($newFilename);
                        $entityManager->persist($equipe);
                        $entityManager->flush();
                        $this->addFlash('success', 'Fichier soumis avec succès à l’équipe sélectionnée !');
                        return $this->redirectToRoute('equipe_list');
                    }
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload du fichier.");
                }
            } else {
                $this->addFlash('error', "Veuillez sélectionner une équipe et un fichier.");
            }
        }
    
        return $this->render('competition/etudiantcomparticiper.html.twig', [
            'competition' => $competition,
            'equipes' => $equipes,
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
