<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Product;

use App\Util\MoneyAmount;
use PHPUnit\Framework\TestCase;

class MoneyAmountTest extends TestCase
{
    /**
     * @dataProvider providerFromReadable
     *
     * @param $amount
     * @param $expectedReadable
     * @param $expectedApi
     * @param $expectedInternal
     */
    public function testMoneyAmountFromReadable($amount, $expectedReadable, $expectedApi, $expectedInternal): void
    {
        $moneyAmount = MoneyAmount::fromReadable($amount);

        $this->assertEquals($expectedReadable, $moneyAmount->toReadable());
        $this->assertEquals($expectedApi, $moneyAmount->toApi());
        $this->assertEquals($expectedInternal, $moneyAmount->toInternal());
    }

    /**
     * @return array
     */
    public function providerFromReadable(): array
    {
        return [
            [10.55, 10.55, 1055, 10550000],
            [-0.0001, -0.0001, 0, -100],
            [0.000001, 0.000001, 0, 1],
            [0.0000001, 0, 0, 0],
            [0, 0, 0, 0],
        ];
    }

    /**
     * @dataProvider providerFromApi
     *
     * @param $amount
     * @param $expectedReadable
     * @param $expectedApi
     * @param $expectedInternal
     */
    public function testMoneyAmountFromApi($amount, $expectedReadable, $expectedApi, $expectedInternal): void
    {
        $moneyAmount = MoneyAmount::fromApi($amount);

        $this->assertEquals($expectedReadable, $moneyAmount->toReadable());
        $this->assertEquals($expectedApi, $moneyAmount->toApi());
        $this->assertEquals($expectedInternal, $moneyAmount->toInternal());
    }

    /**
     * @return array
     */
    public function providerFromApi(): array
    {
        return [
            [1055, 10.55, 1055, 10550000],
            [-1, -0.01, -1, -10000],
            [0, 0, 0, 0],
        ];
    }

    /**
     * @dataProvider providerFromInternal
     *
     * @param $amount
     * @param $expectedReadable
     * @param $expectedApi
     * @param $expectedInternal
     */
    public function testMoneyAmountFromInternal($amount, $expectedReadable, $expectedApi, $expectedInternal): void
    {
        $moneyAmount = MoneyAmount::fromInternal($amount);

        $this->assertEquals($expectedReadable, $moneyAmount->toReadable());
        $this->assertEquals($expectedApi, $moneyAmount->toApi());
        $this->assertEquals($expectedInternal, $moneyAmount->toInternal());
    }

    /**
     * @return array
     */
    public function providerFromInternal(): array
    {
        return [
            [10550000, 10.55, 1055, 10550000],
            [-1, -0.000001, 0, -1],
            [0, 0, 0, 0],
        ];
    }
}