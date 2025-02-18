<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Form\AddprofType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class ProfController extends AbstractController
{
    #[Route('/prof', name: 'prof')]
    public function index(ManagerRegistry $doctrine , Request $request)
    {
        $em = $doctrine->getManager();
        $prof = new Prof();
        $formp = $this->createForm(AddprofType::class , $prof);

        $formp->handleRequest($request);
        if($formp->isSubmitted() && $formp->isValid()){
            $em->persist($prof);
            $em->flush();
            //traitement des donnees
            return $this->redirectToRoute('base');
        }

        return $this->render('Pages/user/signupprof.html.twig', [
            'formp' => $formp->createView(),
        ]);
    }
}
