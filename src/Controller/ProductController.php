<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\ProductRandomDTO;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController
{
    /**
     * Create random product
     *
     * @param ProductRandomDTO $productRandomDTO
     * @return Response
     */
    public function createRandom(ProductRandomDTO $productRandomDTO): Response
    {
        return $this->json([
            'count' => $productRandomDTO->getCount()
        ]);
    }
}