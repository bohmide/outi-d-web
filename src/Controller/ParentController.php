<?php

namespace App\Controller;

use App\Entity\Parents;
use App\Form\AddparentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

    /* #[Route('/addparent', name: 'addparent')]
     public function addparent (ManagerRegistry $doctrine, Request $request): Response
     {
         // Création d'une nouvelle instance de l'entité Parents
         $parents = new Parents();

         // Création du formulaire en associant l'entité Parents au formulaire AddparentType
         $formp = $this->createForm(AddparentType::class, $parents);

         // Traitement de la requête HTTP (remplit l'objet avec les données soumises)
         $formp->handleRequest($request);

         // Vérification si le formulaire a été soumis et est valide
         if ($formp->isSubmitted() && $formp->isValid()) {
             $em = $doctrine->getManager(); // Récupération de l'EntityManager
             $em->persist($parents); // Préparation de l'enregistrement
             $em->flush(); // Enregistrement des données en base

             // Redirection vers la page d'affichage des parents après l'ajout
             return $this->redirectToRoute('app_AfficherParent');
         }

         // Affichage du formulaire dans le template
         return $this->render('parent/ajouterParent.html.twig', [
             'formp' => $formp->createView(), // Passage du formulaire à la vue
         ]);
     }*/

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
