<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\RequestDTOInterface;
use App\DTO\ValidatorDTOInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ProductRandomDTO implements RequestDTOInterface, ValidatorDTOInterface
{
    /**
     * @var int
     */
    private $count;

    /**
     * @inheritdoc
     */
    public function __construct(Request $request)
    {
        $this->count = $request->get('count');
    }

    /**
     * @inheritdoc
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('count', new Assert\Type([
            'type' => ['integer', 'null'],
        ]));
        $metadata->addPropertyConstraint('count', new Assert\Range([
            'min' => 1,
            'max' => 100,
        ]));
    }

    /**
     * Get count of random product to create
     *
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }
}