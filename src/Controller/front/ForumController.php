<?php

namespace App\Controller\front;

use App\Form\ForumType;
use App\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
Use App\Repository\ForumRepository;
use App\Repository\PostRepository as RepositoryPostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(): Response
    {
        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }

    #[Route('/addforum', name: 'app_addforum')]
    public function addForum(Request $req, ForumRepository $rep, ManagerRegistry $doctrine): Response
    {
        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            
            $imageFile = $form->get('image_forum')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('forum_images_directory'),
                    $newFilename
                );
                $forum->setImageForum($newFilename);
            }
    
            $em->persist($forum);
            $em->flush();
    
            return $this->redirectToRoute('app_showforum');
        }
    
        return $this->render('front/forum/addforum.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/showforum', name: 'app_showforum')]
    public function showforum(Request $request, ForumRepository $rep): Response
    {
        $query = $request->query->get('query', '');
        $page = max(1, $request->query->getInt('page', 1)); // Récupère le numéro de la page (par défaut 1)
        $limit = 3; // Nombre de forums par page
    
        if (!empty($query)) {
            $forums = $rep->searchForums($query); // Si recherche active, on utilise searchForums()
            $totalForums = count($forums); // Nombre total de résultats trouvés
        } else {
            $forums = $rep->findPaginatedForums($page, $limit); // Sinon, pagination normale
            $totalForums = count($rep->findAll()); // Nombre total de forums
        }
    
        $totalPages = ceil($totalForums / $limit); // Calcul du nombre total de pages
    
        return $this->render('front/forum/showforum.html.twig', [
            'tabforum' => $forums,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'query' => $query, // Pour garder la valeur de recherche dans l’input
        ]);
    }
    

    #[Route('/deleteforum/{id}', name: 'app_deleteforum',methods: ['POST'])]
    public function deleteforum(ManagerRegistry $m,$id,ForumRepository $rep): Response
    {
        $em=$m->getManager();
        $forum=$rep->find($id);
        $em->remove($forum);
        $em->flush();
        return $this->redirectToRoute('app_showforum');
    }

    #[Route('/updateforum/{id}', name: 'app_updateforum')]
    public function updateforum(ManagerRegistry $m, $id, Request $req, ForumRepository $rep): Response
    {
        $em = $m->getManager();
        $forum = $rep->find($id);
    
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si une nouvelle image a été téléchargée
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image_forum')->getData();
    
            if ($imageFile) {
                // Générer un nom unique pour l'image
                $newFilename = uniqid('', true) . '.' . $imageFile->guessExtension();
    
                try {
                    // Déplacer le fichier dans le répertoire de stockage
                    $imageFile->move(
                        $this->getParameter('forum_images_directory'), // Le répertoire où l'image sera stockée
                        $newFilename
                    );
                    // Mettre à jour le champ imageForum avec le nouveau nom du fichier
                    $forum->setImageForum($newFilename);
                } catch (FileException $e) {
                    // Gestion d'erreur si l'upload échoue
                    $this->addFlash('error', 'L\'upload de l\'image a échoué.');
                    return $this->redirectToRoute('app_updateforum', ['id' => $forum->getId()]);
                }
            }
    
            // Enregistrer les modifications dans la base de données
            $em->flush();
    
            // Afficher un message de succès
            $this->addFlash('success', 'Forum mis à jour avec succès !');
            return $this->redirectToRoute('app_showforum', ['id' => $forum->getId()]);
        }
    
        return $this->render('front/forum/updateforum.html.twig', [
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }


}
