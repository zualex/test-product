<?php

declare(strict_types=1);

namespace App\Service\Product;

use App\Entity\Product;
use App\Service\Product\Exception\NotExistProductIdException;
use App\Service\ServiceInterface;
use App\Util\MoneyAmount;
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
        $price = MoneyAmount::fromReadable($this->faker->numberBetween($min = 0, $max = 10000));

        return $this->create($name, $price, $isNeedFlush);
    }

    /**
     * Create product
     *
     * @param string $name
     * @param MoneyAmount $price
     * @param bool $isNeedFlush
     *
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(string $name, MoneyAmount $price, bool $isNeedFlush = true): Product
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

    /**
     * @param array $productIds
     * @return bool
     */
    public function validateProductIds(array $productIds): bool
    {
        $ids = $this->entityManager->getRepository(Product::class)
            ->getIdsByList($productIds);

        if (count($productIds) !== count($ids)) {
            $missingValues = implode(', ', array_diff($productIds, $ids));

            throw new NotExistProductIdException('Not exist product ids: ' . $missingValues);
        }

        return true;
    }

    /**
     * @return int
     */
    public function getDefaultCount(): int
    {
        return self::DEFAULT_COUNT;
    }
}