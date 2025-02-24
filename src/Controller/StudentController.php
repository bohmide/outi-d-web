<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\AddstudentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;

final class StudentController extends AbstractController
{
    #[Route('/student', name: 'student')]
    public function index(ManagerRegistry $doctrine , Request $request)
    {
        $em = $doctrine->getManager();
        $student = new Student();
        $forms = $this->createForm(AddstudentType::class , $student);

        $forms->handleRequest($request);
        if($forms->isSubmitted() && $forms->isValid()){
            $em->persist($student);
            $em->flush();
            //traitement des donnees
            return $this->redirectToRoute('base');
        }


        return $this->render('Pages/user/signupstudent.html.twig', [
            'forms'  => $forms->createView(),
        ]);
    }

    #[Route('/showstudent', name: 'showstudent')]
    public function showStudents(StudentRepository $studentRepository): Response
    {
        // Récupérer tous les enregistrements de la table Student
        $students = $studentRepository->findAll();

        // Vérifier les données récupérées
        if (!$students) {
            $this->addFlash('warning', 'No students found in the database.');
        }

        return $this->render('Pages/user/studentback.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/deletestudent/{id}', name: 'deletestudent')]
    public function deletestudent(ManagerRegistry $doctrine, int $id, StudentRepository $rep): Response
    {
        // Récupérer le parent à supprimer
        $student = $rep->find($id);
        
        // Vérifier si le parent existe
        if (!$student) {
            throw $this->createNotFoundException('No parent found for id ' . $id);
        }

        // Action suppression
        $em = $doctrine->getManager();
        
        // Préparation de la suppression
        $em->remove($student);

        // Exécution de la commande de suppression
        $em->flush();

        return $this->redirectToRoute('showstudent'); // Vous devrez créer la route de redirection appropriée
    }

    #[Route('/updatestudent/{id}', name: 'updatestudent')]
    public function updatestudent(ManagerRegistry $m , Request $req , int $id , StudentRepository $rep): Response
    {
        $em = $m->getManager();
        $student = $rep->find($id);
        
        if (!$student) {
            throw $this->createNotFoundException('No student found for id ' . $id);
        }

        $forms2 = $this->createForm(AddstudentType::class, $student);
        $forms2->handleRequest($req);
        
        if ($forms2->isSubmitted() && $forms2->isValid()) {
            $em->persist($student);
            $em->flush();
            return $this->redirectToRoute('showstudent');
        }
        
        return $this->render('Pages/user/updatestudent.html.twig', [
            'forms2' => $forms2,
        ]);
    }


}

