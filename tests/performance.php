<?php

include __DIR__ . "/bootstrap.php";

if (file_exists(__DIR__ . '/config-local.php')) {
    $param = include(__DIR__ . '/config-local.php');
} else {
    $param = include(__DIR__ . '/config.php');
}

$phpRedisConfig = $param['class'] = 'dcb9\redis\Connection';
$yiiRedisConfig = $param['class'] = 'yii\redis\Connection';

$app = new \yii\console\Application([
    'id' => 'test-performance-app',
    'basePath' => __DIR__,
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'components' => [
        'phpRedis' => $phpRedisConfig,
        'yiiRedis' => $yiiRedisConfig,
    ],
]);

$count = 10000;
echo "phpredis run SET $count times in";
$start = microtime(true);
/* @var $phpRedis \dcb9\redis\Connection */
$phpRedis = Yii::$app->phpRedis;
$phpRedis->open();
$phpRedis->flushdb();
for ($i = 0; $i < $count; $i++) {
    $phpRedis->set('php_redis_prefix' . $i, $i);
}
echo " " . ((microtime(true) - $start) * 1000) . " micro seconds.\n";

echo "yii redis run SET $count times in";
$start = microtime(true);
/* @var $yiiRedis \yii\redis\Connection */
$yiiRedis = Yii::$app->yiiRedis;
$yiiRedis->flushdb();
for ($i = 0; $i < $count; $i++) {
    $yiiRedis->set('yii_redis_prefix' . $i, $i);
}
echo " " . ((microtime(true) - $start) * 1000) . " micro seconds.\n";

echo "phpredis run GET $count times in";
$start = microtime(true);
for ($i = 0; $i < $count; $i++) {
    $phpRedis->get('php_redis_prefix' . $i);
}
echo " " . ((microtime(true) - $start) * 1000) . " micro seconds.\n";

echo "yii redis run GET $count times in";
$start = microtime(true);
for ($i = 0; $i < $count; $i++) {
    $yiiRedis->get('yii_redis_prefix' . $i);
}
echo " " . ((microtime(true) - $start) * 1000) . " micro seconds.\n";
