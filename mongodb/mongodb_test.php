<?php
/**
* PHP操作MongoDB学习笔记 chenwei
*/
//*************************
//**   连接MongoDB数据库  **//
//*************************
//格式=>("mongodb://用户名:密码 @地址:端口/默认指定数据库",参数)

//可以简写为
//$conn=new Mongo("mongodb://sa:123@localhost"); #带用户名密码
if(!class_exists('Mongo')){
    echo '当前php环境没有安装Mongodb扩展';
    exit;
}
$conn=new Mongo("mongodb://115.47.40.226:27017");

//$conn=new Mongo('mongodb://admin_miss:miss@localhost:27017/test',array('persist'=>'p','replicaSet'=>true)); #完整
//详细资料:http://www.php.net/manual/en/mongo.connecting.php
//*************************
//**   选择数据库与表    **//
//*************************
$db=$conn->selectDB('test');
$collection=$db->selectCollection('student'); #第二种写法

//注意:1.数据库和集合不需要事先创建,若它们不存在则会自动创建它们.
//   2.注意错别字,你可能会无意间的创建一个新的数据库(与原先的数据库混乱).
//*************************
//**   插入文档     **//
//*************************
//**向集合中插入数据,返回bool判断是否插入成功. **/
$rand = rand(100,999);

$array=array('column_name'=>'col'.$rand,'column_exp'=>'xiaocai'.$rand,'age'=>$rand);
$result=$collection->insert($array); #简单插入
echo $array['_id']; #MongoDB会返回一个记录标识

var_dump($result); #返回:bool(true)

$rand_new = rand(10,99);
//**向集合中安全插入数据,返回插入状态(数组). **/
$array=array('column_name'=>'col'.$rand_new,'column_exp'=>'xiaocai2'.$rand_new,'age'=>$rand_new);
$result=$collection->insert($array,array('safe'=>false,'fsync'=>false,'timeout'=>10000)); #用于等待MongoDB完成操作,以便确定是否成功.(当有大量记录插入时使用该参数会比较有用)
echo "新记录ID:".$array['_id']; #MongoDB会返回一个记录标识
var_dump($result); 
echo '<pre>';
//查询一条记录,返回值是一个数组
$row = $collection->find(array('column_name'=>'col669'));
foreach ($row as $key => $val)
{
    var_dump($val);
}

//**完整的写法 **/
#insert($array,array('safe'=>false,'fsync'=>false,'timeout'=>10000))
/*
* *
* 完整格式:insert ( array $a [, array $options = array() ] )
*    insert(array(),array('safe'=>false,'fsync'=>false,'timeout'=>10000))
*       参数:safe:默认false,是否安全写入
*   fsync:默认false,是否强制插入到同步到磁盘
*     timeout:超时时间(毫秒)
*
* 插入结果:{ "_id" : ObjectId("4d63552ad549a02c01000009"), "column_name" : "col770", "column_exp" : "xiaocai" }
*    '_id'为主键字段,在插入是MongoDB自动添加.
*
*    注意:1.以下两次插入的为同一条记录(相同的_id),因为它们的值相同。
*         $collection->insert(array('column_name'=>'xiaocai'));
*         $collection->insert(array('column_name'=>'xiaocai'));
*     避免
* $collection->insert(array('column_name'=>'xiaocai'),true);
* try {
*      $collection->insert(array('column_name'=>'xiaocai'),true);
* }catch(MongoCursorException $e){
*      echo "Can't save the same person twice!\n";
* }
*
* 详细资料:http://www.php.net/manual/zh/mongocollection.insert.php
* *
*/
//*************************
//**   更新文档     **//
//*************************
//** 修改更新 **/
$where=array('column_name'=>'col123');
$newdata=array('column_exp'=>'GGGGGGG','column_fid'=>444);
$result=$collection->update($where,array('$set'=>$newdata)); #$set:让某节点等于给定值,类似的还有$pull $pullAll $pop $inc,在后面慢慢说明用法
/*
* 结果:
* 原数据
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_name":"col123","column_exp":"xiaocai"}
* 被替换成了
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_name":"col123","column_exp":"GGGGGGG","column_fid":444}
*/
//** 替换更新 **/
$where=array('column_name'=>'col709');
$newdata=array('column_exp'=>'HHHHHHHHH','column_fid'=>123);
$result=$collection->update($where,$newdata);
/*
* 结果:
* 原数据
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_name":"col709","column_exp":"xiaocai"}
* 被替换成了
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_exp":"HHHHHHHHH","column_fid":123}
*/
//** 批量更新 **/
$where=array('column_name'=>'col');
$newdata=array('column_exp'=>'multiple','91u'=>684435);
$result=$collection->update($where,array('$set'=>$newdata),array('multiple'=>true));
/**
* 所有'column_name'='col'都被修改
*/
//** 自动累加 **/
$where=array('91u'=>684435);
$newdata=array('column_exp'=>'edit');
$result=$collection->update($where,array('$set'=>$newdata,'$inc'=>array('91u'=>-5)));
/**
* 更新91u=684435的数据,并且91u自减5
*/
/** 删除节点 **/
$where=array('column_name'=>'col685');
$result=$collection->update($where,array('$unset'=>'column_exp'));
/**
* 删除节点column_exp
*/
/*
* *
* 完整格式:update(array $criteria, array $newobj [, array $options = array()  ] )
*       注意:1.注意区分替换更新与修改更新
*    2.注意区分数据类型如 array('91u'=>'684435')与array('91u'=>684435)
* 详细资料:http://www.mongodb.org/display/DOCS/Updating#Updating-%24bit
* *
*/
//*************************
//**   删除文档     **//
//*************************

$collection->remove(array('column_name'=>'col373'));

//*************************
//**   清空集合,相当于清空表    **//
//*************************
//$collection->remove();

/** 删除指定MongoId **/
$id = new MongoId("571f63ed94a1fe041400002a");
$collection->remove(array('_id'=>(object)$id));

/*
* *
*  使用下面的方法来匹配{"_id":ObjectId("4d638ea1d549a02801000011")},查询、更新也一样
*  $id = new MongoId("4d638ea1d549a02801000011");
*  array('_id'=>(object)$id)
* *
*/
//*************************
//**   查询文档     **//
//*************************
/** 查询文档中的记录数 **/
echo 'count:'.$collection->count()."<br>"; #全部
echo 'count:'.$collection->count(array('type'=>'user'))."<br>"; #可以加上条件
echo 'count:'.$collection->count(array('age'=>array('$gt'=>50,'$lte'=>74)))."<br>"; #大于50小于等于74
echo 'count:'.$collection->find()->limit(5)->skip(0)->count(true)."<br>"; #获得实际返回的结果数
echo '<hr/>';
//按照年龄降序,-1降序1升序
$lists = $collection->find()->sort(array('age'=>-1))->limit(3);
foreach ($lists as $_id => $_value) {
    echo "$_id: "; var_dump($_value); echo "<br>";
}
echo '<hr/>';
/**
* 注:$gt为大于、$gte为大于等于、$lt为小于、$lte为小于等于、$ne为不等于、$exists不存在
*/
/** 集合中所有文档 **/
$cursor = $collection->find()->snapshot();
foreach ($cursor as $id => $value) {
    echo "$id: "; var_dump($value); echo "<br>";
}
/**
* 注意:
* 在我们做了find()操作，获得$cursor游标之后，这个游标还是动态的.
* 换句话说,在我find()之后,到我的游标循环完成这段时间,如果再有符合条件的记录被插入到collection,那么这些记录也会被$cursor 获得.
* 如果你想在获得$cursor之后的结果集不变化,需要这样做：
* $cursor = $collection->find();
* $cursor->snapshot();
* 详见http://www.bumao.com/index.php/2010/08/mongo_php_cursor.html
*/
/** 获得第一条数据 **/
$cursor = $collection->findOne();
/**
*  注意:findOne()获得结果集后不能使用snapshot(),fields()等函数;
*/
/** age,type 列不显示 **/
$cursor = $collection->find()->fields(array("age"=>false,"type"=>false));
/** 只显示user 列 **/
$cursor = $collection->find()->fields(array("user"=>true));
/**
* 我这样写会出错:$cursor->fields(array("age"=>true,"type"=>false));
*/
/** (存在type,age节点) and age!=0 and age<50 **/
$where=array('type'=>array('$exists'=>true),'age'=>array('$ne'=>0,'$lt'=>50,'$exists'=>true));
$cursor = $collection->find($where);
/** 分页获取结果集  **/
$cursor = $collection->find()->limit(5)->skip(0);
/** 排序  **/
$cursor = $collection->find()->sort(array('age'=>-1,'type'=>1)); ##1表示降序 -1表示升序,参数的先后影响排序顺序
/** 索引  **/
$collection->ensureIndex(array('age' => 1,'type'=>-1)); #1表示降序 -1表示升序
$collection->ensureIndex(array('age' => 1,'type'=>-1),array('background'=>true)); #索引的创建放在后台运行(默认是同步运行)
$collection->ensureIndex(array('age' => 1,'type'=>-1),array('unique'=>true)); #该索引是唯一的
/**
* ensureIndex (array(),array('name'=>'索引名称','background'=true,'unique'=true))
* 详见:http://www.php.net/manual/en/mongocollection.ensureindex.php
*/
/** 取得查询结果 **/
$cursor = $collection->find();
$array=array();
foreach ($cursor as $id => $value) {
    $array[]=$value;
}
//*************************
//**   文档聚类     **//
//*************************

$conn->close(); #关闭连接
/*
关系型数据库与MongoDB数据存储的区别
MySql数据 结构:
CREATE TABLE IF NOT EXISTS `column`(
`column_id` int(16)  NOT NULL  auto_increment  COMMENT '主键',
`column_name` varchar(32) NOT NULL COMMENT '栏目名称',
PRIMARY KEY  (`column_id`)
);
CREATE TABLE IF NOT EXISTS `article`(
`article_id`  int(16)  NOT NULL  auto_increment  COMMENT '主键',
`article_caption` varchar(15) NOT NULL COMMENT '标题',
PRIMARY KEY(`article_id`)
);
CREATE TABLE IF NOT EXISTS `article_body`(
`article_id` int(16) NOT NULL COMMENT 'article.article_id',
`body` text COMMENT '正文'
);
MongoDB数据结构:
$data=array(
'column_name' =>'default',
'article' =>array(
'article_caption' => 'xiaocai',
'body'   => 'xxxxxxxxxx…'
)
);
$inc
如果记录的该节点存在，让该节点的数值加N；如果该节点不存在，让该节点值等 于N
设结构记录结构为 array('a'=>1,'b'=>'t'),想让a加5，那么：
$coll->update(
array('b'=>'t'),
array('$inc'=>array('a'=>5)),
)
$set
让某节点等于给定值
设结构记录结构为 array('a'=>1,'b'=>'t'),b为加f，那么：
$coll->update(
array('a'=>1),
array('$set'=>array('b'=>'f')),
)
$unset
删除某节点
设记录结构为 array('a'=>1,'b'=>'t')，想删除b节点，那么：
$coll->update(
array('a'=>1),
array('$unset'=>'b'),
)
$push
如果对应节点是个数组，就附加一个新的值上去；不存在，就创建这个数组，并附加一个值在这个数组上；如果 该节点不是数组，返回错误。
设记录结构为array('a'=>array(0=>'haha'),'b'=& gt;1)，想附加新数据到节点a，那么：
$coll->update(
array('b'=>1),
array('$push'=>array('a'=>'wow')),
)
这 样，该记录就会成为：array('a'=>array(0=>'haha',1=>'wow'),'b'=>1)
$pushAll
与$push类似，只是会一次附加多个数值到某节点
$addToSet
如果该阶段的数组中没有某值，就添加之
设记录结构为array('a'=>array(0=& gt;'haha'),'b'=>1)，如果想附加新的数据到该节点a，那么：
$coll->update(
array('b'=>1),
array('$addToSet'=>array('a'=>'wow')),
)
如果在a节点中已经有了wow,那么就不会再添加新的，如果没有，就会为该节点添加新的item——wow。
$pop
设该记录为array('a'=>array(0=>'haha',1=& gt;'wow'),'b'=>1)
删除某数组节点的最后一个元素:
$coll->update(
array('b'=>1),
array('$pop=>array('a'=>1)),
)
删除某数组节点的第一个元素
$coll->update(
array('b'=>1),
array('$pop=>array('a'=>-1)),
)
$pull
如果该节点是个数组，那么删除其值为value的子项，如果不是数组，会返回一个错误。
设该记录为 array('a'=>array(0=>'haha',1=>'wow'),'b'=>1)，想要删除a中value为 haha的子项：
$coll->update(
array('b'=>1),
array('$pull=>array('a'=>'haha')),
)
结 果为： array('a'=>array(0=>'wow'),'b'=>1)
$pullAll
与$pull类似，只是可以删除一组符合条件的记录。
*/
?>