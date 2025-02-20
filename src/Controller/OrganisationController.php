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

final class OrganisationController extends AbstractController
{
    #[Route('/organisation', name: 'organisation_list')]
    public function list(OrganisationRepository $organisationRepository): Response
    {
        $organisations = $organisationRepository->findAll();

        return $this->render('organisation/index.html.twig', [
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
    public function edit(Request $request, Organisation $organisation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', "L'organisation a été mise à jour avec succès.");

            return $this->redirectToRoute('organisation_list');
        }

        return $this->render('organisation/edit.html.twig', [
            'organisation' => $organisation,
            'form' => $form->createView(),
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
}
