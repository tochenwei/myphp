<?php
   //连接本地的 Redis 服务
   $redis = new Redis();
   $redis->connect('115.47.40.226', 6379);
   echo "Connection to server sucessfully";
   //存储数据到列表中
   $redis->set("x", "Redis");
   $redis->set("y", "Mongodb");
   $redis->set("z", "Mysql");
   echo '<br/>';
   // 获取存储的数据并输出
   echo $redis->get("x");
   echo '<br/>';
   echo $redis->get("y");
   echo '<br/>';
   echo $redis->get("z");
   echo '<br/>';
   
   #setex 带生存时间的写入值,60秒有效
   if(empty($redis->get("key_expire"))){
	   $redis->setex('key_expire', 60, 'value_60');
	   echo $redis->get("key_expire");
	   echo '<br/>';
   }
   var_dump($redis->setnx('key_expire', 'value_60'));
?>