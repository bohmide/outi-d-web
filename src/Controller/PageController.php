<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class PageController extends AbstractController{

    #[Route('/home', name: 'base')]
        public function base(): Response
        {
            return $this->render('pages/index.html.twig');
        }
    
        #[Route('/forum', name: 'forum')]
        public function about(): Response
        {
            return $this->render('forum/showforum.html.twig');
        }
    
        #[Route('/courses', name: 'courses')]
        public function courses(): Response
        {
            return $this->render('front/cours/courA.html.twig');
        }
    
        #[Route('/trainers', name: 'trainers')]
        public function trainers(): Response
        {
            return $this->render('pages/trainers.html.twig');
        }
    
        #[Route('/events', name: 'events')]
        public function events(): Response
        {
            return $this->render('../events/showEvents.html.twig');
        }
    
        #[Route('/pricing', name: 'pricing')]
        public function pricing(): Response
        {
            return $this->render('pages/pricing.html.twig');
        }
        #[Route('/contact', name: 'contact')]
        public function contacting(): Response
        {
            return $this->render('pages/contact.html.twig');
        }
        
        #[Route('/getStarted', name: 'getStarted')]
        public function getstarted(): Response
        {
            return $this->render('pages/courses.html.twig');
        }


        #[Route('/login', name: 'login')]
        public function login(): Response
        {
            return $this->render('pages/user/login.html.twig');
        }

        #[Route('/prof', name: 'prof')]
        public function prof(): Response
        {
            return $this->render('pages/user/prof.html.twig');
        }

        #[Route('/parent', name: 'parent')]
        public function parent(): Response
        {
            return $this->render('pages/user/signupparent.html.twig');
        }

        #[Route('/student', name: 'student')]
        public function student(): Response
        {
            return $this->render('pages/user/student.html.twig');
        }

        #[Route('/showparent', name: 'showparent')]
        public function parentback(): Response
        {
            return $this->render('pages/user/parentback.html.twig');
        }
        
        #[Route('/showprof', name: 'showprof')]
        public function proftback(): Response
        {
            return $this->render('pages/user/profback.html.twig');
        }
        
        #[Route('/showstudent', name: 'showstudent')]
        public function studenttback(): Response
        {
            return $this->render('pages/user/studentback.html.twig');
        }

        #[Route('/updateparent', name: 'updateparent')]
        public function updateparent(): Response
        {
            return $this->render('pages/user/parentback.html.twig');
        }

        #[Route('/deleteparent', name: 'deleteparent')]
        public function deleteparent(): Response
        {
            return $this->render('pages/user/parentback.html.twig');
        }

        #[Route('/updateprof', name: 'updateprof')]
        public function updateprof(): Response
        {
            return $this->render('pages/user/profback.html.twig');
        }

        #[Route('/deleteprof', name: 'deleteprof')]
        public function deleteprof(): Response
        {
            return $this->render('pages/user/profback.html.twig');
        }

        #[Route('/updatestudent', name: 'updatestudent')]
        public function updatestudent(): Response
        {
            return $this->render('pages/user/studentback.html.twig');
        }

        #[Route('/deletestudent', name: 'deletestudent')]
        public function deletestudent(): Response
        {
            return $this->render('pages/user/studentback.html.twig');
        }

        #[Route('/', name: 'use')]
        public function quiz(): Response
        {
            return $this->render('pages/use.html.twig');
        }

        #[Route('/profCours', name: 'profcours')]
        public function profC(): Response
        {
            return $this->render('cours/coursProf.html.twig');
        }
}

