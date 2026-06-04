<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function catalogue(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $categorySlug = $request->query->get('category');
        $categories = $categoryRepository->findAll();
        $currentCategory = null;

        if ($categorySlug) {
            $currentCategory = $categoryRepository->findOneBy(['slug' => $categorySlug]);
            $products = $productRepository->findBy(
                ['isActive' => true, 'category' => $currentCategory],
                ['createdAt' => 'DESC']
            );
        } else {
            $products = $productRepository->findBy(
                ['isActive' => true],
                ['createdAt' => 'DESC']
            );
        }

        return $this->render('product/catalogue.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_product_show')]
    public function show(\App\Entity\Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}