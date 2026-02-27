<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/products', name: 'admin_product_')]
final class ProductController extends AbstractController
{
    // -----------------------------
    // ADMIN : LISTE DES PRODUITS
    // -----------------------------
    #[Route(name: 'index', methods: ['GET'])]
    public function adminIndex(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    // -----------------------------
    // ADMIN : AJOUT PRODUIT
    // -----------------------------
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('products_directory'), $newFilename);
                $product->setImage($newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // -----------------------------
    // ADMIN : AFFICHAGE PRODUIT
    // -----------------------------
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    // -----------------------------
    // ADMIN : MODIFICATION PRODUIT
    // -----------------------------
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('products_directory'), $newFilename);
                $product->setImage($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // -----------------------------
    // ADMIN : SUPPRESSION PRODUIT
    // -----------------------------
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Produit supprimé.');
        return $this->redirectToRoute('admin_product_index');
    }
}

