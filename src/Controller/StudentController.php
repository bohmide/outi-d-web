<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\StudentRepository;
use Symfony\Component\Routing\Attribute\Route;

final class StudentController extends AbstractController
{
    #[Route('/newstudent', name: 'Signstudent')]
    public function newStudent(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $student = new Student();
        $formStudent = $this->createForm(StudentType::class, $student);
        $formStudent->handleRequest($request);

        if ($formStudent->isSubmitted() && $formStudent->isValid()) {
            $password = $formStudent->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($student, $student->getPassword());
            $student->setPassword($hashedPassword);

            $student->setRoles(['ROLE_STUDENT']);

            $entityManager->persist($student);
            $entityManager->flush();

            $this->addFlash('success', 'Student created successfully!');
            return $this->redirectToRoute('base');
        }

        return $this->render('Pages/User/signupstudent.html.twig', [
            'forms' => $formStudent->createView(),
        ]);
    }

    #[Route('/showstudents', name: 'showstudents')]
    public function showStudents(StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAll();

        if (!$students) {
            $this->addFlash('warning', 'No students found in the database.');
        }

        return $this->render('Pages/User/studentback.html.twig', [
            'st' => $students,
        ]);
    }

    #[Route('/deletestudent/{id}', name: 'deletestudent')]
    public function deleteStudent(ManagerRegistry $doctrine, int $id, StudentRepository $rep): Response
    {
        $student = $rep->find($id);
        
        if (!$student) {
            throw $this->createNotFoundException('No student found for id ' . $id);
        }
        
        $em = $doctrine->getManager();
        $em->remove($student);
        $em->flush();

        return $this->redirectToRoute('showstudents');
    }

    #[Route('/updatestudent/{id}', name: 'updatestudent')]
    public function updateStudent(ManagerRegistry $doctrine, Request $request, int $id, StudentRepository $rep): Response
    {
        $student = $rep->find($id);

        if (!$student) {
            throw $this->createNotFoundException('No student found for id ' . $id);
        }

        $formStudent = $this->createForm(StudentType::class, $student);
        $formStudent->handleRequest($request);

        if ($formStudent->isSubmitted() && $formStudent->isValid()) {
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('showstudents');
        }

        return $this->render('Pages/User/updatestudent.html.twig', [
            'forms2' => $formStudent->createView(),
        ]);
    }
}
