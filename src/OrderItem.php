<?php

namespace App;

class OrderItem
{
    public function __construct(
        public readonly int $productId,
        public readonly float $price,
        public readonly int $quantity
    ) {
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->price * $this->quantity;
    }
}