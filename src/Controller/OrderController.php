<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    #[Route('/commande/checkout', name: 'app_order_checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkout(
        SessionInterface $session,
        ProductRepository $productRepository
    ): Response {
        $cartItems = $session->get('cart', []);

        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

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

        return $this->render('order/checkout.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/commande/paiement', name: 'app_order_payment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function payment(
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $cartItems = $session->get('cart', []);

        if (empty($cartItems)) {
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setShippingAddress($request->request->get('address'));
        $order->setStatus(Order::STATUS_PAID);

        $total = 0;

        foreach ($cartItems as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setUnitPrice($product->getPrice());
                $orderItem->setOrder($order);

                $total += $product->getPrice() * $quantity;

                $newStock = $product->getStock() - $quantity;
                $product->setStock(max(0, $newStock));

                $entityManager->persist($orderItem);
            }
        }

        $order->setTotalAmount((string) $total);
        $entityManager->persist($order);
        $entityManager->flush();

        $session->remove('cart');

        $this->addFlash('success', '✦ Commande confirmée ! Merci pour votre achat.');

        return $this->redirectToRoute('app_order_confirmation', [
            'id' => $order->getId()
        ]);
    }

    #[Route('/commande/confirmation/{id}', name: 'app_order_confirmation')]
    #[IsGranted('ROLE_USER')]
    public function confirmation(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/compte/commandes', name: 'app_order_history')]
    #[IsGranted('ROLE_USER')]
    public function history(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
        ]);
    }
}