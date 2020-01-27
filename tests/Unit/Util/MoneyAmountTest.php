<?php

declare(strict_types=1);

namespace Tests\Unit\Util;

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

    public function testEqual(): void
    {
        $a = MoneyAmount::fromReadable(10.55);
        $b = MoneyAmount::fromReadable(10.55);
        $c = MoneyAmount::fromReadable(10.550001);

        $this->assertTrue($a->equal($b));
        $this->assertFalse($a->notEqual($b));

        $this->assertFalse($a->equal($c));
        $this->assertTrue($a->notEqual($c));
    }

    public function testAdd(): void
    {
        $a = MoneyAmount::fromReadable(0);
        $b = MoneyAmount::fromReadable(9.555);
        $c = MoneyAmount::fromReadable(0.445);

        $this->assertEquals(9.555, $a->add($b)->toReadable());
        $this->assertEquals(10, $a->add($b)->add($c)->toReadable());
    }
}