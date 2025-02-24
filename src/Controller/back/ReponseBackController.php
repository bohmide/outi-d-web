<?php

namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Question;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reponse;
use App\Form\ReponseType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

final class ReponseBackController extends AbstractController
{
    #[Route('/questions/{id}/reponse', name: 'admin_question_reponse')]
    public function showQuestion(Question $question): Response
    {
        $reponses = $question->getReponse();

        return $this->render('back/reponse/showReponse.html.twig', [
            'question' => $question,
            'reponses' => $reponses,
        ]);
    }
    #[Route('/questions/{id}/reponse/new', name: 'app_reponse_add')]
    public function new($id,Request $request, ManagerRegistry $ma, Question $question): Response
    {
        $em = $ma->getManager();
        
        //  Associer la réponse à la question
        $reponse = new Reponse();
        $reponse->setQuestion($question);

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reponse);
            $em->flush();

            $this->addFlash('success', 'Réponse enregistrée avec succès !');

            return $this->redirectToRoute('admin_question_reponse', ['id' => $question->getId()]);
        }

        return $this->render('back/reponse/addReponse.html.twig', [
            'form' => $form,
            'question' => $question, //  Passer la question au template
        ]);
    }
    #[Route('/questions/{id}/reponse/{reponseId}/edit', name: 'app_reponse_edit')]
    public function edit(int $id, int $reponseId, Request $request, ManagerRegistry $ma): Response
    {
        $em = $ma->getManager();

        // Récupérer la question par son ID
        $question = $em->getRepository(Question::class)->find($id);
        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        // Récupérer la réponse par son ID
        $reponse = $em->getRepository(Reponse::class)->find($reponseId);
        if (!$reponse) {
            throw $this->createNotFoundException('Réponse non trouvée');
        }

        // Création du formulaire pour la réponse
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on met à jour la réponse
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Réponse mise à jour avec succès !');
            return $this->redirectToRoute('admin_question_reponse', ['id' => $question->getId()]);
        }

        // Rendu du formulaire d'édition avec la question et la réponse
        return $this->render('back/reponse/editReponse.html.twig', [
            'form' => $form,
            'question' => $question,
            'reponses' => $question->getReponse(), // Récupérer toutes les réponses
        ]);
    }

    #[Route('/questions/{id}/reponse/{reponseId}/delete', name: 'app_reponse_delete')]
    public function deleteReponse(int $id, int $reponseId, ManagerRegistry $ma): Response
    {
        $em = $ma->getManager();

        // Récupérer la question par son ID
        $question = $em->getRepository(Question::class)->find($id);
        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        // Récupérer la réponse par son ID
        $reponse = $em->getRepository(Reponse::class)->find($reponseId);
        if (!$reponse) {
            throw $this->createNotFoundException('Réponse non trouvée');
        }

        // Supprimer la réponse
        $em->remove($reponse);
        $em->flush();

        // Rediriger après la suppression
        return $this->redirectToRoute('admin_question_reponse', ['id' => $question->getId()]);
    }
}