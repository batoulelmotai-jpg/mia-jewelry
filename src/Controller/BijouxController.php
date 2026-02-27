<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BijouxController extends AbstractController
{
    #[Route('/bijoux', name: 'bijoux')]
    public function index(): Response
    {
        return $this->render('bijoux/index.html.twig');
    }
}
