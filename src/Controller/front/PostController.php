<?php

namespace App\Controller\front;

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
    
            return $this->redirectToRoute('app_showfrontposts', ['id' => $forum->getId()]);
        }
    
        return $this->render('front/post/addpost.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum, 
        ]);
    }
    #[Route('/forum/{id}/posts', name: 'app_showfrontposts')]
    public function showPosts(Forum $forum): Response
    {
        return $this->render('front/post/showposts.html.twig', [
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
    #[Route('/post/{id}/delete', name: 'app_deletepost', methods: ['POST'])]
    public function deletePost($id, ManagerRegistry $m, Request $request): Response
    {
        $em = $m->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (!$post) {
            $this->addFlash('danger', 'Le post n\'existe pas.');
            return $this->redirectToRoute('app_showfrontposts', ['id' => $post->getForum()->getId()]);
        }

        $forumId = $post->getForum()->getId();
        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post supprimé avec succès.');

        return $this->redirectToRoute('app_showfrontposts', ['id' => $forumId]);
    }

    #[Route('/post/{id}/edit', name: 'app_editpost')]
    public function editPost($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $post = $em->getRepository(Post::class)->find($id);
    
        if (!$post) {
            throw $this->createNotFoundException("Le post avec l'ID $id n'existe pas.");
        }
    
        $forum = $post->getForum();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Post modifié avec succès !');
            return $this->redirectToRoute('app_showfrontposts', ['id' => $forum->getId()]);
        }
    
        return $this->render('front/post/editpost.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum  
        ]);
    }
    
}
