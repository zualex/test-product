<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\RequestDTOInterface;
use App\DTO\ValidatorDTOInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CreateOrderRequestDTO implements RequestDTOInterface, ValidatorDTOInterface
{
    /**
     * @var array
     */
    private $product_ids;

    /**
     * @inheritdoc
     */
    public function __construct(Request $request)
    {
        $this->product_ids = $request->get('product_ids');
    }

    /**
     * @inheritdoc
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('product_ids', new Assert\NotBlank());
        $metadata->addPropertyConstraint('product_ids', new Assert\Type([
            'type' => 'array',
        ]));
        $metadata->addPropertyConstraint('product_ids', new Assert\Count([
            'min' => 1,
            'max' => 100,
        ]));
    }

    /**
     * Get list product ids
     *
     * @return array
     */
    public function getProductIds(): array
    {
        return $this->product_ids;
    }
}