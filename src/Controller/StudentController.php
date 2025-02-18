<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\AddstudentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class StudentController extends AbstractController
{
    #[Route('/student', name: 'student')]
    public function index(ManagerRegistry $doctrine , Request $request)
    {
        $em = $doctrine->getManager();
        $student = new Student();
        $form = $this->createForm(AddstudentType::class , $student);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($student);
            $em->flush();
            //traitement des donnees
            return $this->redirectToRoute('base');
        }


        return $this->render('Pages/user/signupstudent.html.twig', [
            'forms'  => $form->createView(),
        ]);
    }
}
