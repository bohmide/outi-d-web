<?php

namespace App\Controller;

use App\Form\ParentsType;
use App\Repository\ParentsRepository; // Pour accéder aux données de l'entité Parents
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response; // Pour retourner une réponse HTTP
use App\Entity\Parents;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ParentController extends AbstractController
{
    #[Route('/newparent', name: 'Signparent')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $prof = new Parents();
        $formp = $this->createForm(Parentstype::class, $prof);
        $formp->handleRequest($request);

        if ($formp->isSubmitted() && $formp->isValid()) {
            
            $password = $formp->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($prof, $prof->getPassword());  // <-- Hash the password
            $prof->setPassword($hashedPassword);

            $prof->setRoles(['ROLE_PARENT']);

            
            $entityManager->persist($prof);
            $entityManager->flush();

            $this->addFlash('success', 'Prof created successfully!');
            return $this->redirectToRoute('base');
        }


        return $this->render('Pages/User/signupparent.html.twig', 
        ['form' => $formp->createView()]);
    }

    #[Route('/showparents', name: 'showparents')]
    public function showParents(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les enregistrements de la table Parents
        $parents = $entityManager->getRepository(Parents::class)->findAll();

        // Vérifier les données récupérées
        if (!$parents) {
            $this->addFlash('warning', 'No parents found in the database.');
        }

        return $this->render('Pages/User/parentback.html.twig', [
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

        $form2 = $this->createForm(ParentsType::class, $parent);
        $form2->handleRequest($req);
        
        if ($form2->isSubmitted() && $form2->isValid()) {
            $em->persist($parent);
            $em->flush();
            return $this->redirectToRoute('showparents');
        }
        
        return $this->render('Pages/user/updateparent.html.twig', [
            'form2' => $form2,
        ]);
    }


}
