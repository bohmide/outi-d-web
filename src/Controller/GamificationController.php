<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Puzzle;
use App\Entity\QuizKids;
use App\Form\GamesType;
use App\Form\PuzzleGameType;
use App\Repository\PuzzleRepository;
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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/backQuizKids/create', name: 'QuizKids_create_admin')]
    public function addQuestion(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, QuizKidsRepository $quizKidsRepository): Response
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

    #[Route('/backQuizKids/list', name: 'QuizKids_list_admin')]
    public function ListQuestion(QuizKidsRepository $quizKidsRepository): Response
    {
        $questions = $quizKidsRepository->findAll();

        return $this->render('gamification/quiz/list.html.twig', [

            'questions' => $questions,

        ]);
    }
    // Supprimer une question
    #[Route('/backQuizKids/delete/{id}', name: 'delete_QuizKids')]
    public function delete(QuizKids $question, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('QuizKids_admin');
    }
    // Modifier une question
    #[Route('/backQuizKids/edit/{id}', name: 'edit_QuizKids')]
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
    // public function quizKids(Request $request, QuizKidsRepository $quizKidsRepository): Response
    // {
    //     $level = $request->query->get('level');
    //     $genre = $request->query->get('genre');
    //     // Fetch filtered quizzes based on level and genre
    //     $quizzes = $quizKidsRepository->findByGenreAndLevel($level, $genre);

    //     if (empty($quizzes)) {
    //         return $this->render('gamification/quiz/no_quiz.html.twig');
    //     }

    //     // Get list of already shown quizzes
    //     $shownQuizzes = $request->getSession()->get('shownQuizzes', []);

    //     // Filter out quizzes that have already been shown
    //     $remainingQuizzes = array_filter($quizzes, function ($quiz) use ($shownQuizzes) {
    //         return !in_array($quiz->getId(), $shownQuizzes);
    //     });

    //     if (empty($remainingQuizzes)) {
    //         return $this->render('gamification/quiz/all_quizzes_played.html.twig');
    //     }

    //     // Pick a random quiz from the remaining ones
    //     $randomQuiz = $remainingQuizzes[array_rand($remainingQuizzes)];

    //     // Add the quiz ID to the session
    //     $shownQuizzes[] = $randomQuiz->getId();
    //     $request->getSession()->set('shownQuizzes', $shownQuizzes);

    //     return $this->render('gamification/quiz/frontQuizKids.html.twig', [
    //         'quiz' => $randomQuiz,
    //         'level' => $request->getSession()->get('selectedLevel'),
    //         'genre' => $request->getSession()->get('selectedGenre'),

    //     ]);
    // }

    public function quizKids(Request $request, QuizKidsRepository $quizKidsRepository): Response
{
    $session = $request->getSession();

    // Get selected level and genre from request
    $level = $request->query->get('level');
    $genre = $request->query->get('genre');

    // Use session-stored level & genre if not provided in request
    if (!$level) {
        $level = $session->get('selectedLevel');
    } else {
        $session->set('selectedLevel', $level);
    }

    if (!$genre) {
        $genre = $session->get('selectedGenre');
    } else {
        $session->set('selectedGenre', $genre);
    }

    // Unique session key for filtered quizzes list
    $quizListKey = "remainingQuizzes_{$level}_{$genre}";

    // Check if we already have quizzes stored in session
    $remainingQuizzes = $session->get($quizListKey, []);

    // If the session list is empty, fetch from the database
    if (empty($remainingQuizzes)) {
        $quizzes = $quizKidsRepository->findByGenreAndLevel($level, $genre);

        if (empty($quizzes)) {
            return $this->render('gamification/quiz/no_quiz.html.twig');
        }

        // Retrieve list of already shown quizzes for this level & genre
        $shownQuizzesKey = "shownQuizzes_{$level}_{$genre}";
        $shownQuizzes = $session->get($shownQuizzesKey, []);

        // Filter out already shown quizzes
        $remainingQuizzes = array_values(array_filter($quizzes, function ($quiz) use ($shownQuizzes) {
            return !in_array($quiz->getId(), $shownQuizzes);
        }));

        // If all quizzes have been played, reset and allow replaying
        if (empty($remainingQuizzes)) {
            $session->remove($shownQuizzesKey);
            $session->remove($quizListKey);
            return $this->render('gamification/quiz/all_quizzes_played.html.twig');
        }

        // Store the new filtered list in session
        $session->set($quizListKey, $remainingQuizzes);
    }

    // Take the first quiz from the remaining list
    $randomQuiz = array_shift($remainingQuizzes);

    // Store updated remaining quizzes list in session
    $session->set($quizListKey, $remainingQuizzes);

    // Add the quiz ID to the shown list
    $shownQuizzesKey = "shownQuizzes_{$level}_{$genre}";
    $shownQuizzes = $session->get($shownQuizzesKey, []);
    $shownQuizzes[] = $randomQuiz->getId();
    $session->set($shownQuizzesKey, $shownQuizzes);

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
    public function checkAnswer(int $id, Request $request, EntityManagerInterface $entityManager)
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
        $isCorrect = false;
        $points = 0;
    
        if ($selectedAnswer === $quiz->getCorrectAnswer()) {
            $message = "Correct! 🎉";
            $isCorrect = true;
            switch ($quiz->getLevel()) {
                case 'hard':
                    $points = 1000;
                    break;
                case 'medium':
                    $points = 500;
                    break;
                case 'easy':
                    $points = 200;
                    break;
            }
            $quiz->setScore($quiz->getScore() + $points);
            $entityManager->persist($quiz);
            $entityManager->flush();
        } else {
            $message = "Incorrect. Try again! ❌";
            $isCorrect = false;
        }

        $session = $request->getSession();
        $totalScore = $session->get('totalScore', 0) + $points;
        $session->set('totalScore', $totalScore);

        // Pass the message and quiz to the template for rendering
        return $this->render('gamification/quiz/resultQuiz.html.twig', [
            'message' => $message,
            'quizzes' => [$quiz], // Render only the current quiz
            'isCorrect' => $isCorrect,
            'selectedLevel' => $selectedLevel,
            'selectedGenre' => $selectedGenre,
            'points' => $points,
            'totalScore' => $totalScore, // Total score in session
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



#[Route('/play/puzzle/{id}', name: 'puzzle_play')]
public function playPuzzle(int $id, PuzzleRepository $puzzleRepository): Response
    {
        // Supposons que chaque pièce soit stockée dans une collection ou sous forme de chemins dans la base de données.
        $pieces = $puzzle = $puzzleRepository->find($id)->getPieces();  // C'est ici que vous récupérez les chemins des pièces de puzzle
        $finalImage = $puzzle = $puzzleRepository->find($id)->getFinalImage();  // Chemin vers l'image finale du puzzle

        return $this->render('gamification/game/puzzle.html.twig', [
            'pieces' => $pieces,
            'finalImage' => $finalImage,
        ]);
    }

    





#[Route('/game/puzzle/upload', name: 'puzzle_upload')]
public function addPuzzle(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $puzzle = new Puzzle();

    $form = $this->createForm(PuzzleGameType::class, $puzzle, [
        'games' => $entityManager->getRepository(Games::class)->findAll(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image complète du puzzle
        $finalImageFile = $form->get('finalImage')->getData();
        if ($finalImageFile) {
            $originalFilename = pathinfo($finalImageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $finalImageFile->guessExtension();

            try {
                $finalImageFile->move(
                    $this->getParameter('upload_puzzleMain'), 
                    $newFilename
                );
                $puzzle->setFinalImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image complète.');
            }
        }

        // Gestion des pièces du puzzle
        $piecesFiles = $request->files->get('puzzle')['pieces']; // Récupérer les fichiers
        if ($piecesFiles) {
            $piecesNames = [];
            
            foreach ($piecesFiles as $pieceFile) {
                if ($pieceFile ) {
                    $originalFilename = pathinfo($pieceFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $pieceFile->guessExtension();

                    try {
                        $pieceFile->move(
                            $this->getParameter('upload_puzzlePieces'), 
                            $newFilename
                        );
                        $piecesNames[] = $newFilename;
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload des pièces.');
                    }
                }
            }

            $puzzle->setPieces($piecesNames); // Enregistrer tous les noms des fichiers de pièces
        }

        // Sauvegarde du puzzle
        $entityManager->persist($puzzle);
        $entityManager->flush();

        return $this->redirectToRoute('puzzle_list');
    }

    return $this->render('gamification/game/puzzleUplaod.html.twig', [
        'form' => $form->createView(),
    ]);
}





#[Route('/back/game/create', name: 'game_create')]
public function createGame(Request $request, EntityManagerInterface $entityManager): Response
{
    $game = new Games();
    $form = $this->createForm(GamesType::class, $game);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Sauvegarder le jeu
        $entityManager->persist($game);
        $entityManager->flush();

        $this->addFlash('success', 'Jeu créé avec succès!');
        return $this->redirectToRoute('game_list');
    }

    return $this->render('gamification/game/createGame.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/back/game/list', name: 'game_list')]
public function showList(EntityManagerInterface $entityManager): Response
{
    // Récupérer tous les jeux depuis la base de données
    $games = $entityManager->getRepository(Games::class)->findAll();
    

    // Retourner la vue avec la liste des jeux
    return $this->render('gamification/game/list.html.twig', [
        'games' => $games,
    ]);
}
#[Route('/back/game/update/{id}', name: 'game_update')]
public function updateGame(Request $request, EntityManagerInterface $entityManager, $id): Response
{
    $game = $entityManager->getRepository(Games::class)->find($id);

    if (!$game) {
        throw $this->createNotFoundException('Jeu non trouvé');
    }

    // Créer le formulaire de mise à jour du jeu
    $form = $this->createForm(GamesType::class, $game);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        // Rediriger après la mise à jour
        return $this->redirectToRoute('game_list');
    }

    return $this->render('gamification/game/updateGame.html.twig', [
        'form' => $form->createView(),
        'game' => $game,
    ]);
}


#[Route('/back/game/delete/{id}', name: 'game_delete')]
public function deleteGame(Games $question, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('game_list');
    }
    #[Route('/back/puzzle/list', name: 'puzzle_list')]
    public function listPuzzles(EntityManagerInterface $entityManager,PuzzleRepository $puzzleRepository): Response
{
    $puzzles = $puzzleRepository->findAll();

    return $this->render('gamification/game/puzzleList.html.twig', [
        'puzzles' => $puzzles,
    ]);
}
#[Route('/back/puzzle/delete/{id}', name: 'puzzle_delete')]
public function deletePuzzle(Puzzle $puzzle, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($puzzle);
        $entityManager->flush();

        return $this->redirectToRoute('puzzle_list');
    }

}
