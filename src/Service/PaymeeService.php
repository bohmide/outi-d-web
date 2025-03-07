<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymeeService
{
    private $client;
    private $merchantId;
    private $secretKey;
    private $apiUrl;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->merchantId = $_ENV['PAYMEE_MERCHANT_ID'];
        $this->secretKey = $_ENV['PAYMEE_SECRET_KEY'];
        $this->apiUrl = $_ENV['PAYMEE_API_URL'];
    }

    public function initiatePayment(float $amount, string $currency, string $orderReference)
    {
        $payload = [
            'amount' => $amount,
            'currency' => $currency,
            'order_reference' => $orderReference,
        ];

        $response = $this->client->request('POST', $this->apiUrl, [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ]
        ]);

        return $response->toArray();
    }
}
