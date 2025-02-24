<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Entity\QuizKids;
use App\Form\ChallengeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/challenge')]

final class ChallengeKidsController extends AbstractController{
    // src/Controller/ChallengeController.php

#[Route('/create', name: 'create_challenge')]
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

    #[Route('/list', name: 'challenge_list')]
    public function challengeList(EntityManagerInterface $entityManager): Response
    {
        $challenges = $entityManager->getRepository(Challenge::class)->findAll();

        return $this->render('/gamification/challenge/list.html.twig', [
            'challenges' => $challenges,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_challenge')]
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
#[Route('/delete/{id}', name: 'delete_challenge')]
public function deleteChallenge(Challenge $challenge, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($challenge);
    $entityManager->flush();

    return $this->redirectToRoute('challenge_list');
}

}


