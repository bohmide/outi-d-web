<?php

namespace App\Controller;

use App\Entity\EventGenre;
use App\Form\EventGenreType;
use App\Repository\EventGenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EventGenreController extends AbstractController
{
    #[Route('/events/genre', name: 'app_event_genre')]
    public function index(): Response
    {
        return $this->render('event_genre/index.html.twig', [
            'controller_name' => 'EventGenreController',
        ]);
    }// create
    #[Route('/events/addGenre', name: 'app_add_event_genre')]
    public function addGenre(Request $request, ManagerRegistry $mr, EventGenreRepository $egr, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $eventGenres = $egr->findAll();
        $eventGenre = new EventGenre();
        $form = $this->createForm(EventGenreType::class, $eventGenre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // check for genre name if exist
            $existGenreName = $egr->findOneBy(['nom_genre' => $eventGenre->getNomGenre()]);
            if ($existGenreName) {
                $this->addFlash('errorNameExist', 'This genre name already exists.');
                return $this->redirectToRoute('app_add_event_genre');
            }
            //initialize the nbr of the new genre
            $eventGenre->setNbr(0);

            // add image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image_file')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Slugify filename
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('event_genre_images_directory'),
                        $newFilename
                    );
                    $eventGenre->setImagePath($newFilename);
                } catch (FileException $fe) {
                    $this->addFlash('error', $fe->getMessage());
                }
            }

            $mr->getManager()->persist($eventGenre);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_show_event_genre');
        }

        return $this->render('event_genre/addGenre.html.twig', [
            'form' => $form,
            'eventGenres' => $eventGenres,
        ]);
    }

    // show
    #[Route('/events/showEventGenres', name: 'app_show_event_genre')]
    public function showEventGenre(EventGenreRepository $egr): Response
    {
        $eventGenres = $egr->findAll();

        return $this->render('event_genre/showGenres.html.twig', [
            'eventGenres' => $eventGenres,
        ]);
    }

    // update
    #[Route('/events/updateGenre/{id}', name: 'app_update_event_genre')]
    public function updateGenre($id, Request $request, ManagerRegistry $mr, EventGenreRepository $egr, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $eventGenres = $egr->findAll();

        $eventGenre = $egr->find($id);
        $form = $this->createForm(EventGenreType::class, $eventGenre);
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
                        $this->getParameter('event_genre_images_directory'),
                        $newFilename
                    );
                    $eventGenre->setImagePath($newFilename);
                } catch (FileException $fe) {
                    $this->addFlash('error', $fe->getMessage());
                }
            }

            $mr->getManager()->persist($eventGenre);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_show_event_genre');
        }

        return $this->render('event_genre/addGenre.html.twig', [
            'form' => $form,
            'eventGenres' => $eventGenres,

        ]);
    }

    // delete
    #[Route('/events/deleteEventGenres/{id}', name: 'app_delete_event_genre')]
    public function deleteEventGenres($id, EventGenreRepository $egr, ManagerRegistry $managerRegistry): Response
    {
        $eventGenres = $egr->find($id);
        $manager = $managerRegistry->getManager();
        $manager->remove($eventGenres);
        $manager->flush();


        return $this->redirectToRoute('app_show_event_genre');
    }

    
}
