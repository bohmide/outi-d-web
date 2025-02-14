<?php

namespace App\Controller;

use App\Form\ForumType;
use APP\FORM\PostType;
use App\Entity\Forum;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
Use App\Repository\ForumRepository;
use App\Repository\PostRepository as RepositoryPostRepository;
use Doctrine\Persistence\ManagerRegistry;

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
            $em->persist($forum);
            $em->flush();
    
            // Rediriger vers la page showforum après l'ajout du forum
            return $this->redirectToRoute('app_showforum', ['id' => $forum->getId()]);
        }
    
        return $this->render('forum/addforum.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/showforum', name: 'app_showforum')]
    public function showforum(ForumRepository $rep):Response
    {
        $forum=$rep->findAll();
        return $this->render('forum/showforum.html.twig', [
            'tabforum' => $forum,
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
            $em->flush();
            $this->addFlash('success', 'Forum mis à jour avec succès !');
            return $this->redirectToRoute('app_showforum', ['id' => $forum->getId()]);
        }
        return $this->render('forum/updateforum.html.twig', [
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/forum/{id}/posts', name: 'app_showposts')]
public function showPosts($id, ForumRepository $forumRepository,RepositoryPostRepository $postRepository): Response
{
    $forum = $forumRepository->find($id);
    if (!$forum) {
        throw $this->createNotFoundException('Le forum demandé n\'existe pas.');
    }
    $posts = $postRepository->findBy(['forum' => $forum]);

    return $this->render('forum/showposts.html.twig', [
        'forum' => $forum,
        'posts' => $posts,
    ]);
}
}
