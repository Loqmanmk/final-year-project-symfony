<?php

namespace App\Dto;

final class CartItemInput
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity = 1,
    ) {
    }
}
