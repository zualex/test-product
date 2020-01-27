<?php

declare(strict_types=1);

namespace App\Util;

class MoneyAmount
{
    public const INTERNAL_MULTIPLIER = 1000000;
    public const API_MULTIPLIER = 100;

    /**
     * @var string
     */
    private $amount;

    /**
     * @param int|string $amount internal
     */
    private function __construct($amount)
    {
        $this->amount = (string) $amount;
    }

    /**
     * @param int|float|string $amount example 10.55
     * @return MoneyAmount
     */
    public static function fromReadable($amount): MoneyAmount
    {
        $result = bcmul((string) $amount, (string) self::INTERNAL_MULTIPLIER);


        return new self($result);
    }

    /**
     * @param int|string $amount example 1055 (10.55 in readable format)
     * @return MoneyAmount
     */
    public static function fromApi($amount): MoneyAmount
    {
        $divisor = (string) (self::INTERNAL_MULTIPLIER / self::API_MULTIPLIER);
        $result = bcmul((string) $amount, $divisor);

        return new self($result);
    }

    /**
     * @param int|string $amount example 10550000 (10.55 in readable format)
     * @return MoneyAmount
     */
    public static function fromInternal($amount): MoneyAmount
    {
        return new self($amount);
    }

    /**
     * @return float example 10.55
     */
    public function toReadable(): float
    {
        $result = bcdiv($this->amount, (string) self::INTERNAL_MULTIPLIER, 6);

        return (float) $result;
    }

    /**
     * @return int 1055 (10.55 in readable format)
     */
    public function toApi(): int
    {
        $divisor = (string) (self::INTERNAL_MULTIPLIER / self::API_MULTIPLIER);
        $result = bcdiv($this->amount, $divisor, 0);

        return (int) $result;
    }

    /**
     * @return string example 10550000 (10.55 in readable format)
     */
    public function toInternal(): string
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

    /**
     * @param MoneyAmount $amount
     * @return bool
     */
    public function notEqual(MoneyAmount $amount): bool
    {
        return $this->equal($amount) === false;
    }

    /**
     * @param MoneyAmount $amount
     * @return MoneyAmount
     */
    public function add(MoneyAmount $amount): MoneyAmount
    {
        $result = bcadd($this->amount, $amount->toInternal());

        return new self($result);
    }
}