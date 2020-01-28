<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\Product;

class ProductIdsResponseDTO extends ResponseDTO
{
    public const OBJECT = 'product_ids';

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct(self::OBJECT, $data);
    }

    /**
     * @param array $products
     * @return ProductIdsResponseDTO
     */
    public static function createFromListProductEntity(array $products): self
    {
        $data = array_map(static function ($item) {
            /** @var $item Product */
            return $item->getId();
        }, $products);

        return new self($data);
    }
}