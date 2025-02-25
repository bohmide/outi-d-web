<?php

namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Chapitre;
use App\Repository\QuizRepository;
use App\Entity\Quiz;
use App\Form\QuizType;


final class QuizBackController extends AbstractController
{
    #[Route('/chapitres/{id}/quiz', name: 'admin_chapitre_quiz')]
    public function showQuiz(Chapitre $chapitre): Response
    {
        $quiz = $chapitre->getQuiz();

        return $this->render('back/quiz/showQuiz.html.twig', [
            'chapitre' => $chapitre,
            'quiz' => $quiz,
        ]);
    }
    #[Route('/chapitres/{id}/quiz/new', name: 'admin_quiz_new')]
    public function newQuiz(Request $request, Chapitre $chapitre, ManagerRegistry $m): Response
    {
        $em = $m->getManager();
        $quiz = new Quiz();
        $quiz->setChapitre($chapitre);

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($quiz);
            $em->flush();

            $this->addFlash('success', 'Quiz créé avec succès !');
            return $this->redirectToRoute('admin_chapitre_quiz', ['id' => $chapitre->getId()]);
        }

        return $this->render('back/quiz/newQuiz.html.twig', [
            'form' => $form,
            'chapitre' => $chapitre,
        ]);
    }

    #[Route('/quiz/{id}/edit', name: 'admin_quiz_edit')]
    public function editQuiz(Request $request, Quiz $quiz, ManagerRegistry $m): Response
    {
        $em = $m->getManager();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Quiz modifié avec succès !');
            return $this->redirectToRoute('admin_chapitre_quiz', ['id' => $quiz->getChapitre()->getId()]);
        }

        return $this->render('back/quiz/editQuiz.html.twig', [
            'form' => $form,
            'quiz' => $quiz,
            'chapitre' => $quiz->getChapitre(),
        ]);
    }

    #[Route('/quiz/{id}/delete', name: 'admin_quiz_delete')]
    public function deleteQuiz(Quiz $quiz, ManagerRegistry $m): Response
    {
        $em = $m->getManager();
    

        $chapitreId = $quiz->getChapitre()->getId();
        $em->remove($quiz);
        $em->flush();

        $this->addFlash('success', 'Quiz supprimé avec succès !');
        return $this->redirectToRoute('admin_chapitre_quiz', ['id' => $chapitreId]);
    }
}
