<?php

declare(strict_types=1);

namespace App\Service\Status;

use App\Entity\Order;
use App\Service\ServiceInterface;
use App\Service\Status\Exception\OrderStatusException;
use Doctrine\ORM\EntityManager;

class OrderStatusService implements ServiceInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function canPay(Order $order): bool
    {
        return $this->isStatusNew($order) === true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function validateCanPay(Order $order): bool
    {
        if ($this->isStatusPaid($order)) {
            throw new OrderStatusException('The order has already been paid');
        }

        if ($this->canPay($order) === false) {
            throw new OrderStatusException('The order must be in status: NEW');
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isStatusNew(Order $order): bool
    {
        return $order->getStatus() === Order::STATUS_NEW;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isStatusPaid(Order $order): bool
    {
        return $order->getStatus() === Order::STATUS_PAID;
    }

    /**
     * @param Order $order
     * @param bool $isNeedFlush
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setStatusNew(Order $order, bool $isNeedFlush = false): void
    {
        $this->setStatus($order, Order::STATUS_NEW, $isNeedFlush);
    }

    /**
     * @param Order $order
     * @param bool $isNeedFlush
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setStatusPaid(Order $order, bool $isNeedFlush = false): void
    {
        $this->setStatus($order, Order::STATUS_PAID, $isNeedFlush);
    }

    /**
     * @param Order $order
     * @param int $status
     * @param bool $isNeedFlush
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setStatus(Order $order, int $status, bool $isNeedFlush): void
    {
        $order->setStatus($status);

        $this->entityManager->persist($order);

        if ($isNeedFlush) {
            $this->entityManager->flush();
        }
    }
}