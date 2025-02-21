<?php

namespace App\Controller\back;

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

final class ForumBackController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(): Response
    {
        return $this->render('back/forum_back/index.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }

    #[Route('/back/addbackforum', name: 'app_addbackforum')]
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
    
            return $this->redirectToRoute('app_showbackforum');
        }
    
        return $this->render('back/forum_back/addforum.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/showbackforum', name: 'app_showbackforum')]
    public function showforum(ForumRepository $rep):Response
    {
        $forum=$rep->findAll();
        return $this->render('back/forum_back/showforum.html.twig', [
            'tabforum' => $forum,
        ]);
    }

    #[Route('/deletebackforum/{id}', name: 'app_deletebackforum',methods: ['POST'])]
    public function deleteforum(ManagerRegistry $m,$id,ForumRepository $rep): Response
    {
        $em=$m->getManager();
        $forum=$rep->find($id);
        $em->remove($forum);
        $em->flush();
        return $this->redirectToRoute('app_showbackforum');
    }

    #[Route('/updatebackforum/{id}', name: 'app_updatebackforum')]
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
                    return $this->redirectToRoute('app_updatebackforum', ['id' => $forum->getId()]);
                }
            }
    
            // Enregistrer les modifications dans la base de données
            $em->flush();
    
            // Afficher un message de succès
            $this->addFlash('success', 'Forum mis à jour avec succès !');
            return $this->redirectToRoute('app_showbackforum', ['id' => $forum->getId()]);
        }
    
        return $this->render('back/forum_back/updateforum.html.twig', [
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }

}