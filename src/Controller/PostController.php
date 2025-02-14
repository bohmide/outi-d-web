<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Forum;
use App\Form\PostType;
use App\Repository\ForumRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/addpost/{forumId}', name: 'app_addpost')]
    public function addPost($forumId, Request $request, ForumRepository $forumRepository,ManagerRegistry $m): Response
    {
        $em=$m->getManager();
        $forum = $forumRepository->find($forumId);
        if (!$forum) {
            throw $this->createNotFoundException("Forum not found");
        }
    
        $post = new Post();
        $post->setForum($forum); 
    
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();
    
            return $this->redirectToRoute('app_showposts', ['id' => $forum->getId()]);
        }
    
        return $this->render('post/addpost.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum, 
        ]);
    }

    #[Route('/forum/{id}/posts', name: 'app_showposts')]
    public function showPosts(Forum $forum): Response
    {
        return $this->render('post/showposts.html.twig', [
            'forum' => $forum,
            'posts' => $forum->getPosts(),
        ]);
    }

    #[Route('/post/{id}/like', name: 'app_like_post', methods: ['POST'])]
    public function likePost(Post $post, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        
        // Incrémente le nombre de likes
        $post->setNbLike($post->getNbLike() + 1);
        
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->json(['nbLike' => $post->getNbLike()]);
    }


    
}
