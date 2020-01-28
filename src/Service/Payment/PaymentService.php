<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Service\Payment\Exception\PaymentException;
use App\Service\ServiceInterface;
use App\Util\MoneyAmount;
use GuzzleHttp\ClientInterface;

class PaymentService implements ServiceInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->url = 'https://ya.ru';
    }

    /**
     * @param MoneyAmount $amount
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function purchase(MoneyAmount $amount): bool
    {
        try {
            $response = $this->client->request('GET', $this->url);

            if ($response->getStatusCode() !== 200) {
                $this->throwFailedToPayException();
            }
        } catch (\Throwable $exception) {
            $this->throwFailedToPayException();
        }

        return true;
    }

    private function throwFailedToPayException(): void
    {
        throw new PaymentException('Failed to pay. Try again later.');
    }
}