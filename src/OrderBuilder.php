<?php

namespace App;

final class OrderBuilder implements OrderBuilderInterface
{
    private int $orderId;
    private int $customerId;
    /** @var OrderItem[] */
    private array $items = [];

    /**
     * @param int $orderId
     * @return $this
     */

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @param int $customerId
     * @return $this
     */

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @return $this
     */

    public function addItem(int $productId, float $price, int $quantity): self
    {
        $this->items[] = new OrderItem($productId, $price, $quantity);
        return $this;
    }

    /**
     * @return Order
     */
    public function build(): Order
    {
        $order = new Order($this->orderId, $this->customerId);
        foreach ($this->items as $item) {
            $order->addItem($item);
        }
        return $order;
    }
}