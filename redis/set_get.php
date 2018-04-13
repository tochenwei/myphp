<?php
   //连接本地的 Redis 服务
   $redis = new Redis();
   $result = $redis->connect('127.0.0.1', 6379);
   var_dump($result);
   echo "<br/>";
   if($result){
	echo "Connection to server sucessfully";
   }else{
	   echo "Connection to server failed";
   }
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
   $nx_result = $redis->setnx('key_expire_not_exist', 'value_60');
   var_dump($nx_result);
   if($nx_result){
       $redis->expire('key_expire_not_exist', 60);
	   $redis->exec();
	   echo "exec";
   }
?>