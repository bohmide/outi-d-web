<?php

namespace App\Controller\profFront;

use App\Entity\Chapitre;
use App\Entity\Cours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ChapitreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ChapitreType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface as SluggerSluggerInterface;

final class ChapitreBackController extends AbstractController
{
    #[Route('/{id}/chapitres', name: 'admin_cours_chapitres')] 
    public function showChapitreCours(ChapitreRepository $chapRepo,Cours $cours): Response
    {
         // Fetch only chapters that belong to the selected course
        $chapitres = $chapRepo->findBy(['cours' => $cours]);
                   return $this->render('profFrontCours/chapitre/listChapitre.html.twig', [
                   'cours' => $cours,
                   'chapitres' => $chapitres, // Fetching from the database
        ]);
    }
    #[Route('/{id}/chapitres/new', name: 'chapitre_admin_new')]
        public function new(Request $request, Cours $cours, ManagerRegistry $m, SluggerSluggerInterface $slugger): Response
{
    $em = $m->getManager();
    $chapitre = new Chapitre();
    $chapitre->setCours($cours); // Associer au bon cours

    $form = $this->createForm(ChapitreType::class, $chapitre);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())  
    {
        // Gestion du fichier
        $fichier = $form->get('file')->getData();
        
        if ($fichier) {
            $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichier->guessExtension();

            try {
                $fichier->move(
                    $this->getParameter('chapitres_directory'),
                    $newFilename
                );
                $chapitre->setContenu($newFilename); // Stocke uniquement le nom du fichier
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload du fichier.');
            } 
        }

        // Récupération du contenu texte
        $contenuText = $form->get('contenuText')->getData();
        $chapitre->setContenuText($contenuText);

        $em->persist($chapitre);
        $em->flush();
        $this->addFlash('success', 'Chapitre ajouté avec succès !');
        
        return $this->redirectToRoute('admin_cours_chapitres', ['id' => $cours->getId()]);
    }

    return $this->render('profFrontCours/chapitre/addChapitre.html.twig', [
        'form' => $form,
        'cours' => $cours,
    ]);
    }

    #[Route('/updateChapitre/{id}', name: 'app_admin_updateChapitre')]
    public function updateChapitre(Request $request, ManagerRegistry $m, int $id, ChapitreRepository $rep, SluggerSluggerInterface $slugger): Response
    {
        $em = $m->getManager();
        $chapitre = $rep->find($id);
    
        if (!$chapitre) {
            throw $this->createNotFoundException('Chapitre non trouvé');
        }
    
        $originalFile = $chapitre->getContenu(); // Récupérer le fichier existant
    
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du fichier
            $fichier = $form->get('file')->getData();
    
            if ($fichier) {
                // Supprimer l'ancien fichier si nécessaire
                if ($originalFile) {
                    $filePath = $this->getParameter('chapitres_directory') . '/' . $originalFile;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
    
                // Upload du nouveau fichier
                $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichier->guessExtension();
    
                try {
                    $fichier->move(
                        $this->getParameter('chapitres_directory'),
                        $newFilename
                    );
                    $chapitre->setContenu($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier.');
                }
            }
    
            // Mise à jour du contenu texte
            $contenuText = $form->get('contenuText')->getData();
            $chapitre->setContenuText($contenuText);
    
            $em->flush();
            $this->addFlash('success', 'Chapitre modifié avec succès !');
    
            return $this->redirectToRoute('admin_cours_chapitres', [
                'id' => $chapitre->getCours()->getId()
            ]);
        }
    
        return $this->render('profFrontCours/chapitre/editChapitre.html.twig', [
            'form' => $form,
            'chapitre' => $chapitre,
            'cours' => $chapitre->getCours(),
        ]);
    }

        #[Route('/deleteChapitre/{id}', name: 'app_admin_deleteChapitre')]
        public function deleteChapitre(int $id, ManagerRegistry $m, ChapitreRepository $rep): Response
        {
            $em = $m->getManager();
            $chapitre = $rep->find($id);
        
            if (!$chapitre) {
                $this->addFlash('error', 'Chapitre introuvable.');
                return $this->redirectToRoute('admin_cours_chapitres');
            }
        
            // Supprimer le fichier associé s'il existe
            if ($chapitre->getContenu()) {
                $filePath = $this->getParameter('chapitres_directory') . '/' . $chapitre->getContenu();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        
            $em->remove($chapitre);
            $em->flush();
        
            $this->addFlash('success', 'Chapitre supprimé avec succès !');
        
            return $this->redirectToRoute('admin_cours_chapitres', [
                'id' => $chapitre->getCours()->getId()
            ]);
        }

        #[Route('/uploads/chapitres/{file}', name :'serve_file')]
    public function serveFile(string $file): Response
    {
        $filePath = $this->getParameter('chapitres_directory') . '/chapitres/' . $file;

        // Check if the file exists
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas');
        }

        // Get the file's MIME type
        $mimeType = mime_content_type($filePath);

        // Create the response with the file content and proper headers
        return new Response(
            file_get_contents($filePath),
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            ]
        );
    }    

}

