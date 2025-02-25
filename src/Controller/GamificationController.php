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

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
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
#[Route('/adminQuizKids/create', name: 'QuizKids_create_admin')]
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
                    $question->setOptions(array_values($form->get('options')->getData()));
                    
                    // Assigner au champ `options`
                    
                    $question->setScore(0);

                    $entityManager->persist($question);
                    $entityManager->flush();
                    $form = $this->createForm(QuizKidsType::class, new QuizKids());

                return $this->redirectToRoute('QuizKids_list_admin');

                }
                

                

                //compter le nombre total de questions
                $totalQuestions = $quizKidsRepository->countQuestions();

                return $this->render('gamification/quiz/addQuestion.html.twig', [
                    'form' => $form->createView(),
                    
                    'totalQuestions' => $totalQuestions,
                ]);
            }

#[Route('/adminQuizKids/list', name: 'QuizKids_list_admin')]
                public function ListQuestion( QuizKidsRepository $quizKidsRepository): Response
                {   
                    $questions = $quizKidsRepository->findAll();

                    return $this->render('gamification/quiz/list.html.twig', [
                        
                        'questions' => $questions,
                        
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
                        $question->setOptions(array_values($form->get('options')->getData()));
                        
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
                        return $this->redirectToRoute('QuizKids_list_admin');
                    }

                    return $this->render('gamification/quiz/editQuestion.html.twig', [
                        'form' => $form->createView(),
                        
                        'question' => $question
                    ]);
                }

#[Route('/kids', name: 'main_kids')]
                public function main(): Response
                {
                    return $this->render('gamification/homeKids.html.twig');
                }
#[Route('/Quizkids', name: 'quiz_kids', defaults: ['level' => null, 'genre' => null])]
                public function quizKids(EntityManagerInterface $entityManager, QuizKidsRepository $quizKidsRepository, Request $request, $level, $genre): Response
                {
                  
                    $level = $request->query->get('level');
                    $genre = $request->query->get('genre');
                    // Fetch filtered quizzes based on level and genre
                    $quizzes = $quizKidsRepository->findByGenreAndLevel($level, $genre);
                    //dump($quizzes);
                    //dump($level, $genre);
                    // If no quizzes are found, return a no quizzes page
                    if (!$quizzes) {
                        return $this->render('quiz/no_quiz.html.twig');
                    }
                
                    // Store the filtered quizzes in the session
                    $request->getSession()->set('filteredQuizzes', $quizzes);
                    
                    // Get the list of already shown quizzes from the session
                    $shownQuizzes = $request->getSession()->get('shownQuizzes', []);
                    //dump($shownQuizzes);
                    
                    // Filter out quizzes that have already been shown
                    $remainingQuizzes = array_filter($quizzes, function ($quiz) use ($shownQuizzes) {
                        return !in_array($quiz->getId(), $shownQuizzes);
                    });
                
                    // If all quizzes have been shown, display the "all quizzes played" message
                    if (empty($remainingQuizzes)) {
                        return $this->render('gamification/quiz/all_quizzes_played.html.twig');
                    }
                
                    // Pick a random quiz from the remaining ones
                    $randomQuiz = $remainingQuizzes[array_rand($remainingQuizzes)];
                
                    // Add the current quiz ID to the shown quizzes list
                    $shownQuizzes[] = $randomQuiz->getId();
                    $request->getSession()->set('shownQuizzes', $shownQuizzes);
                    //dump($remainingQuizzes);
                
                    // Return the random quiz with the selected level and genre
                    return $this->render('gamification/quiz/frontQuizKids.html.twig', [
                        'quiz' => $randomQuiz,
                        'level' => $level,
                        'genre' => $genre,
                    ]);
                }
                
    
  

#[Route('/select-quizKids', name: 'select_quizKids')]
    
                public function selectQuiz(EntityManagerInterface $entityManager, Request $request): Response
                {
                    // Fetch distinct levels and genres from the database
                    $levels = $entityManager->getRepository(QuizKids::class)->createQueryBuilder('q')
                        ->select('DISTINCT q.level')
                        ->getQuery()
                        ->getResult();

                    $genres = $entityManager->getRepository(QuizKids::class)->createQueryBuilder('q')
                        ->select('DISTINCT q.genre')
                        ->getQuery()
                        ->getResult();

                    // Get selected level and genre from the query parameters
                    $selectedLevel = $request->query->get('level', null);
                    $selectedGenre = $request->query->get('genre', null);

                    return $this->render('gamification/quiz/selectQuiz.html.twig', [
                        'levels' => $levels,
                        'genres' => $genres,
                        'selectedLevel' => $selectedLevel,
                        'selectedGenre' => $selectedGenre,
                    ]);  
                }

#[Route('/quiz/check/{id}', name: 'check_answer')]
            public function checkAnswer(int $id, Request $request)
                {
                    // Find the quiz by ID
                    $quiz = $this->entityManager->getRepository(QuizKids::class)->find($id);
                    // Get the selected level and genre from the session or request
                    $selectedLevel = $request->getSession()->get('selectedLevel');
                    $selectedGenre = $request->getSession()->get('selectedGenre');
                    
                    if (!$quiz) {
                        throw $this->createNotFoundException('No quiz found for id ' . $id);
                    }

                    // Get the selected answer from the form
                    $selectedAnswer = $request->request->get('answer');

                    // Debugging: print the selected answer and the correct answer
                    //dump($selectedAnswer); // This will print the selected answer
                    //dump($quiz->getCorrectAnswer()); // This will print the correct answer

                    // Check if the selected answer matches the correct answer
                    $message = '';
                    if ($selectedAnswer === $quiz->getCorrectAnswer()) {
                        $message = "Correct! 🎉";
                        $isCorrect = true;
                    } else {
                        $message = "Incorrect. Try again! ❌";
                        $isCorrect = false;
                    }

                    // Pass the message and quiz to the template for rendering
                    return $this->render('gamification/quiz/resultQuiz.html.twig', [
                        'message' => $message,
                        'quizzes' => [$quiz], // Render only the current quiz
                        'isCorrect' => $isCorrect,
                        'selectedLevel' => $selectedLevel,
                        'selectedGenre' => $selectedGenre,
                    ]);
                }

    #[Route('/Quizkids/reset', name: 'quiz_kids_reset')]
                public function resetQuizSession(Request $request): Response
                {
                    // Clear the shown quizzes from the session
                    $request->getSession()->remove('shownQuizzes');
                
                    // Redirect back to the quiz page
                    return $this->redirectToRoute('select_quizKids');
                }  
}
