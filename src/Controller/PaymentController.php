<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    private $httpClient;
    private $paymeeUrl;
    private $paymeeSecretKey;
    private $merchantId;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->paymeeUrl = $_ENV['PAYMEE_API_URL'];
        $this->paymeeSecretKey = $_ENV['PAYMEE_SECRET_KEY'];
        $this->merchantId = $_ENV['PAYMEE_MERCHANT_ID'];
    }

    #[Route('/payment/initiate', name: 'payment_initiate', methods: ['POST'])]
    public function initiatePayment(Request $request): JsonResponse
    {

        
        $data = json_decode($request->getContent(), true);

        if (!isset($data['amount'], $data['first_name'], $data['last_name'], $data['email'], $data['phone'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $orderId = uniqid(); // Génère un ID unique pour la transaction

        $paymentData = [
            "amount" => (float) $data['amount'],
            "note" => "Payment for event participation",
            "first_name" => $data['first_name'],
            "last_name" => $data['last_name'],
            "email" => $data['email'],
            "phone" => $data['phone'],
            'return_url' => 'https://127.0.0.1:8000/payment/success',
            'cancel_url' => 'https://127.0.0.1:8000/payment/cancel',
            'webhook_url' => 'https://127.0.0.1:8000/payment/webhook',
            "order_id" => $orderId
        ];

        try {
            $response = $this->httpClient->request('POST', $this->paymeeUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Token " . $this->paymeeSecretKey,
                ],
                'json' => $paymentData
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            if ($statusCode === 200 && isset($content['data']['payment_url'])) {
                return new JsonResponse(['payment_url' => $content['data']['payment_url']]);
            } else {
                return new JsonResponse(['error' => 'Failed to initiate payment', 'details' => $content], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'API error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(CartService $cartService): Response
    {
        $cartService->clearCart();
        $this->addFlash('paymeeSuccess','payment t3ada ya zi***');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return new Response("<h1>Paiement annulé.</h1>");
    }

    #[Route('/payment/webhook', name: 'payment_webhook', methods: ['POST'])]
    public function paymentWebhook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['payment_status'], $data['token'], $data['check_sum'])) {
            return new JsonResponse(['error' => 'Invalid webhook data'], Response::HTTP_BAD_REQUEST);
        }

        $expectedCheckSum = md5($data['token'] . ($data['payment_status'] ? "1" : "0") . $this->paymeeSecretKey);

        if ($expectedCheckSum !== $data['check_sum']) {
            return new JsonResponse(['error' => 'Checksum verification failed'], Response::HTTP_BAD_REQUEST);
        }

        if ($data['payment_status']) {
            return new JsonResponse(['message' => 'Payment successful']);
        } else {
            return new JsonResponse(['message' => 'Payment failed']);
        }
    }
}
