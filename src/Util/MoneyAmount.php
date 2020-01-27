<?php

declare(strict_types=1);

namespace App\Util;

class MoneyAmount
{
    public const INTERNAL_MULTIPLIER = 1000000;
    public const API_MULTIPLIER = 100;

    /**
     * @var int
     */
    private $amount;

    /**
     * @param int $amount internal
     */
    private function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param float $amount example 10.55
     * @return MoneyAmount
     */
    public static function fromReadable(float $amount): MoneyAmount
    {
        $result = (int) ($amount * self::INTERNAL_MULTIPLIER);

        return new self($result);
    }

    /**
     * @param int $amount example 1055 (10.55 in readable format)
     * @return MoneyAmount
     */
    public static function fromApi(int $amount): MoneyAmount
    {
        $result = (int) ($amount / self::API_MULTIPLIER * self::INTERNAL_MULTIPLIER);

        return new self($result);
    }

    /**
     * @param int $amount example 10550000 (10.55 in readable format)
     * @return MoneyAmount
     */
    public static function fromInternal(int $amount): MoneyAmount
    {
        return new self($amount);
    }

    /**
     * @return float example 10.55
     */
    public function toReadable(): float
    {
        return round($this->amount / self::INTERNAL_MULTIPLIER, 6);
    }

    /**
     * @return int 1055 (10.55 in readable format)
     */
    public function toApi(): int
    {
        return (int) round($this->amount / (self::INTERNAL_MULTIPLIER / self::API_MULTIPLIER), 0);
    }

    /**
     * @return int example 10550000 (10.55 in readable format)
     */
    public function toInternal(): int
    {
        return $this->amount;
    }

    /**
     * @param MoneyAmount $amount
     * @return bool
     */
    public function equal(MoneyAmount $amount): bool
    {
        return $this->amount === $amount->toInternal();
    }
}