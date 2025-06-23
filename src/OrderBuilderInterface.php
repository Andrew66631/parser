<?php

namespace App;

interface OrderBuilderInterface
{
    public function setOrderId(int $orderId): self;

    public function setCustomerId(int $customerId): self;
    public function addItem(int $productId, float $price, int $quantity): self;
    public function build(): Order;
}