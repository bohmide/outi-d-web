<?php

namespace App\Controller;

use App\Entity\QuizKids;
use App\Repository\QuizKidsRepository;
use App\Form\QuizKidsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class GamificationController extends AbstractController
{
    #[Route('/showQuiz/{id}', name: 'quiz_question')]
public function showQuestion(int $id, EntityManagerInterface $em): Response
{
    $quiz = $em->getRepository(QuizKids::class)->find($id);

    if (!$quiz) {
        return $this->redirectToRoute('quiz_result');
    }

    return $this->render('quiz/index.html.twig', [
        'quiz' => $quiz,
    ]);
}

#[Route('/quiz/submit/{id}', name: 'quiz_submit', methods: ['POST'])]
public function submitAnswer(int $id, Request $request, EntityManagerInterface $em): Response
{
    $quiz = $em->getRepository(QuizKids::class)->find($id);
    $selectedAnswer = $request->request->get('answer');
    $isCorrect = ($quiz && $quiz->getCorrectAnswer() === $selectedAnswer) ? true : false;

    // Store result in session (user answers and score)
    $session = $request->getSession();
    $userAnswers = $session->get('user_answers', []);
    $userAnswers[] = [
        'questionId' => $quiz->getId(),
        'userAnswer' => $selectedAnswer,
        'correctAnswer' => $quiz->getCorrectAnswer(),
    ];
    $session->set('user_answers', $userAnswers);

    // Update score
    $score = $session->get('quiz_score', 0);
    if ($isCorrect) {
        $session->set('quiz_score', $score + 1);
    }

    // Find the next question
    $nextQuestion = $em->getRepository(QuizKids::class)->find($id + 1);
    if ($nextQuestion) {
        return $this->redirectToRoute('quiz_question', ['id' => $nextQuestion->getId()]);
    } else {
        return $this->redirectToRoute('quiz_result');
    }
}

#[Route('/quiz/result', name: 'quiz_result')]
public function showResult(Request $request, EntityManagerInterface $em): Response
{
    $session = $request->getSession();
    $score = $session->get('quiz_score', 0);
    $userAnswers = $session->get('user_answers', []);
    
    // Fetch the quizzes (questions) from the database based on the IDs stored in the session
    $quizzes = [];
    foreach ($userAnswers as $userAnswer) {
        $quiz = $em->getRepository(QuizKids::class)->find($userAnswer['questionId']);
        if ($quiz) {
            $quizzes[] = [
                'question' => $quiz->getQuestion(), // The actual question text
                'userAnswer' => $userAnswer['userAnswer'],
                'correctAnswer' => $userAnswer['correctAnswer'],
            ];
        }
    }

    return $this->render('quiz/result.html.twig', [
        'score' => $score,
        'total' => count($quizzes),
        'quizzes' => $quizzes,  // Send the list of quizzes with question text
    ]);
}
#[Route('/adminQuizKids', name: 'QuizKids_admin')]
public function addQuestion(Request $request, EntityManagerInterface $entityManager ,SluggerInterface $slugger, QuizKidsRepository $quizKidsRepository): Response
{
    $question = new QuizKids();
    $form = $this->createForm(QuizKidsType::class, $question);
    $form->handleRequest($request);
    
    
    if ($form->isSubmitted() && $form->isValid()) {
        $options = $form->get('options')->getData();
        $mediaFile = $form->get('mediaFile')->getData();

        if ($mediaFile) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);            
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();
    
            // Déplacement du fichier vers le dossier public/uploads
            try {
                $mediaFile->move(
                    $this->getParameter('upload_destination'),
                    $newFilename
                );
                $question->setMedia($newFilename); // Sauvegarde du nom dans la base
            } catch (FileException $e) {
                // Gérer l'erreur si nécessaire
            }
        }
        
        // Convertir la chaîne de texte en tableau
        $optionsArray = array_map('trim', explode(',', $options)); 
        
        // Assigner au champ `options`
        $question->setOptions($optionsArray);
        $question->setScore(0);

        $entityManager->persist($question);
        $entityManager->flush();
        $form = $this->createForm(QuizKidsType::class, new QuizKids());

       /* return $this->redirectToRoute('quiz_success');*/

    }
    

    $questions = $quizKidsRepository->findAll();

    //compter le nombre total de questions
    $totalQuestions = $quizKidsRepository->countQuestions();

    return $this->render('gamification/quiz/addQuestion.html.twig', [
        'form' => $form->createView(),
        'questions' => $questions,
        'totalQuestions' => $totalQuestions,
    ]);
}

// Supprimer une question
#[Route('/adminQuizKids/delete/{id}', name: 'delete_QuizKids')]
    public function delete(QuizKids $question, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('QuizKids_admin');
    }
// Modifier une question
#[Route('/adminQuizKids/edit/{id}', name: 'edit_QuizKids')]
public function edit(QuizKids $question, Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(QuizKidsType::class, $question);
    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {
        $mediaFile = $form->get('mediaFile')->getData();
        $options = $form->get('options')->getData();
        $optionsArray = array_map('trim', explode(',', $options));
        $question->setOptions($optionsArray);
        if ($mediaFile) {
            $newFilename = uniqid() . '.' . $mediaFile->guessExtension();
            try {
                $mediaFile->move($this->getParameter('upload_destination'), $newFilename);
                $question->setMedia($newFilename);
            } catch (FileException $e) {
                // Gérer l'erreur
            }
            
        }
        $entityManager->flush();
        return $this->redirectToRoute('QuizKids_admin');
    }

    return $this->render('gamification/quiz/editQuestion.html.twig', [
        'form' => $form->createView(),
        
        'question' => $question
    ]);
}

#[Route('/kids', name: 'main_kids')]
    public function forms(): Response
    {
        return $this->render('gamification/index.html.twig');
    }

}
