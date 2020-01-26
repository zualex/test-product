<?php

declare(strict_types=1);

namespace App\Service\Product;

use App\Entity\Product;
use App\Service\ServiceInterface;
use Doctrine\ORM\EntityManager;
use Faker\Factory;

class ProductService implements ServiceInterface
{
    public const DEFAULT_COUNT = 20;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->faker = Factory::create();
    }

    /**
     * Batch create random product
     *
     * @param int|null $count
     *
     * @return Product[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function batchCreateRandom(int $count = null): array
    {
        $result = [];
        $countPrepare = $count ?? $this->getDefaultCount();

        for ($i = 0; $i < $countPrepare; $i++) {
            $product = $this->createRandom(false);
            $result[] = $product;
        }

        $this->entityManager->flush();

        return $result;
    }

    /**
     * Create random product
     *
     * @param bool $isNeedFlush
     *
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createRandom(bool $isNeedFlush = true): Product
    {
        $name = $this->faker->name;
        $price = $this->faker->numberBetween($min = 0, $max = 1000000);

        return $this->create($name, $price, $isNeedFlush);
    }

    /**
     * Create product
     *
     * @param string $name
     * @param int $price
     * @param bool $isNeedFlush
     *
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(string $name, int $price, bool $isNeedFlush = true): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        $this->entityManager->persist($product);

        if ($isNeedFlush) {
            $this->entityManager->flush();
        }

        return $product;
    }

    private function getDefaultCount(): int
    {
        return self::DEFAULT_COUNT;
    }
}