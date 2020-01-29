<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllCount(): int
    {
        return (int)$this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function getIdsByList(array $productIds): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('a.id')
            ->andWhere('a.id IN (:ids)')
            ->setParameter('ids', $productIds)
            ->getQuery()
            ->getResult();

        return array_map(function($row) {
            return $row['id'];
        }, $result);
    }

    /**
     * @param array $productIds
     * @return self[]
     */
    public function findByIds(array $productIds): array
    {
        return $this->findBy(['id' => $productIds]);
    }
}