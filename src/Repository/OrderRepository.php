<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class OrderRepository extends EntityRepository
{
    /**
     * @param int $orderId
     * @return Order|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function findByIdWithLockMode(int $orderId): ?Order
    {
        try {
            $result = $this->createQueryBuilder('a')
                ->select('a')
                ->andWhere('a.id IN (:id)')
                ->setParameter('id', $orderId)
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)
                ->getSingleResult();
        } catch (NoResultException $exception) {
            $result = null;
        }

        return $result;
    }
}