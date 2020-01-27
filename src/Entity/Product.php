<?php

declare(strict_types=1);

namespace App\Entity;

use App\Util\MoneyAmount;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products")
 */
class Product
{
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
     * @var int|null
     *
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $price;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return MoneyAmount|null
     */
    public function getPrice(): ?MoneyAmount
    {
        if ($this->price !== null) {
            return MoneyAmount::fromInternal((int) $this->price);
        }

        return null;
    }

    /**
     * @param MoneyAmount $price
     *
     * @return self
     */
    public function setPrice(MoneyAmount $price): self
    {
        $this->price = $price->toInternal();

        return $this;
    }
}