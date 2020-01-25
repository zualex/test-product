<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Entity\Product;
use App\Service\ProductService;
use Tests\BaseTestCase;
use Tests\DatabaseTransactions;

class ProductServiceTest extends BaseTestCase
{
    use DatabaseTransactions;

    public function testBasicTest()
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Product::class);

        $count = 5;
        $totalPrev = $repository->getAllCount();

        /** @var ProductService $productService */
        $productService = self::$container->get('App.Service.ProductService');
        $productIds = $productService->batchCreateRandom($count);

        $totalAfter = $repository->getAllCount();

        $this->assertCount($count, $productIds);
        $this->assertEquals($totalAfter, $totalPrev + $count);
    }
}