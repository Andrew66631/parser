<?php

namespace App;
use Predis\Client as RedisClient;
use Predis\Connection\ConnectionException;
final class OrderProcessor implements OrderProcessorInterface
{
    public function __construct(
        private ?RedisClient $redis = null
    ) {
    }

    /**
     * @param array $orders
     * @return array
     */
    public function process(array $orders): array
    {
        usort($orders, fn(Order $a, Order $b) => $b->getTotal() <=> $a->getTotal());

        $this->cacheLastOrder($orders);

        return $orders;
    }

    /**
     * @param array $orders
     * @return array
     */
    public function groupByCustomer(array $orders): array
    {
        /** @var $orders Order[] $grouped */
        $grouped = [];
        foreach ($orders as $order) {
            $customerId = $order->customerId;
            if (!isset($grouped[$customerId])) {
                $grouped[$customerId] = [
                    'orders' => [],
                    'total_orders' => 0,
                    'total_amount' => 0.0
                ];
            }

            $grouped[$customerId]['orders'][] = [
                'order_id' => $order->orderId,
                'total' => $order->getTotal(),
                'items_count' => count($order->getItems())
            ];
            $grouped[$customerId]['total_orders']++;
            $grouped[$customerId]['total_amount'] += $order->getTotal();
        }
        return $grouped;
    }

    /**
     * @param array $orders
     * @return void
     */
    private function cacheLastOrder(array $orders): void
    {
        if ($this->redis && !empty($orders)) {
            try {
                $lastOrder = end($orders);
                $this->redis->set('last_processed_order', (string) $lastOrder->orderId);
                $this->redis->expire('last_processed_order', 3600); // TTL 1 час
            } catch (ConnectionException $e) {
                error_log("Ошибка кеширования: " . $e->getMessage());
            }
        }
    }

}