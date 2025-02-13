<?php

namespace App\Controller;

use App\Entity\Sponsors;
use App\Form\SponsorsType;
use App\Repository\SponsorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class SponsorsController extends AbstractController
{
    #[Route('/sponsors', name: 'app_sponsors')]
    public function index(): Response
    {
        return $this->render('sponsors/index.html.twig', [
            'controller_name' => 'SponsorsController',
        ]);
    }



    // create
    #[Route('/sponsors/addSponsor', name: 'app_add_sponsor')]
    public function addSponsor(Request $request, ManagerRegistry $mr, SponsorsRepository $sr, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $sponsor = new Sponsors();
        $form = $this->createForm(SponsorsType::class, $sponsor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // check for genre name if exist
            $existGenreName = $sr->findOneBy(['nom_sponsor' => $sponsor->getNomSponsor()]);
            if ($existGenreName) {
                $this->addFlash('errorNameExist', 'This sponsor name already exists.');
                return $this->redirectToRoute('app_add_sponsor');
            }
            // add image
            /** @var UploadedFile $imageFile */

            $imageFile = $form->get('image_file')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Slugify filename
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('sponsors_images_directory'),
                        $newFilename
                    );
                    $sponsor->setImagePath($newFilename);
                } catch (FileException $fe) {
                    $this->addFlash('error', $fe->getMessage());
                }
            }

            $mr->getManager()->persist($sponsor);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_show_sponsors');
        }

        return $this->render('sponsors/addSponsors.html.twig', [
            'form' => $form,
        ]);
    }

    // show
    #[Route('/sponsors/showSponsors', name: 'app_show_sponsors')]
    public function showEventGenre(SponsorsRepository $sr): Response
    {
        $sponsors = $sr->findAll();

        return $this->render('sponsors/showSponsors.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }


    #[Route('/sponsors/updateSponsor/{id}', name: 'app_update_sponsor')]
    public function updateSponsor($id, Request $request, ManagerRegistry $mr, SponsorsRepository $sr, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $sponsor =$sr->find($id);
        $form = $this->createForm(SponsorsType::class, $sponsor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            // add image
            /** @var UploadedFile $imageFile */

            $imageFile = $form->get('image_file')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Slugify filename
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('sponsors_images_directory'),
                        $newFilename
                    );
                    $sponsor->setImagePath($newFilename);
                } catch (FileException $fe) {
                    $this->addFlash('error', $fe->getMessage());
                }
            }

            $mr->getManager()->persist($sponsor);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_show_sponsors');
        }

        return $this->render('sponsors/addSponsors.html.twig', [
            'form' => $form,
        ]);
    }


    
    // delete
    #[Route('/sponsors/deleteSponsor/{id}', name: 'app_delete_sponsor')]
    public function deleteSponosr($id, SponsorsRepository $sr, ManagerRegistry $managerRegistry): Response
    {
        $sponsor = $sr->find($id);
        $manager = $managerRegistry->getManager();
        $manager->remove($sponsor);
        $manager->flush();


        return $this->redirectToRoute('app_show_sponsors');
    }
    
}
