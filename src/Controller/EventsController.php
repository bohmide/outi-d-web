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

    // show front
    #[Route('/events/prof/showEvents', name: 'app_front_prof_show_events')]
    public function showEventGenreF(EvenementsRepository $er): Response
    {
        $events = $er->findAll();

        return $this->render('events/front/showEvents.html.twig', [
            'events' => $events,
        ]);
    }

    // show front
    #[Route('/events/etudiant/showEvents', name: 'app_front_etudiant_show_events')]
    public function showEventF(EvenementsRepository $er): Response
    {
        $events = $er->findAll();

        return $this->render('events/front/showEventsV2.html.twig', [
            'events' => $events,
        ]);
    }

    // show back
    #[Route('/admin/events/prof/showEvents', name: 'app_back_show_events')]
    public function showEventGenreB(EvenementsRepository $er): Response
    {
        $events = $er->findAll();

        return $this->render('events/back/showEvents.html.twig', [
            'events' => $events,
        ]);
    }


    // show front details
    #[Route('/events/etudiant/details/{id}', name: 'app_back_show_events')]
    public function showEventDetails($id, EvenementsRepository $er): Response
    {
        $event = $er->find($id);

        return $this->render('events/front/eventDetails.html.twig', [
            'event' => $event,
        ]);
    }


    // create
    #[Route('/events/addEvent', name: 'app_front_add_event')]
    public function addSponsor(Request $request, ManagerRegistry $mr, EvenementsRepository $er, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Evenements();
        $form = $this->createForm(EvenementsType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // check for genre name if exist
            $existEventName = $er->findOneBy(['nom_event' => $event->getNomEvent()]);
            if ($existEventName) {
                $this->addFlash('errorNameExist', 'This Event name already exists.');
                return $this->redirectToRoute('app_front_add_event');
            }
            if ($event->getDateEvent() == null) {
                $this->addFlash('errorDate', 'date is require');
                return $this->redirectToRoute('app_front_add_event');
            }

            $eventNbr = $event->getNbrMembers();
            if ($eventNbr < 0) {
                $this->addFlash('errorNbrLimit', 'number limit must be positive');
                return $this->render('events/front/addEvent.html.twig', [
                    'form' => $form,
                ]);
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
            $eventGenre->setNbr($eventGenre->getNbr() + 1);

            $mr->getManager()->persist($event);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_front_prof_show_events');
        }

        return $this->render('events/front/addEvent.html.twig', [
            'form' => $form,
        ]);
    }

    // update front
    #[Route('/events/updateEvent/{id}', name: 'app_front_update_event')]
    public function updateEventF($id, Request $request, ManagerRegistry $mr, EvenementsRepository $er, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = $er->find($id);
        $form = $this->createForm(EvenementsType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            // check the name
            $existEventName = $er->createQueryBuilder('e')
                ->where('e.nom_event = :nom_event')
                ->andWhere('e.id != :id')
                ->setParameter('nom_event', $event->getNomEvent())
                ->setParameter('id', $event->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if ($existEventName) {
                $this->addFlash('errorNameExist', 'This Event name already exists.');
                return $this->render('events/front/addEvent.html.twig', ['form' => $form,]);
            }


            // check the nbr limite
            $eventNbr = $event->getNbrMembers();
            if ($eventNbr < 0) {
                $this->addFlash('errorNbrLimit', 'number limit must be positive');
                return $this->render('events/front/addEvent.html.twig', [
                    'form' => $form,
                ]);
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

            $mr->getManager()->persist($event);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_front_prof_show_events');
        }

        return $this->render('events/front/addEvent.html.twig', [
            'form' => $form,
        ]);
    }

    // update back
    #[Route('admin/events/updateEvent/{id}', name: 'app_back_update_event')]
    public function updateEventB($id, Request $request, ManagerRegistry $mr, EvenementsRepository $er, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $events = $er->findAll();
        $event = $er->find($id);
        $form = $this->createForm(EvenementsType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $eventNbr = $event->getNbrMembers();
            if ($eventNbr < 0) {
                $this->addFlash('errorNbrLimit', 'number limit must be positive');
                return $this->render('events/back/addEvent.html.twig', [
                    'form' => $form,
                ]);
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

            $mr->getManager()->persist($event);
            $mr->getManager()->flush();
            return $this->redirectToRoute('app_back_show_events');
        }

        return $this->render('events/back/addEvent.html.twig', [
            'form' => $form,
            'events' => $events,
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
        $eventGenre->setNbr($eventGenre->getNbr() - 1);
        $manager->flush();


        return $this->redirectToRoute('app_front_prof_show_events');
    }
}
