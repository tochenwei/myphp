<?php
   //连接本地的 Redis 服务
   $redis = new Redis();
   $redis->connect('127.0.0.1', 6379);
   echo "Connection to server sucessfully";
   echo '<br/>';
   
    /**
    getMultiple
	传参
	由key组成的数组
	返回参数
	如果key存在返回value，不存在返回false
	*/
	$redis->set('key1', 'value1'); 
	$redis->set('key2', 'value2');
	$redis->set('key3', 'value3');
	$multi = $redis->getMultiple(array('key1', 'key2', 'key3'));
	print_r($multi);
	echo '<br/>';

?>