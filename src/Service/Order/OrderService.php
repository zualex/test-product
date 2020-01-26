<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\DTO\Request\CreateOrderRequestDTO;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\Product\ProductService;
use App\Service\ServiceInterface;
use Doctrine\ORM\EntityManager;

class OrderService implements ServiceInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @param EntityManager $entityManager
     * @param ProductService $productService
     */
    public function __construct(EntityManager $entityManager, ProductService $productService)
    {
        $this->entityManager = $entityManager;
        $this->productService = $productService;
    }

    /**
     * @param array $productIds
     *
     * @return Order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(array $productIds): Order
    {
        $this->productService->validateProductIds($productIds);

        $products = $this->entityManager->getRepository(Product::class)
            ->findByIds($productIds);

        $order = new Order();
        $order->setStatus(Order::STATUS_NEW);

        $this->entityManager->persist($order);

        foreach ($products as $product) {
            $item = new OrderItem();
            $item->setProduct($product);
            $item->setPrice($product->getPrice());
            $item->setName($product->getName());
            $item->setCount(1);

            $order->addOrderItem($item);

            $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

        return $order;
    }
}