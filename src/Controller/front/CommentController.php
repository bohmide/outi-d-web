<?php

namespace App\Controller\front;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('front/comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route("/post/{id}/comments", name:"app_showcomments")]
    public function showcomments($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $post = $em->getRepository(Post::class)->find($id);
    
        if (!$post) {
            throw $this->createNotFoundException("Post non trouvé");
        }
    
        $comments = $post->getComments(); // Assure-toi que cette relation est bien définie dans l'entité Post
    
        return $this->render('front/comment/showcomments.html.twig', [
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    #[Route('/post/{id}/comment/add', name: 'app_addcomment')]
    public function addComment($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException("Post non trouvé !");
        }

        $comment = new Comment();
        $comment->setPost($post);
        $comment->setDateCreation(new \DateTime());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $post->setNbComment($post->getNbComment() + 1);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['nbComment' => $post->getNbComment()]);
            }

            return $this->redirectToRoute('app_showcomments', ['id' => $post->getId()]);
        }

        return $this->render('front/comment/addcomment.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_deletecomment')]
    public function deleteComment($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        if (!$comment) {
            throw $this->createNotFoundException("Commentaire introuvable !");
        }

        $post = $comment->getPost();
        $em->remove($comment);
        $post->setNbComment(max(0, $post->getNbComment() - 1)); // Diminue le compteur
        $em->flush();

        // Redirection vers la liste des commentaires du post
        return $this->redirectToRoute('app_showcomments', ['id' => $post->getId()]);
    }

    #[Route('/comment/{id}/edit', name: 'app_editfrontcomment')]
    public function editComment($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Commentaire introuvable');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Commentaire mis à jour avec succès!');
            return $this->redirectToRoute('app_showcomments', ['id' => $comment->getPost()->getId()]);
        }

        return $this->render('front/comment/editcomment.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

}
