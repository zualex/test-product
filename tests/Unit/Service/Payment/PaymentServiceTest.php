<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Status;

use App\Service\Payment\Exception\PaymentException;
use App\Service\Payment\PaymentService;
use App\Util\MoneyAmount;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function testPurchase(): void
    {
        $amount = MoneyAmount::fromReadable(10);
        $paymentService = new PaymentService($this->getClient(200));

        $result = $paymentService->purchase($amount);

        $this->assertTrue($result);
    }

    public function testPurchaseFailed(): void
    {
        $this->expectException(PaymentException::class);

        $amount = MoneyAmount::fromReadable(10);
        $paymentService = new PaymentService($this->getClient(402));

        $paymentService->purchase($amount);
    }

    /**
     * @param $httpStatusCode
     * @return Client
     */
    private function getClient($httpStatusCode): Client
    {
        $mock = new MockHandler([
            new Response($httpStatusCode)
        ]);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }
}