<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\DTO\Request\ProductRandomDTO;
use App\Service\Product\ProductService;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController
{
    /**
     * Create random product
     *
     * @param ProductRandomDTO $productRandomDTO
     * @param ProductService $productService
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createRandom(ProductRandomDTO $productRandomDTO, ProductService $productService): Response
    {
        $count = $productRandomDTO->getCount();

        return $this->json([
            'product_ids' => $productService->batchCreateRandom($count)
        ]);
    }
}