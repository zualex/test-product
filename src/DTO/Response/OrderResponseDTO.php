<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\Order;

class OrderResponseDTO extends ResponseDTO
{
    public const OBJECT = 'order';

    public function __construct(array $data)
    {
        parent::__construct(self::OBJECT, $data);
    }

    public static function createFromOrderEntity(Order $order): self
    {
        $data = [
            'id' => $order->getId()
        ];

        return new self($data);
    }
}