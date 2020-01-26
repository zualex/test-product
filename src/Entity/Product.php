<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products")
 */
class Product
{
    public const PRICE_MULTIPLIER = 1000000;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var float|null
     *
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        if ($this->price !== null) {
            return round($this->price / self::PRICE_MULTIPLIER, 6);
        }

        return null;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price * self::PRICE_MULTIPLIER;

        return $this;
    }
}