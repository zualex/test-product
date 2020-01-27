<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\DTO\RequestDTOInterface;
use App\DTO\ValidatorDTOInterface;
use App\Util\MoneyAmount;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PayOrderRequestDTO implements RequestDTOInterface, ValidatorDTOInterface
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var int
     */
    private $amount;

    /**
     * @inheritdoc
     */
    public function __construct(Request $request)
    {
        $this->orderId = $request->get('orderId');
        $this->amount = $request->get('amount');
    }

    /**
     * @inheritdoc
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('orderId', new Assert\NotBlank());
        $metadata->addPropertyConstraint('amount', new Assert\NotBlank());

        $metadata->addPropertyConstraint('orderId', new Assert\Type(['type' => 'numeric']));
        $metadata->addPropertyConstraint('amount', new Assert\Type(['type' => 'integer']));
    }

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId(): int
    {
        return (int) $this->orderId;
    }

    /**
     * Get amount order
     *
     * @return MoneyAmount
     */
    public function getAmount(): MoneyAmount
    {
        return MoneyAmount::fromApi($this->amount);
    }
}