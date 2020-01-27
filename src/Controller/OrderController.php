<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\DTO\Request\CreateOrderRequestDTO;
use App\DTO\Response\OrderResponseDTO;
use App\Service\Order\OrderService;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends BaseController
{
    /**
     * Create order
     *
     * @param CreateOrderRequestDTO $createOrderDTO
     * @param OrderService $orderService
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(CreateOrderRequestDTO $createOrderDTO, OrderService $orderService): Response
    {
        $productIds = $createOrderDTO->getProductIds();
        $order = $orderService->create($productIds);
        $responseDTO = OrderResponseDTO::createFromOrderEntity($order);

        return $this->json($responseDTO);
    }
}