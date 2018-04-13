<?php
require 'RedisLock.class.php';

$config = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'index' => 0,
    'auth' => '',
    'timeout' => 1,
    'reserved' => NULL,
    'retry_interval' => 100,
);

// 创建redislock对象
$oRedisLock = new RedisLock($config);

// 定义锁标识
$key = 'mylock';

// 获取锁
$is_lock = $oRedisLock->lock($key, 10);

if($is_lock){
    echo 'get lock success<br>';
    echo 'do sth..<br>';
    sleep(5);
    echo 'success<br>';
    $oRedisLock->unlock($key);

// 获取锁失败
}else{
    echo 'request too frequently<br>';
}

?>