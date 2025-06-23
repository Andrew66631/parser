<?php

namespace App;
use Predis\Client as RedisClient;
abstract class AbstractOrderFactory
{
    abstract public function createOrderBuilder(): OrderBuilderInterface;
    abstract public function createOrderProcessor(?RedisClient $redis = null): OrderProcessorInterface;
}