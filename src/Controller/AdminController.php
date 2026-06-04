<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return $this->render('admin/index.html.twig', [
            'totalProducts'   => count($productRepository->findAll()),
            'totalOrders'     => count($orderRepository->findAll()),
            'totalUsers'      => count($userRepository->findAll()),
            'totalCategories' => count($categoryRepository->findAll()),
            'recentOrders'    => $orderRepository->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }

    #[Route('/produits', name: 'app_admin_products')]
    public function products(ProductRepository $productRepository): Response
    {
        return $this->render('admin/products.html.twig', [
            'products' => $productRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/produits/nouveau', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function newProduct(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): Response {
        if ($request->isMethod('POST')) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($request->request->get('price'));
            $product->setStock((int) $request->request->get('stock'));
            $product->setMaterial($request->request->get('material'));
            $product->setIsActive($request->request->get('isActive') ? true : false);

            $category = $categoryRepository->find($request->request->get('category'));
            $product->setCategory($category);

            $imageFile = $request->files->get('image');
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images/products',
                    $newFilename
                );
                $product->setImageName($newFilename);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit créé avec succès !');
            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('admin/product_form.html.twig', [
            'product'    => null,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/produits/{id}/modifier', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
    public function editProduct(
        Product $product,
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): Response {
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($request->request->get('price'));
            $product->setStock((int) $request->request->get('stock'));
            $product->setMaterial($request->request->get('material'));
            $product->setIsActive($request->request->get('isActive') ? true : false);

            $category = $categoryRepository->find($request->request->get('category'));
            $product->setCategory($category);

            $imageFile = $request->files->get('image');
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images/products',
                    $newFilename
                );
                $product->setImageName($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('admin/product_form.html.twig', [
            'product'    => $product,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/produits/{id}/supprimer', name: 'app_admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Produit supprimé.');
        return $this->redirectToRoute('app_admin_products');
    }

    #[Route('/categories', name: 'app_admin_categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/categories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/categories/nouveau', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $category = new Category();
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));
            $category->setSlug(strtolower(str_replace(' ', '-', $request->request->get('name'))));

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée !');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/category_form.html.twig', ['category' => null]);
    }

    #[Route('/categories/{id}/modifier', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));
            $category->setSlug(strtolower(str_replace(' ', '-', $request->request->get('name'))));

            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée !');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/category_form.html.twig', ['category' => $category]);
    }

    #[Route('/categories/{id}/supprimer', name: 'app_admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Catégorie supprimée.');
        return $this->redirectToRoute('app_admin_categories');
    }

    #[Route('/commandes', name: 'app_admin_orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        return $this->render('admin/orders.html.twig', [
            'orders' => $orderRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/commandes/{id}/statut', name: 'app_admin_order_status', methods: ['POST'])]
    public function updateOrderStatus(
        \App\Entity\Order $order,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $order->setStatus($request->request->get('status'));
        $em->flush();
        $this->addFlash('success', 'Statut mis à jour !');
        return $this->redirectToRoute('app_admin_orders');
    }
    #[Route('/clients', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
   {
    return $this->render('admin/users.html.twig', [
        'users' => $userRepository->findBy([], ['createdAt' => 'DESC']),
    ]);
   }
}