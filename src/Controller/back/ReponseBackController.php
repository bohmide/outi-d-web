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
use App\Repository\ReponseRepository;
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
    public function edit(#[MapEntity(mapping: ['id' => 'id'])] Question $question, 
       #[MapEntity(mapping: ['reponseId' => 'id'])] Reponse $reponse,Request $request, ManagerRegistry $ma): Response
{
    $em = $ma->getManager();
        $form = $this->createForm(ReponseType::class, $question);
        $form->handleRequest($request);
    // If the form is submitted and valid, persist the changes and redirect
    if ($form->isSubmitted() && $form->isValid()) {
        $reponse=$reponse->setQuestion($question); // reaffecter la question 
        $em->flush();

        $this->addFlash('success', 'Réponse mise à jour avec succès !');
        return $this->redirectToRoute('admin_question_reponse', ['id' => $question->getId()]);
    }

    // Render the edit form with the question and the reponse
    return $this->render('back/reponse/editReponse.html.twig', [
        'form' => $form,
        'question' => $question,
        'reponse' => $reponse, // Pass the reponse to the template for editing
    ]);
}

}
