<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Status;

use App\Entity\Order;
use App\Service\Order\OrderService;
use App\Service\Product\ProductService;
use App\Service\Status\Exception\OrderStatusException;
use App\Service\Status\OrderStatusService;
use Tests\BaseTestCase;
use Tests\DatabaseTransactions;

class OrderStatusServiceTest extends BaseTestCase
{
    use DatabaseTransactions;

    public function testStatus(): void
    {
        $order = $this->createOrder();
        $orderStatusService = $this->getOrderStatusService();

        $this->assertEquals(Order::STATUS_NEW, $order->getStatus());
        $this->assertTrue($orderStatusService->isStatusNew($order));

        $orderStatusService->setStatusPaid($order);
        $this->assertEquals(Order::STATUS_PAID, $order->getStatus());
        $this->assertTrue($orderStatusService->isStatusPaid($order));

        $orderStatusService->setStatusNew($order);
        $this->assertEquals(Order::STATUS_NEW, $order->getStatus());
        $this->assertTrue($orderStatusService->isStatusNew($order));
    }

    public function testCanPay(): void
    {
        $order = $this->createOrder();
        $orderStatusService = $this->getOrderStatusService();

        $orderStatusService->setStatusPaid($order);
        $this->assertFalse($orderStatusService->canPay($order));

        $orderStatusService->setStatusNew($order);
        $this->assertTrue($orderStatusService->canPay($order));
    }

    public function testValidateCanPay(): void
    {
        $this->expectException(OrderStatusException::class);

        $order = $this->createOrder();
        $orderStatusService = $this->getOrderStatusService();

        $orderStatusService->setStatusPaid($order);
        $orderStatusService->validateCanPay($order);
    }

    private function createOrder(): Order
    {
        /** @var ProductService $productService */
        $productService = self::$container->get(ProductService::class);

        /** @var OrderService $orderService */
        $orderService = self::$container->get(OrderService::class);

        list($product) = $productService->batchCreateRandom(1);

        return $orderService->create([$product->getId()]);
    }

    /**
     * @return OrderStatusService
     */
    private function getOrderStatusService(): OrderStatusService
    {
        return self::$container->get(OrderStatusService::class);
    }
}