<?php

namespace App;

use Predis\Client as RedisClient;
use Predis\Connection\ConnectionException;

final class OrderApplication
{
    public function __construct(
        private OrderFileLoader $loader,
        private AbstractOrderFactory $factory,
        private ?RedisClient $redis = null
    ) {
    }

    public function run(): void
    {
        try {
            $rawOrders = $this->loader->load();
            $orders = $this->buildOrders($rawOrders);

            $processor = $this->factory->createOrderProcessor($this->redis);
            $processedOrders = $processor->process($orders);
            $groupedOrders = $processor->groupByCustomer($processedOrders);

            $this->displayResults($processedOrders, $groupedOrders);

        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * @param array $rawOrders
     * @return array
     */
    private function buildOrders(array $rawOrders): array
    {
        $builder = $this->factory->createOrderBuilder();
        $orders = [];

        foreach ($rawOrders as $rawOrder) {
            $builder
                ->setOrderId($rawOrder['order_id'])
                ->setCustomerId($rawOrder['customer_id']);

            foreach ($rawOrder['items'] as $item) {
                $builder->addItem(
                    $item['product_id'],
                    $item['price'],
                    $item['quantity']
                );
            }

            $orders[] = $builder->build();
        }

        return $orders;
    }

    /**
     * @param array $orders
     * @param array $groupedOrders
     * @return void
     */
    private function displayResults(array $orders, array $groupedOrders): void
    {
        $this->displaySortedOrders($orders);
        $this->displayCustomerOrders($groupedOrders);
        $this->displayRedisCacheInfo();
    }

    /**
     * @param array $orders
     * @return void
     */
    private function displaySortedOrders(array $orders): void
    {
        echo "Заказы отсортированные по сумме" . PHP_EOL;
        foreach ($orders as $order) {
            printf(
                "Заказ - #%d (Покупатель ID - : %d) - Сумма: %.2f" . PHP_EOL,
                $order->orderId,
                $order->customerId,
                $order->getTotal()
            );
        }
    }

    /**
     * @param array $groupedOrders
     * @return void
     */
    private function displayCustomerOrders(array $groupedOrders): void
    {
        echo PHP_EOL . "Заказы покупателя" . PHP_EOL;
        foreach ($groupedOrders as $customerId => $customerData) {
            printf(
                "Покупатель %d: Кол-во заказов - %d, Сумма всех заказов: %.2f" . PHP_EOL,
                $customerId,
                $customerData['total_orders'],
                $customerData['total_amount']
            );

            foreach ($customerData['orders'] as $order) {
                printf(
                    "  - Номер заказа #%d: %d кол-во позиций, Сумма заказа: %.2f" . PHP_EOL,
                    $order['order_id'],
                    $order['items_count'],
                    $order['total']
                );
            }
            echo PHP_EOL;
        }
    }

    /**
     * @return void
     */
    private function displayRedisCacheInfo(): void
    {
        if ($this->redis) {
            try {
                $lastOrderId = $this->redis->get('last_processed_order');
                echo PHP_EOL . "Последний обработанный заказ" . PHP_EOL;
                echo "Номер заказа: " . ($lastOrderId ?: 'none') . PHP_EOL;
            } catch (ConnectionException $e) {
                echo "Нет подключения к Redis: " . $e->getMessage() . PHP_EOL;
            }
        }
    }
}
