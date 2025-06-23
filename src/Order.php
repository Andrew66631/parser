<?php

namespace App;

class Order
{
    /** @var OrderItem[] */
    private array $items = [];

    public function __construct(
        public readonly int $orderId,
        public readonly int $customerId
    ) {
    }

    /**
     * @param OrderItem $item
     * @return void
     */
    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return array_reduce(
            $this->items,
            fn(float $sum, OrderItem $item) => $sum + $item->getTotal(),
            0.0
        );
    }
}