<?php
require __DIR__ . '/../vendor/autoload.php';
use Predis\Client as RedisClient;
use App\OrderFactory;
use App\OrderApplication;
use App\OrderFileLoader;


$redis = new RedisClient([
    'scheme' => 'tcp',
    'host'   => 'redis',
    'port'   => 6379,
    'timeout' => 2.0,
]);

$factory = new OrderFactory();
$loader = new OrderFileLoader('orders.json');
$app = new OrderApplication($loader, $factory, $redis);
echo '<pre>';
$app->run();
echo '</pre>';