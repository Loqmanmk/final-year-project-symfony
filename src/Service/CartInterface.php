<?php

namespace App\Service;

use App\Dto\CartItemInput;

interface CartInterface
{
    public function add(CartItemInput $item): void;

    public function updateQuantity(int $productId, int $quantity): void;

    public function remove(int $productId): void;

    /**
     * @return array<int, int>
     */
    public function all(): array;

    public function count(): int;

    public function clear(): void;
}
