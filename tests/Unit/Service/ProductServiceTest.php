<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Entity\Product;
use App\Service\Product\ProductService;
use Tests\BaseTestCase;
use Tests\DatabaseTransactions;

class ProductServiceTest extends BaseTestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        $name = $this->faker->name;
        $price = $this->faker->numberBetween($min = 0, $max = 1000);

        $product = $this->getProductService()->create($name, $price);
        $productNotFlushed = $this->getProductService()->create($name, $price, false);

        $this->assertNotNull($product->getId());
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());

        $this->assertNull($productNotFlushed->getId());
        $this->assertEquals($name, $productNotFlushed->getName());
        $this->assertEquals($price, $productNotFlushed->getPrice());
    }

    public function testCreateRandom(): void
    {
        $product = $this->getProductService()->createRandom();
        $productNotFlushed = $this->getProductService()->createRandom(false);

        $this->assertNotNull($product->getId());
        $this->assertNull($productNotFlushed->getId());
    }

    public function testBatchCreateRandom(): void
    {
        $count = 3;

        $em = $this->getEntityManager();
        $repository = $em->getRepository(Product::class);
        $totalPrev = $repository->getAllCount();

        $products = $this->getProductService()->batchCreateRandom($count);
        $totalAfter = $repository->getAllCount();

        $this->assertCount($count, $products);
        $this->assertEquals($totalAfter, $totalPrev + $count);
    }

    public function testBatchCreateRandomByDefaultCount(): void
    {
        $products = $this->getProductService()->batchCreateRandom(null);

        $this->assertCount($this->getProductService()->getDefaultCount(), $products);
    }

    public function testBatchCreateRandomCountNotValid(): void
    {
        $productsLessZero = $this->getProductService()->batchCreateRandom(-1);
        $productsZero = $this->getProductService()->batchCreateRandom(0);

        $this->assertCount(0, $productsLessZero);
        $this->assertCount(0, $productsZero);
    }


    /**
     * @return ProductService
     */
    private function getProductService(): ProductService
    {
        return self::$container->get(ProductService::class);
    }
}