<?php 
/**
描述：删除指定的键
参数：一个键，或不确定数目的参数，每一个关键的数组：key1 key2 key3 … keyN
返回值：删除的项数
*/ 
$redis = new redis();  
$redis->connect('115.47.40.226', 6379);  
$redis->set('test',"1111111111111");  
echo $redis->get('test');   //结果：1111111111111  
$redis->delete('test');  
var_dump($redis->get('test'));  //结果：bool(false)  
?>  