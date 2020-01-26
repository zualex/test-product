<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Product;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\Order\OrderService;
use App\Service\Product\Exception\NotExistProductIdException;
use App\Service\Product\ProductService;
use Tests\BaseTestCase;
use Tests\DatabaseTransactions;

class OrderServiceTest extends BaseTestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        list($productFirst, $productSecond) = $this->getProductService()->batchCreateRandom(2);

        $order = $this->getOrderService()->create([
            $productFirst->getId(),
            $productSecond->getId(),
        ]);

        $this->assertNotNull($order->getId());
        $this->assertEquals(Order::STATUS_NEW, $order->getStatus());
        $this->assertCount(2, $order->getOrderItems());

        /** @var OrderItem $orderItemFirst */
        $orderItemSecond = $order->getOrderItems()->filter(function ($orderItem) use ($productSecond) {
            return $orderItem->getProduct()->getId() === $productSecond->getId();
        })->first();

        $this->assertNotNull($orderItemSecond->getId());
        $this->assertEquals($orderItemSecond->getName(), $productSecond->getName());
        $this->assertEquals($orderItemSecond->getPrice(), $productSecond->getPrice());
    }

    public function testCreateWithException(): void
    {
        $this->expectException(NotExistProductIdException::class);

        $order = $this->getOrderService()->create([-1, -2]);
    }

    /**
     * @return OrderService
     */
    private function getOrderService(): OrderService
    {
        return self::$container->get(OrderService::class);
    }

    /**
     * @return ProductService
     */
    private function getProductService(): ProductService
    {
        return self::$container->get(ProductService::class);
    }
}