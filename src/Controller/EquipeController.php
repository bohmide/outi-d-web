<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeFormType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EquipeController extends AbstractController
{ 
   
    
    
   //#[Route('/reserver', name: 'reserver-equipe')]
    //public function reserver(Request $request, EntityManagerInterface $entityManager): Response
    //{
      //  $equipe = new Equipe();
        //$form = $this->createForm(EquipeFormType::class, $equipe);
        //$form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {
          //  $entityManager->persist($equipe);
            //$entityManager->flush();

           // return $this->redirectToRoute('equipe_list');
        //}

        //return $this->render('equipe/reserver.html.twig', [
          //  'form' => $form->createView(),
        //]);
    //}

    #[Route('/equipelist', name: 'equipe_list')]
    public function list(EquipeRepository $equipeRepository): Response
    {
        $equipes = $equipeRepository->findAll();
        return $this->render('equipe/listeequipe.html.twig', [
            'equipes' => $equipes,
        ]);
    }
    #[Route('/equipe/{id}', name: 'equipe_details', methods: ['GET'])]
    public function details(Equipe $equipe): Response
    {
        return $this->render('equipe/detailsequipe.html.twig', [
          'equipe' => $equipe,
        'competition' => $equipe->getCompetitions(),
        ]);
    }

    #[Route('/delete/{id}', name: 'equipe_delete')]
    public function delete(Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($equipe);
        $entityManager->flush();
        
        return $this->redirectToRoute('equipe_list');
    }
    
 //back 

 #[Route('/equipelistadmin', name: 'equipe_listback')]
    public function listback(EquipeRepository $equipeRepository): Response
    {
        $equipes = $equipeRepository->findAll();
        return $this->render('equipe/back/listeequipe.html.twig', [
            'equipes' => $equipes,
        ]);
    }
    #[Route('/delete/{id}/deleteadmin', name: 'equipe_delete')]
    public function deleteback(Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($equipe);
        $entityManager->flush();
        
        return $this->redirectToRoute('equipe_listback');
    }
   


}
