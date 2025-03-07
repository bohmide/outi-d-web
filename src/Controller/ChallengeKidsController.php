<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\Challenge;
use App\Entity\QuizKids;
use App\Form\ChallengeType;
use App\Repository\QuizKidsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;







final class ChallengeKidsController extends AbstractController{
    // src/Controller/ChallengeController.php

#[Route('/back-challenge/create', name: 'create_challenge')]
public function createChallenge(Request $request, EntityManagerInterface $entityManager): Response
{
        $challenge = new Challenge();
        $challenge->setProgress("0");
        $form = $this->createForm(ChallengeType::class, $challenge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($challenge->getQuizzes() as $quiz) {
                $quiz->setIdChallenge($challenge); // Associer chaque quiz au challenge
            }

            $entityManager->persist($challenge);
            $entityManager->flush();

            return $this->redirectToRoute('challenge_list');
        }

        return $this->render('/gamification/challenge/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/back-challenge/list', name: 'challenge_list')]
    public function challengeList(EntityManagerInterface $entityManager): Response
    {
        $challenges = $entityManager->getRepository(Challenge::class)->findAll();

        return $this->render('/gamification/challenge/list.html.twig', [
            'challenges' => $challenges,
        ]);
    }

    #[Route('/back-challenge/edit/{id}', name: 'edit_challenge')]
public function editChallenge(Request $request, Challenge $challenge, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ChallengeType::class, $challenge);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('challenge_list');
    }

    return $this->render('/gamification/challenge/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/back-challenge/delete/{id}', name: 'delete_challenge')]
public function deleteChallenge(Challenge $challenge, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($challenge);
    $entityManager->flush();

    return $this->redirectToRoute('challenge_list');
}

#[Route('/challenge/map', name: 'app_map')]
    public function index(Request $request): Response
    {

        $request->getSession()->clear();
        return $this->render('/gamification/challenge/map.html.twig');
    }
#[Route('/quiz/country/{country}', name: 'quiz_country')]
    public function showByCountry(string $country, QuizKidsRepository $quizRepo): Response
    {
        // Fetch all quizzes for the selected country
        $quizzes = $quizRepo->findBy(['country' => $country]);
    
        if (!$quizzes) {
            $this->addFlash('warning', 'No quizzes available for ' . $country);
            return $this->redirectToRoute('globetrotter_map');
        }
    
        // Select a random quiz
        $quiz = $quizzes[array_rand($quizzes)];
    
    
        return $this->render('/gamification/quiz/country.html.twig', [
            'country' => $country,
            'quiz' => $quiz,
        ]);
    }    

#[Route('/quizCountry/check/{id}', name: 'check_answer_for_country')]
public function checkAnswer(int $id, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
{
    // Find the quiz by ID
    $quiz = $entityManager->getRepository(QuizKids::class)->find($id);

    // Get the selected level and genre from the session or request
    $selectedLevel = $request->getSession()->get('selectedLevel');
    $selectedGenre = $request->getSession()->get('selectedGenre');

    if (!$quiz) {
        throw $this->createNotFoundException('No quiz found for id ' . $id);
    }

    // Get the selected answer from the form
    $selectedAnswer = $request->request->get('answer');

    // Initialize variables
    $message = '';
    $isCorrect = false;
    $points = 0;

    // Check if the selected answer is correct
    if ($selectedAnswer === $quiz->getCorrectAnswer()) {
        $message = "Correct! 🎉";
        $isCorrect = true;
        $points = 75000; // Points for a correct answer

        // Update the score
        $quiz->setScore($quiz->getScore() + $points);
        $entityManager->persist($quiz);
        $entityManager->flush();
    } else {
        $message = "Incorrect. Try again! ❌";
        $isCorrect = false;
    }

    // Get the total score from the session
    $session = $request->getSession();
    $totalScore = $session->get('totalScore', 0) + $points;
    $session->set('totalScore', $totalScore);

    // Check if a badge is earned based on the score
    $badgeMessage = '';
    $badgeIcon = '';
    $badgeName = '';
    $badge = null;
    $username='Amine';

    // Find the badge that corresponds to the total score
    $badgeRepository = $entityManager->getRepository(Badge::class);
    $badge = $badgeRepository->findOneBy(['requiredScore' => $totalScore]);
    


    if ($badge) {
        $badgeMessage = 'Congratulations! You have earned the ' . $badge->getName() . ' badge! 🎉';
        $badgeName = $badge->getName();
        $badgeIcon = $badge->getIcon();

        $imagePath = $this->generateCertificateImage($badge,$username); 

        // Create the email
        $email = (new Email())
            ->from(new Address('no-reply@outid.com','Outi-d Kids STAFF'))
            ->to('mouhamedaminebenali8@gmail.com') // Replace with dynamic user email
            ->subject('Congratulations! Badge Earned')
            ->html('<p>Great job! You\'ve earned a badge for reaching 2000 points!</p><p>Badge Name: ' . $badge->getName() . '</p>')
            ->attachFromPath($imagePath) 
            ->text('A player has earned 1000 points and received a badge. Great job!');
        // Send the email
        $mailer->send($email);

        // Mark the email as sent
        $session->set('emailSent', true);
    }

    // Return the result to the view
    return $this->render('gamification/challenge/countryResult.html.twig', [
        'message' => $message,
        'quizzes' => [$quiz],
        'isCorrect' => $isCorrect,
        'selectedLevel' => $selectedLevel,
        'selectedGenre' => $selectedGenre,
        'points' => $points,
        'totalScore' => $totalScore,
        'badgeMessage' => $badgeMessage,  // Pass the badge message
        'badgeName' => $badgeName,        // Pass the badge name
        'badgeIcon' => $badgeIcon,        // Pass the badge icon
    ]);
}

private function generateCertificateImage($badge, $username)
{
    $manager = ImageManager::imagick(); // Ou ImageManager::imagick() si disponible

    // Création du canvas
    $image = $manager->create(900, 600);
    $image->fill('#ffffff'); // Fond blanc

    // Ajout du cadre coloré
    $frame = $manager->create(880, 580);
    $frame->fill('#ffcc99'); // Fond coloré enfantin
    $image->place($frame, 'center');
    $badgeName=$badge->getName();

    // Ajout du titre "CERTIFICATE"
    $image->text('Congratulations', 250, 100, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(50);
        $font->color('#4B0082'); // Violet foncé
    });

    // Ajout du sous-titre "OF APPRECIATION"
    $image->text($badgeName, 270, 140, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(20);
        $font->color('#FF4500'); // Orange foncé
    });

    // Texte "Presented to"
    $image->text('To our Globtrotter Junior:', 200, 200, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(20);
        $font->color('#000000');
    });

    // Nom du récipiendaire (dynamique)
    $image->text($username, 250, 250, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(35);
        $font->color('#1E90FF'); // Bleu
    });

    // Texte de description
    $image->text("Great job! You've earned a badge", 180, 300, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(18);
        $font->color('#333333');
    });

    // Ajout du badge (icône)
    $badgeIconPath = $this->getParameter('kernel.project_dir') . '/public/uploads/badges/' . $badge->getIcon();
    if (file_exists($badgeIconPath)) {
        $badgeImage = $manager->read($badgeIconPath);
        $badgeImage->resize(100, 100);
        $image->place($badgeImage, 'top-right', 50, 50);
    }

    // Ajout du score atteint
    $image->text('for reaching points: ' . $badge->getRequiredScore(), 250, 350, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(25);
        $font->color('#008000');  // Vert
    });

    // Ajout des signatures
    $image->text('Signature', 200, 500, function ($font) {
        $font->file($this->getParameter('kernel.project_dir') . '/public/fonts/Fredoka-VariableFont_wdth,wght.ttf');
        $font->size(18);
        $font->color('#000000');
    });
 

    // Sauvegarde du certificat
    $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/badges/certificate.png';
    $image->save($imagePath);



    return $imagePath;
}
}