<?php

namespace App;

interface OrderProcessorInterface
{
    /**
     * @param array $orders
     * @return array
     */
    public function process(array $orders): array;

    /**
     * @param array $orders
     * @return array
     */
    public function groupByCustomer(array $orders): array;
}