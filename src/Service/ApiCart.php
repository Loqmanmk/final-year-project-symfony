<?php

namespace App\Service;

use App\Dto\CartItemInput;

class ApiCart implements CartInterface
{
    public function add(CartItemInput $item): void
    {
        throw new \LogicException('ApiCart simule une autre strategie de panier.');
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        throw new \LogicException('ApiCart simule une autre strategie de panier.');
    }

    public function remove(int $productId): void
    {
        throw new \LogicException('ApiCart simule une autre strategie de panier.');
    }

    public function all(): array
    {
        return [];
    }

    public function count(): int
    {
        return 0;
    }

    public function clear(): void
    {
    }
}
