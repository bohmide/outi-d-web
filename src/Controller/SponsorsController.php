<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SponsorsController extends AbstractController
{
    #[Route('/sponsors', name: 'app_sponsors')]
    public function index(): Response
    {
        return $this->render('sponsors/index.html.twig', [
            'controller_name' => 'SponsorsController',
        ]);
    }
}
