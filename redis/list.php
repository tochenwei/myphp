<?php
   //连接本地的 Redis 服务
   $redis = new Redis();
   $redis->connect('115.47.40.226', 6379);
   echo "Connection to server sucessfully";
   echo '<br/>';
   #####################
   /**
       在Redis中，List类型是按照插入顺序排序的字符串链表。和数据结构中的普通链表一样，
   我们可以在其头部(left)和尾部(right)添加新的元素。在插入时，如果该键并不存在，
   Redis将为该键创建一个新的链表。与此相反，如果链表中所有的元素均被移除，
   那么该键也将会被从数据库中删除。List中可以包含的最大元素数量是4294967295。
       从元素插入和删除的效率视角来看，如果我们是在链表的两头插入或删除元素，
   这将会是非常高效的操作，即使链表中已经存储了百万条记录，该操作也可以在常量时间内完成。
   然而需要说明的是，如果元素插入或删除操作是作用于链表中间，那将会是非常低效的。
   相信对于有良好数据结构基础的开发者而言，这一点并不难理解。
   */
   #####################
    /**
	list相关操作
    lPush
	$redis->lPush(key, value);
	在名称为key的list左边（头）添加一个值为value的 元素
	*/
	$redis->delete('list1');  
	$redis->lPush('list1', 'value1');
	$redis->lPush('list1', 'value2');
	$redis->lPush('list1', 'value3');
	$redis->lPush('list1', 'value4');
	$list1 = $redis->lgetrange('list1',0,-1);
	var_dump($list1);
	var_dump($redis->lsize('list1'));
	/**
	lget
	描述：返回指定键存储在列表中指定的元素。 
	0第一个元素，1第二个… -1最后一个元素，-2的倒数第二…错误的索引或键不指向列表则返回FALSE。
	参数：key index
	返回值：成功返回指定元素的值，失败false
	*/
	var_dump($redis->lget('list1',3));
	var_dump($redis->lset('list1',3,'test1.1'));
	var_dump($redis->lget('list1',3));
	echo '<br/>';
    /**
	返回并弹出指定Key关联的链表中的第一个元素，即头部元素，。如果该Key不存，返回
	*/
	var_dump($redis->lPop('list1'));
	
	$redis->delete('list2');  
	/**
	rPush
	$redis->rPush(key, value);
	在名称为key的list右边（尾）添加一个值为value的 元素
	*/
	$redis->rPush('list2', 'value2.1');
	$redis->rPush('list2', 'value2.2');
	$redis->rPush('list2', 'value2.3');
	$redis->rPush('list2', 'value2.4');
	/**
	返回并弹出指定Key关联的链表中的最后一个元素，即尾部元素，。如果该Key不存，返回false。
	*/
	$redis->rPop('list2');
	/**
	lremove
	描述：从列表中从头部开始移除count个匹配的值。
	如果count为零，所有匹配的元素都被删除。如果count是负数，内容从尾部开始删除。
	参数：key count value
	返回值：成功返回删除的个数，失败false
	*/
	var_dump($redis->lremove('list2','value2.3',2));   //结果：int(2)  
	/**
	lgetrange
	描述：
	返回在该区域中的指定键列表中开始到结束存储的指定元素，
	lGetRange(key, start, end)。0第一个元素，1第二个元素… -1最后一个元素，-2的倒数第二…
	参数：key start end
	返回值：成功返回查找的值，失败false
	返回值类型:array
	*/
	$list2 = $redis->lgetrange('list2',0,-1);
	var_dump($list2);
	echo '<br/>';
?>