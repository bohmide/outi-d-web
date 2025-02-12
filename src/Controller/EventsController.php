<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Form\EvenementsType;
use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EventsController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(): Response
    {
        return $this->render('events/index.html.twig', [
            'controller_name' => 'EventsController',
        ]);
    }

    // show
    #[Route('/events/showEvents', name: 'app_show_events')]
    public function showEventGenre(EvenementsRepository $er): Response
    {
        $events = $er->findAll();

        return $this->render('events/showEvents.html.twig', [
            'events' => $events,
        ]);
    }


    // create
    #[Route('/events/addEvent', name: 'app_add_event')]
    public function addSponsor(Request $request, ManagerRegistry $mr, EvenementsRepository $er, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Evenements();
        $form = $this->createForm(EvenementsType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // check for genre name if exist
            $existEventName = $er->findOneBy(['nom_event' => $event->getNomEvent()]);
            if ($existEventName) {
                $this->addFlash('error', 'This Event name already exists.');
                return $this->redirectToRoute('app_add_event');
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
                        $this->getParameter('events_images_directory'),
                        $newFilename
                    );
                    $event->setImagePath($newFilename);
                } catch (FileException $fe) {
                    $this->addFlash('error', $fe->getMessage());
                }
            }

            $eventGenre = $event->getGenre();
            $eventGenre->setNbr($eventGenre->getNbr()+1);

            $mr->getManager()->persist($event);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_show_events');
        }

        return $this->render('events/addEvent.html.twig', [
            'form' => $form,
        ]);
    }

    // update
    #[Route('/events/updateEvent/{id}', name: 'app_update_event')]
    public function updateSponsor($id, Request $request, ManagerRegistry $mr, EvenementsRepository $er, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = $er->find($id);
        $form = $this->createForm(EvenementsType::class, $event);
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
                        $this->getParameter('events_images_directory'),
                        $newFilename
                    );
                    $event->setImagePath($newFilename);
                } catch (FileException $fe) {
                    $this->addFlash('error', $fe->getMessage());
                }
            }

            $mr->getManager()->persist($event);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_show_events');
        }

        return $this->render('events/addEvent.html.twig', [
            'form' => $form,
        ]);
    }

    // delete
    #[Route('/events/deleteEvent/{id}', name: 'app_delete_event')]
    public function deleteSponosr($id, EvenementsRepository $er, ManagerRegistry $managerRegistry): Response
    {
        $event = $er->find($id);
        $manager = $managerRegistry->getManager();
        $manager->remove($event);
        $eventGenre = $event->getGenre();
        $eventGenre->setNbr($eventGenre->getNbr()-1);
        $manager->flush();


        return $this->redirectToRoute('app_show_events');
    }
}
