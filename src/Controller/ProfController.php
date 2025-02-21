<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Form\AddprofType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProfRepository;
use Symfony\Component\HttpFoundation\Response;

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

    #[Route('/showprof', name: 'showprof')]
    public function showProfs(ProfRepository $profRepository): Response
    {
        // Récupérer tous les enregistrements de la table Prof
        $profs = $profRepository->findAll();

        // Vérifier les données récupérées
        if (!$profs) {
            $this->addFlash('warning', 'No professors found in the database.');
        }

        return $this->render('Pages/user/profback.html.twig', [
            'profs' => $profs,
        ]);
    }

    #[Route('/deleteprof/{id}', name: 'deleteprof')]
    public function deleteprof(ManagerRegistry $doctrine, int $id, ProfRepository $rep): Response
    {
        $parent = $rep->find($id);
        
        if (!$parent) {
            throw $this->createNotFoundException('No professor found for id ' . $id);
        }
        $em = $doctrine->getManager();
        
        // Supprimer l'entité
        $em->remove($parent);
        $em->flush();

        // Redirection après suppression
        return $this->redirectToRoute('showprof');
    }

    #[Route('/updateprof/{id}', name: 'updateprof')]
    public function updateprof(ManagerRegistry $m , Request $req , int $id , ProfRepository $rep): Response
    {
        $em = $m->getManager();
        $prof = $rep->find($id);
        
        if (!$prof) {
            throw $this->createNotFoundException('No professor found for id ' . $id);
        }

        $formp2 = $this->createForm(AddprofType::class, $prof);
        $formp2->handleRequest($req);
        
        if ($formp2->isSubmitted() && $formp2->isValid()) {
            $em->persist($prof);
            $em->flush();
            return $this->redirectToRoute('showprof');
        }
        
        return $this->render('Pages/user/updateprof.html.twig', [
            'formp2' => $formp2,
        ]);
    }

}
