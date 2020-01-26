<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\DTO\Request\CreateOrderRequestDTO;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends BaseController
{
    /**
     * Create random product
     *
     * @param CreateOrderRequestDTO $createOrderDTO
     * @return Response
     */
    public function create(CreateOrderRequestDTO $createOrderDTO): Response
    {
        $productIds = $createOrderDTO->getProductIds();
        dd($productIds);


        return $this->json('123');
    }
}