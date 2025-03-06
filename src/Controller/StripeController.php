<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\PaymentIntent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    private LoggerInterface $logger;

    // Injecter le logger via le constructeur
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/stripe', name: 'app_stripe')]
    public function index(CartService $cartService): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'total' => $cartService->getTotalPrice(),
        ]);
    }

    #[Route('/payment/checkout', name: 'payment_checkout')]
    public function checkout(CartService $cartService): Response
    {
        if ($cartService->getTotalPrice() <= 0) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->render('stripe/checkout.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'total' => $cartService->getTotalPrice(),
        ]);
    }

    #[Route('/payment/create-charge', name: 'payment_create_charge', methods: ['POST'])]
    public function createCharge(
        Request $request,
        CartService $cartService,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $response = ['success' => false, 'redirect' => $urlGenerator->generate('cart_show')];

        try {
            // 1. Validation des données
            $data = json_decode($request->getContent(), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Requête JSON invalide');
            }

            if (!isset($data['paymentMethodId']) || empty(trim($data['paymentMethodId']))) {
                throw new \InvalidArgumentException('PaymentMethod ID requis');
            }

            // 2. Validation du montant
            $amount = $cartService->getTotalPrice();
            if ($amount <= 0 || $amount > 100000) { // Limite de 1000€
                throw new \DomainException('Montant invalide : ' . $amount);
            }

            // 3. Création du PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($amount * 100),
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'user_id' => $this->getUser()?->getId() ?? 'guest',
                    'cart_hash' => md5(serialize($cartService->getCart()))
                ],
                'description' => 'Paiement pour ' . ($this->getUser()?->getEmail() ?? 'guest')
            ]);

            // 4. Confirmation du paiement
            $confirmedIntent = $paymentIntent->confirm([
                'payment_method' => $data['paymentMethodId'],
                'return_url' => $urlGenerator->generate('payment_success', ['id' => 'temp_id'], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

            // 5. Gestion des statuts
            if ($confirmedIntent->status === 'requires_action') {
                return new JsonResponse([
                    'success' => false,
                    'requiresAction' => true,
                    'clientSecret' => $confirmedIntent->client_secret
                ]);
            }

            if ($confirmedIntent->status !== 'succeeded') {
                throw new \RuntimeException('Statut de paiement inattendu : ' . $confirmedIntent->status);
            }

            // 6. Enregistrement en base
            $payment = new Payment();
            $payment
                ->setOrderId('PAY-' . uniqid())
                ->setAmount($amount)
                ->setStatus($confirmedIntent->status)
                ->setStripePaymentId($confirmedIntent->id)
                ->setCreatedAt(new \DateTimeImmutable());

            $em->persist($payment);
            $em->flush();

            // 7. Vidage du panier
            $cartService->clearCart();

            // 8. Préparation réponse
            $response = [
                'success' => true,
                'redirect' => $urlGenerator->generate('payment_success', [
                    'id' => $payment->getId(),
                    'checksum' => hash_hmac('sha256', $payment->getId(), $_ENV['APP_SECRET'])
                ])
            ];

            $logger->info('Paiement réussi', [
                'payment_id' => $payment->getId(),
                'amount' => $amount,
                'stripe_id' => $confirmedIntent->id
            ]);

        } catch (\Throwable $e) {
            // 9. Gestion d'erreur
            $logger->error('Erreur de paiement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->getContent()
            ]);

            $response['error'] = $e->getMessage();
            
            if ($em->isOpen()) {
                $em->clear();
            }
        }

        return new JsonResponse($response);
    }

    #[Route('/stripe/status/{orderId}', name: 'app_stripe_status')]
    public function paymentStatus(string $orderId, EntityManagerInterface $em): Response
    {
        $payment = $em->getRepository(Payment::class)->findOneBy(['orderId' => $orderId]);

        if (!$payment) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        return $this->render('stripe/status.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/stripe/history', name: 'app_stripe_history')]
    public function paymentHistory(EntityManagerInterface $em): Response
    {
        $payments = $em->getRepository(Payment::class)->findAll();

        return $this->render('stripe/history.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/payment/success/{id}', name: 'payment_success')]
    public function paymentSuccess(Payment $payment): Response
    {
        return $this->render('stripe/success.html.twig', [
            'payment' => $payment
        ]);
    }
}
