<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'products_index')]
    public function index(ProductRepository $repo): Response
    {
        return $this->render('products/index.html.twig', [
            'products' => $repo->findAll()
        ]);
    }
}
