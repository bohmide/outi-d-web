<?php

namespace App\Service;

use App\Entity\Evenements;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addToCart(Evenements $evenement): void
    {
        $cart = $this->session->get('cart', []);

        $cartKey = 'event_' . $evenement->getId();
        if (!isset($cart[$cartKey])) {
            $cart[$cartKey] = [
                'id' => $evenement->getId(),
                'name' => $evenement->getNomEvent(),
                'price' => $evenement->getPrix() ?? 0,
                'quantity' => 1
            ];
        } else {
            $cart[$cartKey]['quantity']++;
        }

        $this->session->set('cart', $cart);
    }

    public function removeItem(string $cartKey): void
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$cartKey]);
        $this->session->set('cart', $cart);
    }

    public function decreaseQuantity(string $cartKey): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$cartKey])) {
            if ($cart[$cartKey]['quantity'] > 1) {
                $cart[$cartKey]['quantity']--;
            } else {
                unset($cart[$cartKey]);
            }
        }
        $this->session->set('cart', $cart);
    }

    public function clearCart(): void
    {
        $this->session->remove('cart');
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function getTotalPrice(): float
    {
        return array_reduce($this->getCart(), fn($total, $item) => $total + ($item['price'] * $item['quantity']), 0);
    }
}
