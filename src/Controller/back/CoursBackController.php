<?php

namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Cours;
use App\Repository\CoursRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\CoursType;
use App\Repository\ChapitreRepository;

final class CoursBackController extends AbstractController
{
    #[Route('/cours/backshow', name: 'app_cours_back')]
    public function index(CoursRepository $coursRepository): Response
    {
        // Récupérer tous les cours pour l'admin
        $cours = $coursRepository->findAll();

        return $this->render('back/cours/index.html.twig', [
            'cours' => $cours,
        ]);
    }
    #[Route('/new', name: 'admin_cours_new')]
    public function new(Request $request, ManagerRegistry $ma): Response
    {
        $em = $ma->getManager();
        $cours = new Cours();
        $cours->setDateCreation(new \DateTimeImmutable('today')); //date actuelle
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cours);
            $em->flush();

            $this->addFlash('success', 'Le cours a été créé avec succès.');
            return $this->redirectToRoute('app_cours_back');
        }

        return $this->render('back/cours/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: 'admin_cours_update')]
    public function updatecours(ManagerRegistry $m,Request $req,$id,CoursRepository $rep): Response
    {
        $em=$m->getManager();
        $ListCours=$rep->find($id);
        $form=$this->createForm(CoursType::class,$ListCours);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($ListCours);
            $em->flush();
            return $this->redirectToRoute('app_cours_back');
        }
        return $this->render('back/cours/new.html.twig', [
            'form' => $form,]);
        
        }
        #[Route('/{id}/delete', name: 'admin_cours_delete')]
        public function deleteCours(ManagerRegistry $m,$id,CoursRepository $rep): Response
        {
            $em = $m->getManager();
            $cours = $rep->find($id);
                $em->remove($cours);
                $em->flush();
                return $this->redirectToRoute('app_cours_back');
        }
        #[Route('/{id}/chapitres', name: 'admin_cours_chapitres')]
        public function showChapitreCours(ChapitreRepository $chapRepo,Cours $cours): Response
        {
             //  only chapters that belong to the selected cours
            $chapitres = $chapRepo->findBy(['cours' => $cours]);
                       return $this->render('back/chapitre/listChapitre.html.twig', [
                       'cours' => $cours,
                       'chapitres' => $chapitres, // Fetching from the database
            ]);
        }
    
}


