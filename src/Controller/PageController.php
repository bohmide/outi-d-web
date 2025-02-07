<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class PageController extends AbstractController{

    #[Route('/', name: 'base')]
        public function base(): Response
        {
            return $this->render('pages/index.html.twig');
        }
    
        #[Route('/about', name: 'about')]
        public function about(): Response
        {
            return $this->render('pages/about.html.twig');
        }
    
        #[Route('/courses', name: 'courses')]
        public function courses(): Response
        {
            return $this->render('pages/courses.html.twig');
        }
    
        #[Route('/trainers', name: 'trainers')]
        public function trainers(): Response
        {
            return $this->render('pages/trainers.html.twig');
        }
    
        #[Route('/events', name: 'events')]
        public function events(): Response
        {
            return $this->render('pages/events.html.twig');
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
}
