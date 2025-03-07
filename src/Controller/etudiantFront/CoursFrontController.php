<?php

namespace App\Controller\etudiantFront;

use App\Entity\Cours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CoursRepository;
use App\Repository\ChapitreRepository;
use App\Entity\Chapitre;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\QuizRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;


final class CoursFrontController extends AbstractController
{
    #[Route('/cours', name: 'app_showcours')]
    public function index(CoursRepository $coursRepository, Request $request): Response
    {
        $searchCriteria = [
            'nom' => $request->query->get('nom'),
            'etat' => $request->query->get('etat'),
            'dateCreation' => $request->query->get('dateCreation'),
        ];

        $cours = $coursRepository->searchCours($searchCriteria);

        return $this->render('etudiantFrontCours/cours/showCours.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/{id}', name: 'front_cours_show')]
    public function show(Cours $cours): Response
    {
        // Afficher les détails d'un cours spécifique
        return $this->render('etudiantFrontCours/cours/showCoursDetails.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/{id}/inscrire', name: 'front_cours_inscrire')]
    public function inscrire(Cours $cours): Response
    {
        // Logique pour inscrire l'utilisateur au cours

        $this->addFlash('success', 'Vous êtes inscrit au cours avec succès.');
        return $this->redirectToRoute('app_chapitre_front', ['id' => $cours->getId()]);
    }

    // partie chapitres 
    #[Route('/cours/{id}/chapitre', name: 'app_chapitre_front')]
    public function ShowChapitresEtudiant(Cours $cours, ChapitreRepository $chapitreRepository): Response
    {
        // Récupérer les chapitres du cours spécifique
        $chapitres = $chapitreRepository->findBy(['cours' => $cours]);

        return $this->render('etudiantFrontCours/chapitre/showChapitre.html.twig', [
            'cours' => $cours,
            'chapitres' => $chapitres,
        ]);
    }

    #[Route('/chapitre/{id}', name: 'show_chapitre_details')]
    public function showDetails(Chapitre $chapitre): Response
    {
        return $this->render('etudiantFrontCours/chapitre/showChapitreDetails.html.twig', [
            'chapitre' => $chapitre,
            'quiz' => $chapitre->getQuiz(), // Get the quiz if it exists
        ]);
    }


    // quiz routes
    #[Route('/chapitre/{id}/quiz', name: 'front_chapitre_lancer_quiz')]
    public function lancerQuiz(int $id, ChapitreRepository $chapitreRepository): Response
    {
        // Fetch the chapter manually
        $chapitre = $chapitreRepository->find($id);
    
        // If no chapter is found, show an error message
        if (!$chapitre) {
            throw $this->createNotFoundException('Chapitre non trouvé.');
        }
    
        $quiz = $chapitre->getQuiz();
    
        if (!$quiz) {
            $this->addFlash('warning', 'Aucun quiz n’est disponible pour ce chapitre.');
            return $this->redirectToRoute('show_chapitre_details', ['id' => $chapitre->getId()]);
        }
    
        return $this->render('etudiantFrontCours/quiz/lancerQuiz.html.twig', [
            'quiz' => $quiz,
            'questions' => $quiz->getQuestion(),
        ]);
    }
    #[Route('/quiz/{id}/submit', name: 'quiz_submit')]
    public function validerQuiz(int $id, Request $request, QuizRepository $quizRepository,MailerInterface $mailer): Response
    {
        $quiz = $quizRepository->find($id);
    
        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz demandé n\'existe pas.');
        }
    
        $score = 0;
        $reponsesSoumises = $request->get('reponses', []); // Récupérer les réponses soumises
    
        foreach ($quiz->getQuestion() as $question) {
            $type = $question->getType(); // Supposons que le type est stocké sous forme de string "choix_unique" ou "choix_multiple"
    
            if ($type === "choix_unique") {
                // Cas d'une question à choix unique
                $bonneReponse = null;
                foreach ($question->getReponse() as $reponse) {
                    if ($reponse->isCorrect()) {
                        $bonneReponse = $reponse->getId();
                        break; // Une seule bonne réponse
                    }
                }
    
                if (isset($reponsesSoumises[$question->getId()])) {
                    $reponseUtilisateur = (int) $reponsesSoumises[$question->getId()];
                    if ($reponseUtilisateur === $bonneReponse) {
                        $score++;
                    }
                }
            } elseif ($type === "choix_multiple") {
                // Cas d'une question à choix multiple
                $bonnesReponses = [];
                foreach ($question->getReponse() as $reponse) {
                    if ($reponse->isCorrect()) {
                        $bonnesReponses[] = (int) $reponse->getId();
                    }
                }
    
                if (isset($reponsesSoumises[$question->getId()])) {
                    $reponsesUtilisateur = array_map('intval', (array)$reponsesSoumises[$question->getId()]);
    
                    // Vérifier si les réponses utilisateur correspondent exactement aux bonnes réponses
                    sort($reponsesUtilisateur);
                    sort($bonnesReponses);
    
                    if ($reponsesUtilisateur === $bonnesReponses) {
                        $score++;
                    }
                }
            }
        }
    
        $totalQuestions = count($quiz->getQuestion());

    if ($score === $totalQuestions) {
        // Email statique pour test
        $emailEtudiant = 'mejria742@gmail.com'; 

        // Récupérer la certification associée au cours
        $cours = $quiz->getChapitre()->getCours();
        $projectDir = $this->getParameter('kernel.project_dir');
        $certificationPath = $projectDir . '/public/uploads/certifications/certif_' . $cours->getId() . '.pdf';

        if (!file_exists($certificationPath)) {
            throw new \Exception('Fichier de certification introuvable : ' . $certificationPath);
        }

        // Créer l'email
        $email = (new Email())
            ->from(new Address('outid@gmail.com', 'Plateforme E-learning OUTID'))
            ->to($emailEtudiant)
            ->subject('Félicitations ! Voici votre certification')
            ->text('Félicitations ! Vous avez obtenu un score parfait au quiz du cours "' . $cours->getNom(). '". Vous trouverez en pièce jointe votre certification.')
            ->attachFromPath($certificationPath, 'certification.pdf');

        // Envoyer l'email
        $mailer->send($email);

        $this->addFlash('success', 'Félicitations ! La certification a été envoyée à ' . $emailEtudiant);
    } else {
        $this->addFlash('success', 'Votre score est : ' . $score . '/' . $totalQuestions);
    }

    return $this->redirectToRoute('front_cours_show', [
        'id' => $quiz->getChapitre()->getCours()->getId()
    ]);

    }
    
    
}
