<?php

function generateOrdersFile($filename, $count = 200) {
    $products = [];
    for ($i = 1; $i <= 50; $i++) {
        $products[$i] = [
            'id' => $i,
            'base_price' => rand(10, 300) + (rand(0, 99) / 100)
        ];
    }

    $orders = [];
    for ($i = 1; $i <= $count; $i++) {
        $order = [
            'order_id' => $i,
            'customer_id' => rand(1000, 9999),
            'items' => []
        ];

        $itemsCount = rand(1, 5);
        $usedProducts = [];

        for ($j = 0; $j < $itemsCount; $j++) {
            $productId = rand(1, 50);
            while (in_array($productId, $usedProducts)) {
                $productId = rand(1, 50);
            }
            $usedProducts[] = $productId;

            $order['items'][] = [
                'product_id' => $productId,
                'price' => $products[$productId]['base_price'],
                'quantity' => rand(1, 5)
            ];
        }

        $orders[] = $order;
    }

    file_put_contents($filename, json_encode($orders, JSON_PRETTY_PRINT));
}

generateOrdersFile('orders.json');