<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\Order\Exception\NotFoundOrderException;
use App\Service\Order\Exception\TotalAmountException;
use App\Service\Product\ProductService;
use App\Service\ServiceInterface;
use App\Service\Status\OrderStatusService;
use App\Util\MoneyAmount;
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
     * @var OrderStatusService
     */
    private $orderStatusService;

    /**
     * @param EntityManager $entityManager
     * @param ProductService $productService
     * @param OrderStatusService $orderStatusService
     */
    public function __construct(
        EntityManager $entityManager,
        ProductService $productService,
        OrderStatusService $orderStatusService
    ) {
        $this->entityManager = $entityManager;
        $this->productService = $productService;
        $this->orderStatusService = $orderStatusService;
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
        $this->orderStatusService->setStatusNew($order);

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

    /**
     * Pay order
     *
     * @param int $orderId
     * @param MoneyAmount $amount
     * @return void
     * @throws \Throwable
     */
    public function pay(int $orderId, MoneyAmount $amount): void
    {
        /** @var Order $order */
        $order = $this->entityManager->getRepository(Order::class)
            ->findById($orderId);

        if ($order === null) {
            throw new NotFoundOrderException('Not found order');
        }

        $this->orderStatusService->validateCanPay($order);

        $total = $this->getTotal($order);

        if ($total->notEqual($amount)) {
            throw new TotalAmountException('Not correct amount');
        }

        $this->orderStatusService->setStatusProcessing($order, true);

        $this->entityManager->beginTransaction();
        try {
            if ($total->toApi() > 0) {
                // TODO pay
            }

            $this->orderStatusService->setStatusPaid($order, true);

            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            $this->orderStatusService->setStatusNew($order, true);

            throw $exception;
        }
    }

    /**
     * Get total
     *
     * @param Order $order
     * @return MoneyAmount
     */
    public function getTotal(Order $order): MoneyAmount
    {
        $result = MoneyAmount::fromReadable(0);

        foreach ($order->getOrderItems() as $item) {
            if ($item->getPrice() === null) {
                continue;
            }

            $result = $result->add($item->getPrice());
        }

        return $result;
    }
}