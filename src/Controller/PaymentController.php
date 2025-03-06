<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
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
    /*
        #[Route('/payment/initiate', name: 'payment_initiate', methods: ['POST'])]
        public function initiatePayment(Request $request, EntityManagerInterface $entityManager): JsonResponse
        {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['amount'], $data['first_name'], $data['last_name'], $data['email'], $data['phone'])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            $orderId = uniqid();
            $token = bin2hex(random_bytes(16));

            $payment = new Payment();
            $payment->setOrderId($orderId)
                ->setToken($token)
                ->setAmount((float) $data['amount'])
                ->setStatus('pending');

            $entityManager->persist($payment);
            $entityManager->flush();

            $paymentData = [
                "amount" => (float) $data['amount'],
                "note" => "Payment for event participation",
                "first_name" => $data['first_name'],
                "last_name" => $data['last_name'],
                "email" => $data['email'],
                "phone" => $data['phone'],
                'return_url' => 'https://127.0.0.1:8000/payment/success?token=' . $token,
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

                file_put_contents('paymee_log.txt', json_encode($content, JSON_PRETTY_PRINT), FILE_APPEND);

                if ($statusCode === 200 && isset($content['data']['payment_url'])) {
                    return new JsonResponse(['payment_url' => $content['data']['payment_url']]);
                } else {
                    return new JsonResponse(['error' => 'Failed to initiate payment', 'details' => $content], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'API error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        #[Route('/payment/webhook', name: 'payment_webhook', methods: ['POST'])]
        public function paymentWebhook(Request $request, EntityManagerInterface $em): JsonResponse
        {
            $data = json_decode($request->getContent(), true);

            file_put_contents('webhook_log.txt', print_r($data, true), FILE_APPEND);

            // Validate required fields from Paymee's webhook data
            if (!isset($data['order_id'], $data['payment_status'], $data['check_sum'])) {
                return new JsonResponse(['error' => 'Invalid webhook data'], Response::HTTP_BAD_REQUEST);
            }

            // Calculate the expected checksum using order_id, payment_status, and secret key
            $statusValue = $data['payment_status'] ? '1' : '0'; // Adjust based on Paymee's status format
            $expectedCheckSum = md5($data['order_id'] . $statusValue . $this->paymeeSecretKey);

            if ($data['check_sum'] !== $expectedCheckSum) {
                return new JsonResponse(['error' => 'Checksum verification failed'], Response::HTTP_BAD_REQUEST);
            }

            // Retrieve payment by order_id
            $payment = $em->getRepository(Payment::class)->findOneBy(['orderId' => $data['order_id']]);

            if (!$payment) {
                file_put_contents('webhook_errors.txt', "Payment not found for order_id: " . $data['order_id'], FILE_APPEND);
                return new JsonResponse(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);
            }

            // Update payment status
            $paymentStatus = $data['payment_status'] ? 'success' : 'failed';
            $payment->setStatus($paymentStatus);
            $em->flush();

            return new JsonResponse(['message' => 'Payment status updated successfully']);
        }

        #[Route('/payment/success', name: 'payment_success')]
        public function paymentSuccess(Request $request, CartService $cartService, EntityManagerInterface $em): Response
        {
            $token = $request->query->get('token');

            if (!$token) {
                $this->addFlash('error', 'Token de paiement manquant.');
                return $this->redirectToRoute('cart_show');
            }

            // Récupération du paiement
            $payment = $em->getRepository(Payment::class)->findOneBy(['token' => $token]);

            if (!$payment || $payment->getStatus() !== 'success') {
                $this->addFlash('error', 'Paiement non validé.');
                return $this->redirectToRoute('cart_show');
            }

            // Vider le panier et afficher un message de succès
            $cartService->clearCart();
            $this->addFlash('paymeeSuccess', 'Le paiement a été effectué avec succès.');

            return $this->redirectToRoute('cart_show');
        }

        #[Route('/payment/cancel', name: 'payment_cancel')]
        public function paymentCancel(): Response
        {
            return new Response("<h1>Paiement annulé.</h1>");
        }*/

    #[Route('/payment/initiate', name: 'payment_initiate', methods: ['POST'])]
    public function initiatePayment(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        if (!isset($data['amount'], $data['first_name'], $data['last_name'], $data['email'], $data['phone'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        // Generate unique order ID and token
        $orderId = uniqid();
        $token = bin2hex(random_bytes(16));

        // Log the generated order ID and token
        file_put_contents('initiate_log.txt', "Generated Order ID: $orderId | Token: $token\n", FILE_APPEND);

        // Save payment to database
        $payment = new Payment();
        $payment->setOrderId($orderId)
            ->setToken($token)
            ->setAmount((float) $data['amount'])
            ->setStatus('pending');

        $entityManager->persist($payment);
        $entityManager->flush();

        // Prepare data for Paymee API
        $paymentData = [
            "amount" => (float) $data['amount'],
            "note" => "Payment for event participation",
            "first_name" => $data['first_name'],
            "last_name" => $data['last_name'],
            "email" => $data['email'],
            "phone" => $data['phone'],
            'return_url' => 'https://127.0.0.1:8000/payment/success?token=' . $token,
            'cancel_url' => 'https://127.0.0.1:8000/payment/cancel',
            'webhook_url' => 'https://127.0.0.1:8000/payment/webhook',
            "order_id" => $orderId
        ];

        try {
            // Send request to Paymee API
            $response = $this->httpClient->request('POST', $this->paymeeUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Token " . $this->paymeeSecretKey,
                ],
                'json' => $paymentData
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            // Log Paymee API response
            file_put_contents('paymee_log.txt', json_encode($content, JSON_PRETTY_PRINT), FILE_APPEND);

            if ($statusCode === 200 && isset($content['data']['payment_url'])) {
                return new JsonResponse(['payment_url' => $content['data']['payment_url']]);
            } else {
                return new JsonResponse(['error' => 'Failed to initiate payment', 'details' => $content], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'API error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/payment/webhook', name: 'payment_webhook', methods: ['POST'])]
    public function paymentWebhook(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Log received webhook data
        file_put_contents('webhook_log.txt', print_r($data, true), FILE_APPEND);

        // Validate required fields
        if (!isset($data['order_id'], $data['payment_status'], $data['check_sum'])) {
            return new JsonResponse(['error' => 'Invalid webhook data'], Response::HTTP_BAD_REQUEST);
        }

        // Calculate expected checksum
        $statusValue = $data['payment_status'] ? '1' : '0';
        $expectedCheckSum = md5($data['order_id'] . $statusValue . $this->paymeeSecretKey);

        // Verify checksum
        if ($data['check_sum'] !== $expectedCheckSum) {
            file_put_contents('checksum_errors.txt', "Checksum mismatch. Expected: $expectedCheckSum | Received: " . $data['check_sum'], FILE_APPEND);
            return new JsonResponse(['error' => 'Checksum verification failed'], Response::HTTP_BAD_REQUEST);
        }

        // Retrieve payment by order_id
        $payment = $em->getRepository(Payment::class)->findOneBy(['orderId' => $data['order_id']]);

        if (!$payment) {
            file_put_contents('webhook_errors.txt', "Payment not found for order_id: " . $data['order_id'], FILE_APPEND);
            return new JsonResponse(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        // Update payment status
        $paymentStatus = $data['payment_status'] ? 'success' : 'failed';
        $payment->setStatus($paymentStatus);
        $em->flush();

        return new JsonResponse(['message' => 'Payment status updated successfully']);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(Request $request, CartService $cartService, EntityManagerInterface $em): Response
    {
        $token = $request->query->get('token');

        if (!$token) {
            $this->addFlash('error', 'Token de paiement manquant.');
            return $this->redirectToRoute('cart_show');
        }

        // Retrieve payment by token
        $payment = $em->getRepository(Payment::class)->findOneBy(['token' => $token]);

        if (!$payment || $payment->getStatus() !== 'success') {
            $this->addFlash('error', 'Paiement non validé.');
            return $this->redirectToRoute('cart_show');
        }

        // Clear cart and show success message
        $cartService->clearCart();
        $this->addFlash('paymeeSuccess', 'Le paiement a été effectué avec succès.');

        return $this->redirectToRoute('cart_show');
    }
    #[Route('/payment/cancel', name: 'payment_cancel', methods: ['POST'])]
    public function paymentCancel(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->request->get('token');
    
        if (!$token) {
            $this->addFlash('error', 'Token de paiement manquant.');
            return $this->redirectToRoute('cart_show');
        }
    
        // Traitement du paiement annulé
        $payment = $em->getRepository(Payment::class)->findOneBy(['token' => $token]);
    
        if (!$payment) {
            $this->addFlash('error', 'Paiement non trouvé.');
            return $this->redirectToRoute('cart_show');
        }
    
        $payment->setStatus('canceled');
        $em->flush();
    
        $this->addFlash('paymeeSuccess', 'Le paiement a été annulé.');
    
        // Redirection pour éviter les doubles soumissions
        return $this->redirectToRoute('cart_show');
    }
    
}
