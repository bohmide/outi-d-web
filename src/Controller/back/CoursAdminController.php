<?php

namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CoursRepository;
use App\Entity\Cours;
use App\Form\CoursType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ChapitreRepository;

final class CoursAdminController extends AbstractController
{
    #[Route('/cours/adminShow', name: 'admin_cours_back')]
    public function showAdmin(CoursRepository $coursRepository): Response
    {
        // récupérer tous les cours 
        $cours = $coursRepository->findAll();

        return $this->render('cours_admin/showAdmincours.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('cours/adminShow/new', name: 'back_cours_new')]
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
            return $this->redirectToRoute('admin_cours_back');
        }

        return $this->render('cours_admin/coursAdminNew.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('cours/adminShow/{id}/delete', name: 'back_cours_delete')]
    public function delete(ManagerRegistry $m, $id, CoursRepository $rep): Response
    {
        $em = $m->getManager();
        $cours = $rep->find($id);
        $em->remove($cours);
        $em->flush();
        return $this->redirectToRoute('admin_cours_back');
    }
    #[Route('/cours/adminShow/{id}/chapitres', name: 'back_cours_chapitres')]
    public function showAdminChapitre(ChapitreRepository $chapRepo, Cours $cours): Response
    {
        // Récupère uniquement les chapitres associés au cours
        $chapitres = $chapRepo->findBy(['cours' => $cours]);

        return $this->render('cours_admin/showAdminChapitres.html.twig', [
            'cours' => $cours,
            'chapitres' => $chapitres, // Récupération depuis la base de données
        ]);
    }
    #[Route('/chapitres/deleteChapitre/{id}', name: 'back_admin_deleteChapitre')]
    public function deleteChapitre(int $id, ManagerRegistry $m, ChapitreRepository $rep): Response
    {
        $em = $m->getManager();
        $chapitre = $rep->find($id);

        if (!$chapitre) {
            $this->addFlash('error', 'Chapitre introuvable.');
            return $this->redirectToRoute('admin_cours_chapitres');
        }
        // Supprimer le fichier associé s'il existe
        if ($chapitre->getContenu()) {
            $filePath = $this->getParameter('chapitres_directory') . '/' . $chapitre->getContenu();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $em->remove($chapitre);
        $em->flush();

        $this->addFlash('success', 'Chapitre supprimé avec succès !');

        return $this->redirectToRoute('back_cours_chapitres', [
            'id' => $chapitre->getCours()->getId()
        ]);
    }
}
