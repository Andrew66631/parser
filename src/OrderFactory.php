<?php

namespace App;

use Predis\Client as RedisClient;

class OrderFactory extends AbstractOrderFactory
{
    public function createOrderBuilder(): OrderBuilderInterface
    {
        return new OrderBuilder();
    }

    public function createOrderProcessor(?RedisClient $redis = null): OrderProcessorInterface
    {
        return new OrderProcessor($redis);
    }
}
