<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\DTO\Request\CreateProductRandomRequestDTO;
use App\DTO\Response\ProductIdsResponseDTO;
use App\Service\Product\ProductService;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController
{
    /**
     * Create random product
     *
     * @param CreateProductRandomRequestDTO $createProductRandomRequestDTO
     * @param ProductService $productService
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createRandom(
        CreateProductRandomRequestDTO $createProductRandomRequestDTO,
        ProductService $productService
    ): Response {
        $count = $createProductRandomRequestDTO->getCount();
        $products = $productService->batchCreateRandom($count);
        $responseDTO = ProductIdsResponseDTO::createFromListProductEntity($products);

        return $this->json($responseDTO);
    }
}