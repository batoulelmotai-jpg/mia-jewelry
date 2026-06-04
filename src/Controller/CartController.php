<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\ProductRepository;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'app_cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cartItems = $session->get('cart', []);
        $cart = [];
        $total = 0;

        foreach ($cartItems as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $subtotal = $product->getPrice() * $quantity;
                $total += $subtotal;
                $cart[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(
        int $id,
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository
    ): Response {
        $product = $productRepository->find($id);

        if (!$product) {
            $this->addFlash('error', 'Produit introuvable.');
            return $this->redirectToRoute('app_catalogue');
        }

        $quantity = (int) $request->request->get('quantity', 1);
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        if ($cart[$id] > $product->getStock()) {
            $cart[$id] = $product->getStock();
            $this->addFlash('error', 'Stock insuffisant.');
        }

        $session->set('cart', $cart);
        $this->addFlash('success', '✦ ' . $product->getName() . ' ajouté au panier !');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/modifier/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(
        int $id,
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository
    ): Response {
        $quantity = (int) $request->request->get('quantity', 1);
        $cart = $session->get('cart', []);

        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $product = $productRepository->find($id);
            if ($product && $quantity <= $product->getStock()) {
                $cart[$id] = $quantity;
            }
        }

        $session->set('cart', $cart);
        $this->addFlash('success', 'Panier mis à jour.');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_cart_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        unset($cart[$id]);
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit retiré du panier.');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/vider', name: 'app_cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');
        $this->addFlash('success', 'Panier vidé.');
        return $this->redirectToRoute('app_cart');
    }
}