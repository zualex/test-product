<?php

declare(strict_types=1);

namespace App\Entity;

use App\Util\MoneyAmount;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 * @ORM\Table(name="order_items")
 */
class OrderItem
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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="orderItems")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    private $order;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $product;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true, "default" : 0})
     */
    private $count;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     *
     * @return self
     */
    public function setOrder(Order $order = null): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     *
     * @return self
     */
    public function setProduct(Product $product = null): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
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
     * @return MoneyAmount
     */
    public function getPrice(): MoneyAmount
    {
        return MoneyAmount::fromInternal($this->price);
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

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return self
     */
    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}