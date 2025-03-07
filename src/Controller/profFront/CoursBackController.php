<?php

namespace App\Controller\profFront;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Cours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Certification;
use App\Repository\CoursRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\CoursType;
use App\Repository\ChapitreRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\PdfGenerator;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class CoursBackController extends AbstractController

{
    
    #[Route('/Profshow', name: 'app_cours_back')]
    public function index(CoursRepository $coursRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération des cours sous forme de requête Doctrine
        $query = $coursRepository->createQueryBuilder('c')->getQuery();
        
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query, // Requête Doctrine
            $request->query->getInt('page', 1), // Numéro de la page actuelle
            2 // Nombre d'éléments par page
        );
    
        return $this->render('profFrontCours/cours/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'admin_cours_new')]
public function new(Request $request, ManagerRegistry $ma, PdfGenerator $pdfGenerator, Environment $twig): Response
{
    $em = $ma->getManager();
    $cours = new Cours();
    $cours->setDateCreation(new \DateTimeImmutable('today'));

    $form = $this->createForm(CoursType::class, $cours);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $isCertifie = $form->get('is_certifie')->getData();

        if ($isCertifie) {
            $certification = new Certification();
            $certification->setNomCertification($cours->getNom());
            $em->persist($certification);

            // 🔹 1ère sauvegarde pour générer l'ID du cours
            $em->persist($cours);
            $em->flush(); // Maintenant, $cours->getId() est disponible

            $cours->setCertification($certification);

            // 🔹 Génération du PDF avec l'ID du cours
            $pdfFilename = $pdfGenerator->generateCertificationPdf($cours, $twig);
            $certification->setPdfFilename($pdfFilename);

            // 🔹 2ème sauvegarde pour enregistrer le fichier PDF
            $em->flush();
        } else {
            $em->persist($cours);
            $em->flush();
        }

        $this->addFlash('success', 'Le cours a été créé avec succès.');
        return $this->redirectToRoute('app_cours_back');
    }

    return $this->render('profFrontCours/cours/new.html.twig', [
        'form' => $form,
    ]);
}

    
    #[Route('/{id}/update', name: 'admin_cours_update')]
    public function updatecours(ManagerRegistry $m, Request $req, int $id, CoursRepository $rep, PdfGenerator $pdfGenerator, Environment $twig): Response 
    {
        $em = $m->getManager();
        $cours = $rep->find($id);
    
        if (!$cours) {
            throw $this->createNotFoundException('Cours introuvable');
        }
    
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $isCertifie = $form->get('is_certifie')->getData();
    
            if ($isCertifie) {
                if ($cours->getCertification() === null) {
                    $certification = new Certification();
                    $certification->setNomCertification($cours->getNom());
                    $em->persist($certification);
                    $em->flush(); // Nécessaire pour avoir l'ID
                    $cours->setCertification($certification);
                }
    
                // Génération du PDF avec un nom unique basé sur l'ID du cours
                $pdfFilename = $pdfGenerator->generateCertificationPdf($cours, $twig);
                $cours->getCertification()->setPdfFilename($pdfFilename);
            } else {
                if ($cours->getCertification()) {
                    $em->remove($cours->getCertification());
                    $cours->setCertification(null);
                }
            }
    
            $em->persist($cours);
            $em->flush();
    
            return $this->redirectToRoute('app_cours_back');
        }
    
        return $this->render('profFrontCours/cours/new.html.twig', [
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'admin_cours_delete', methods: ['POST', 'DELETE'])]
    public function deleteCours(ManagerRegistry $m, $id, CoursRepository $rep): RedirectResponse
    {
        $em = $m->getManager();
        $cours = $rep->find($id);
    
        if (!$cours) {
            // Ajouter un message flash d'erreur si le cours n'est pas trouvé
            $this->addFlash('error', 'Cours introuvable');
            return $this->redirectToRoute('app_cours_back'); 
        }
    
        try {
            // Suppression du cours
            $em->remove($cours);
            $em->flush();
    
            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le cours a été supprimé avec succès.');
    
        } catch (\Exception $e) {
            // Ajouter un message flash d'erreur en cas d'échec
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du cours.');
        }
    
        // Rediriger vers la liste des cours après la suppression
        return $this->redirectToRoute('app_cours_back');
    }

    
        #[Route('/{id}/chapitres', name: 'admin_cours_chapitres')]
        public function showChapitreCours(ChapitreRepository $chapRepo,Cours $cours): Response
        {
             //  only chapters that belong to the selected cours
            $chapitres = $chapRepo->findBy(['cours' => $cours]);
                       return $this->render('profFrontCours/chapitre/listChapitre.html.twig', [
                       'cours' => $cours,
                       'chapitres' => $chapitres, // Fetching from the database
            ]);
        }


        #[Route('/recherche/cours', name: 'search_courses', methods: ['GET'])]
public function searchCourses(Request $request, CoursRepository $coursRepository): JsonResponse
{
    $query = $request->query->get('query');  // Le terme de recherche envoyé par la requête AJAX
    
    // Si le terme de recherche est vide, on retourne tous les cours
    if ($query) {
        $cours = $coursRepository->createQueryBuilder('c')
            ->where('c.nom LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    } else {
        // Retourner tous les cours si la recherche est vide
        $cours = $coursRepository->findAll();
    }

    // Renvoyer la réponse JSON avec les cours filtrés
    $coursesData = array_map(function($course) {
        return [
            'nom' => $course->getNom(),
            'etat' => $course->getEtat(),
            'dateCreation' => $course->getDateCreation()->format('d/m/Y'),
            'id' => $course->getId()
        ];
    }, $cours);

    return new JsonResponse(['courses' => $coursesData]);
}

    
}


