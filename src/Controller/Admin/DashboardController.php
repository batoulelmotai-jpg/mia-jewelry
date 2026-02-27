<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(
        ProductRepository $productRepo,
        OrderRepository $orderRepo
    ): Response {
        $productsCount = $productRepo->count([]);
        $ordersCount = $orderRepo->count([]);
        $newOrdersCount = $orderRepo->count(['status' => 'NOUVELLE']); // change if you use other status

        return $this->render('admin/dashboard/index.html.twig', [
            'productsCount' => $productsCount,
            'ordersCount' => $ordersCount,
            'newOrdersCount' => $newOrdersCount,
        ]);
    }
}