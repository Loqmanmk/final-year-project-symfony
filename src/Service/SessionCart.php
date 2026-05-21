<?php

namespace App\Service;

use App\Dto\CartItemInput;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionCart implements CartInterface
{
    private const SESSION_KEY = 'cart';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function add(CartItemInput $item): void
    {
        $cart = $this->all();
        $productId = $item->productId;
        $cart[$productId] = ($cart[$productId] ?? 0) + max(1, $item->quantity);

        $this->save($cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->all();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->all();
        unset($cart[$productId]);

        $this->save($cart);
    }

    public function all(): array
    {
        $cart = $this->session()->get(self::SESSION_KEY, []);

        if (!is_array($cart)) {
            return [];
        }

        $normalized = [];
        foreach ($cart as $productId => $quantity) {
            $normalized[(int) $productId] = (int) $quantity;
        }

        return $normalized;
    }

    public function count(): int
    {
        return array_sum($this->all());
    }

    public function clear(): void
    {
        $this->session()->remove(self::SESSION_KEY);
    }

    /**
     * @param array<int, int> $cart
     */
    private function save(array $cart): void
    {
        ksort($cart);
        $this->session()->set(self::SESSION_KEY, $cart);
    }

    private function session(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
