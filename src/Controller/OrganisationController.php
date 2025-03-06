<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Form\OrganisationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrganisationRepository;
use Doctrine\Persistence\ManagerRegistry;

final class OrganisationController extends AbstractController
{
    #[Route('/organisation', name: 'organisation_list')]
    public function list(OrganisationRepository $organisationRepository): Response
    {
        $organisations = $organisationRepository->findAll();

        return $this->render('organisation/show.html.twig', [
            'organisations' => $organisations,
        ]);
    }

    #[Route('/organisation/add', name: 'add_organisation')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $organisation = new Organisation();
        $form = $this->createForm(OrganisationType::class, $organisation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($organisation);
            $entityManager->flush();

            $this->addFlash('success', "L'organisation a été ajoutée avec succès!");

            return $this->redirectToRoute('organisation_list');
        }

        return $this->render('organisation/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/organisation/{id}/edit', name: 'edit_organisation', methods: ['GET', 'POST'])]
    public function edit($id,Request $request, OrganisationRepository $op,ManagerRegistry $entityManager): Response
    {
        $organisation = $op->find($id);

        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->getManager()->persist($organisation);

            $entityManager->getManager()->flush();

            $this->addFlash('success', "L'organisation a été mise à jour avec succès.");

            return $this->redirectToRoute('organisation_list');
        }

        return $this->render('organisation/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/organisation/{id}/delete', name: 'delete_organisation', methods: ['POST'])]
    public function delete(Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($organisation);
        $entityManager->flush();

        $this->addFlash('success', "L'organisation a été supprimée avec succès.");

        return $this->redirectToRoute('organisation_list');
    }



    //back
    #[Route('/organisationadmin', name: 'show-admin-organisation')]
    public function showorganisationadmin(OrganisationRepository $organisationRepository): Response
    {
        $organisations = $organisationRepository->findAll();

        return $this->render('organisation/back/listadmin.html.twig', [
            'organisations' => $organisations,
        ]);
    }

    #[Route('/admin/organisation/{id}/delete', name: 'delete-admin-organisation', methods: ['POST'])]
    public function deleteorganisationadmin(Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($organisation);
        $entityManager->flush();

        $this->addFlash('success', "L'organisation a été supprimée avec succès.");

        return $this->redirectToRoute('show-admin-organisation');
    }
}
