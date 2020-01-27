<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 */
class Order
{
    public const STATUS_NEW = 0;
    public const STATUS_PROCESSING = 1;
    public const STATUS_PAID = 2;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(columnDefinition="TINYINT UNSIGNED DEFAULT 0 NOT NULL")
     */
    private $status;

    /**
     * @var OrderItem[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="order")
     */
    private $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return (int) $this->status;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return OrderItem[]|ArrayCollection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem $orderItem
     *
     * @return self
     */
    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }

        return $this;
    }

    /**language
     * @param OrderItem $orderItem
     *
     * @return self
     */
    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->contains($orderItem)) {
            $this->orderItems->removeElement($orderItem);
            $orderItem->setOrder(null);
        }

        return $this;
    }
}