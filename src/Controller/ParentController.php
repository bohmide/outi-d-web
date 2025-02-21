<?php

namespace App\Controller;

use App\Repository\ParentsRepository; // Pour accéder aux données de l'entité Parents
use Symfony\Component\HttpFoundation\Response; // Pour retourner une réponse HTTP
use App\Entity\Parents;
use App\Form\AddparentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ParentController extends AbstractController
{
    #[Route('/parent', name: 'parent')]
    public function index(ManagerRegistry $doctrine, Request $request)
    {
        $em = $doctrine->getManager();
        $parents = new Parents();
        $form = $this->createForm(AddparentType::class, $parents);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($parents);
            $em->flush();

            // traitement des donnees
            return $this->redirectToRoute('base');
        }

        return $this->render('Pages/user/signupparent.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/showparent', name: 'showparent')]
    public function showParents(ParentsRepository $parentsRepository): Response
    {
        // Récupérer tous les enregistrements de la table Parents
        $parents = $parentsRepository->findAll();

        // Vérifier les données récupérées
        if (!$parents) {
            $this->addFlash('warning', 'No parents found in the database.');
        }

        return $this->render('Pages/user/parentback.html.twig', [
            'parents' => $parents,
        ]);
    }

    #[Route('/deleteparent/{id}', name: 'deleteparent')]
    public function deleteparent(ManagerRegistry $doctrine, int $id, ParentsRepository $rep): Response
    {
        // Récupérer le parent à supprimer
        $parent = $rep->find($id);
        
        // Vérifier si le parent existe
        if (!$parent) {
            throw $this->createNotFoundException('No parent found for id ' . $id);
        }

        // Action suppression
        $em = $doctrine->getManager();
        
        // Préparation de la suppression
        $em->remove($parent);

        // Exécution de la commande de suppression
        $em->flush();

        return $this->redirectToRoute('showparent'); // Vous devrez créer la route de redirection appropriée
    }

    #[Route('/updateparent/{id}', name: 'updateparent')]
    public function updateparent(ManagerRegistry $m , Request $req , int $id , ParentsRepository $rep): Response
    {
        $em = $m->getManager();
        $parent = $rep->find($id);
        
        if (!$parent) {
            throw $this->createNotFoundException('No parent found for id ' . $id);
        }

        $form2 = $this->createForm(AddparentType::class, $parent);
        $form2->handleRequest($req);
        
        if ($form2->isSubmitted() && $form2->isValid()) {
            $em->persist($parent);
            $em->flush();
            return $this->redirectToRoute('showparent');
        }
        
        return $this->render('Pages/user/updateparent.html.twig', [
            'form2' => $form2,
        ]);
    }


}
