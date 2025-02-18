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

