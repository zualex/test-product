<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Order;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\Order\Exception\NotFoundOrderException;
use App\Service\Order\Exception\TotalAmountException;
use App\Service\Order\OrderService;
use App\Service\Product\Exception\NotExistProductIdException;
use App\Service\Product\ProductService;
use App\Service\Status\Exception\OrderStatusException;
use App\Service\Status\OrderStatusService;
use App\Util\MoneyAmount;
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

        $this->getOrderService()->create([-1, -2]);
    }

    public function testGetTotal(): void
    {
        $priceFirst = MoneyAmount::fromReadable(10.55);
        $priceSecond = MoneyAmount::fromReadable(0);
        $priceThird = MoneyAmount::fromReadable(0.45);

        $productFirst = $this->getProductService()->create('product_1', $priceFirst);
        $productSecond = $this->getProductService()->create('product_2', $priceSecond);
        $productThird = $this->getProductService()->create('product_3', $priceThird);

        $order = $this->getOrderService()->create([
            $productFirst->getId(),
            $productSecond->getId(),
            $productThird->getId(),
        ]);

        $total = $this->getOrderService()->getTotal($order);
        $expectedTotal = $priceFirst->add($priceThird);

        $this->assertTrue($total->equal($expectedTotal));
    }

    public function testPayWithoutPurchase(): void
    {
        $price = MoneyAmount::fromReadable(0);
        $product = $this->getProductService()->create('product_1', $price);
        $order = $this->getOrderService()->create([$product->getId()]);

        $this->getOrderService()->pay($order->getId(), $price);

        $this->assertEquals(Order::STATUS_PAID, $order->getStatus());
    }

    public function testPayWithNotFoundException(): void
    {
        $this->expectException(NotFoundOrderException::class);

        $this->getOrderService()->pay(-1, MoneyAmount::fromReadable(0));
    }

    public function testPayWithTotalAmountException(): void
    {
        $this->expectException(TotalAmountException::class);

        $price = MoneyAmount::fromReadable(0);
        $product = $this->getProductService()->create('product_1', $price);
        $order = $this->getOrderService()->create([$product->getId()]);

        $this->getOrderService()->pay($order->getId(), MoneyAmount::fromReadable(-1));
    }

    public function testPayWithOrderStatusException(): void
    {
        $this->expectException(OrderStatusException::class);

        $price = MoneyAmount::fromReadable(0);
        $product = $this->getProductService()->create('product_1', $price);
        $order = $this->getOrderService()->create([$product->getId()]);

        $this->getOrderStatusService()->setStatusProcessing($order);

        $this->getOrderService()->pay($order->getId(), MoneyAmount::fromReadable(0));
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

    /**
     * @return OrderStatusService
     */
    private function getOrderStatusService(): OrderStatusService
    {
        return self::$container->get(OrderStatusService::class);
    }
}