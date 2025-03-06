<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_show')]
    public function showCart(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getCart(),
            'total' => $cartService->getTotalPrice(),
            'stripe_key' => $_ENV["STRIPE_KEY"]
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add')]
    public function addEvent(Evenements $evenement, CartService $cartService): Response
    {
        $cartService->addToCart($evenement);
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/remove/{key}', name: 'cart_remove')]
    public function removeItem(string $key, CartService $cartService): Response
    {
        $cartService->removeItem($key);
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/decrease/{key}', name: 'cart_decrease')]
    public function decreaseQuantity(string $key, CartService $cartService): Response
    {
        $cartService->decreaseQuantity($key);
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/clear', name: 'cart_clear')]
    public function clearCart(CartService $cartService): Response
    {
        $cartService->clearCart();
        return $this->redirectToRoute('cart_show');
    }
}
