<?php
   //连接本地的 Redis 服务
   $redis = new Redis();
   $redis->connect('115.47.40.226', 6379);
   echo "Connection to server sucessfully";
   //判断key是否存在。存在 true 不在 false
   $redis->set("x", "Redis");
   echo '<br/>';
   var_dump($redis->exists("x"));
   echo '<br/>';
    #清空当前数据库
    $redis->flushDB();
    #清空所有数据库
	#$redis->flushAll();
    var_dump($redis->exists("x"));
?>