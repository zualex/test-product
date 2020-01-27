<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    /**
     * @param int $orderId
     * @return Order|null
     */
    public function findById(int $orderId): ?Order
    {
        return $this->findOneBy(['id' => $orderId]);
    }
}