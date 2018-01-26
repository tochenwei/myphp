<?php
/**
* PHP����MongoDBѧϰ�ʼ� chenwei
*/
//*************************
//**   ����MongoDB���ݿ�  **//
//*************************
//��ʽ=>("mongodb://�û���:���� @��ַ:�˿�/Ĭ��ָ�����ݿ�",����)

//���Լ�дΪ
//$conn=new Mongo("mongodb://sa:123@localhost"); #���û�������
if(!class_exists('Mongo')){
    echo '��ǰphp����û�а�װMongodb��չ';
    exit;
}
$conn=new Mongo("mongodb://115.47.40.226:27017");

//$conn=new Mongo('mongodb://admin_miss:miss@localhost:27017/test',array('persist'=>'p','replicaSet'=>true)); #����
//��ϸ����:http://www.php.net/manual/en/mongo.connecting.php
//*************************
//**   ѡ�����ݿ����    **//
//*************************
$db=$conn->selectDB('test');
$collection=$db->selectCollection('student'); #�ڶ���д��

//ע��:1.���ݿ�ͼ��ϲ���Ҫ���ȴ���,�����ǲ���������Զ���������.
//   2.ע������,����ܻ������Ĵ���һ���µ����ݿ�(��ԭ�ȵ����ݿ����).
//*************************
//**   �����ĵ�     **//
//*************************
//**�򼯺��в�������,����bool�ж��Ƿ����ɹ�. **/
$rand = rand(100,999);

$array=array('column_name'=>'col'.$rand,'column_exp'=>'xiaocai'.$rand,'age'=>$rand);
$result=$collection->insert($array); #�򵥲���
echo $array['_id']; #MongoDB�᷵��һ����¼��ʶ

var_dump($result); #����:bool(true)

$rand_new = rand(10,99);
//**�򼯺��а�ȫ��������,���ز���״̬(����). **/
$array=array('column_name'=>'col'.$rand_new,'column_exp'=>'xiaocai2'.$rand_new,'age'=>$rand_new);
$result=$collection->insert($array,array('safe'=>false,'fsync'=>false,'timeout'=>10000)); #���ڵȴ�MongoDB��ɲ���,�Ա�ȷ���Ƿ�ɹ�.(���д�����¼����ʱʹ�øò�����Ƚ�����)
echo "�¼�¼ID:".$array['_id']; #MongoDB�᷵��һ����¼��ʶ
var_dump($result); 
echo '<pre>';
//��ѯһ����¼,����ֵ��һ������
$row = $collection->find(array('column_name'=>'col669'));
foreach ($row as $key => $val)
{
    var_dump($val);
}

//**������д�� **/
#insert($array,array('safe'=>false,'fsync'=>false,'timeout'=>10000))
/*
* *
* ������ʽ:insert ( array $a [, array $options = array() ] )
*    insert(array(),array('safe'=>false,'fsync'=>false,'timeout'=>10000))
*       ����:safe:Ĭ��false,�Ƿ�ȫд��
*   fsync:Ĭ��false,�Ƿ�ǿ�Ʋ��뵽ͬ��������
*     timeout:��ʱʱ��(����)
*
* ������:{ "_id" : ObjectId("4d63552ad549a02c01000009"), "column_name" : "col770", "column_exp" : "xiaocai" }
*    '_id'Ϊ�����ֶ�,�ڲ�����MongoDB�Զ����.
*
*    ע��:1.�������β����Ϊͬһ����¼(��ͬ��_id),��Ϊ���ǵ�ֵ��ͬ��
*         $collection->insert(array('column_name'=>'xiaocai'));
*         $collection->insert(array('column_name'=>'xiaocai'));
*     ����
* $collection->insert(array('column_name'=>'xiaocai'),true);
* try {
*      $collection->insert(array('column_name'=>'xiaocai'),true);
* }catch(MongoCursorException $e){
*      echo "Can't save the same person twice!\n";
* }
*
* ��ϸ����:http://www.php.net/manual/zh/mongocollection.insert.php
* *
*/
//*************************
//**   �����ĵ�     **//
//*************************
//** �޸ĸ��� **/
$where=array('column_name'=>'col123');
$newdata=array('column_exp'=>'GGGGGGG','column_fid'=>444);
$result=$collection->update($where,array('$set'=>$newdata)); #$set:��ĳ�ڵ���ڸ���ֵ,���ƵĻ���$pull $pullAll $pop $inc,�ں�������˵���÷�
/*
* ���:
* ԭ����
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_name":"col123","column_exp":"xiaocai"}
* ���滻����
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_name":"col123","column_exp":"GGGGGGG","column_fid":444}
*/
//** �滻���� **/
$where=array('column_name'=>'col709');
$newdata=array('column_exp'=>'HHHHHHHHH','column_fid'=>123);
$result=$collection->update($where,$newdata);
/*
* ���:
* ԭ����
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_name":"col709","column_exp":"xiaocai"}
* ���滻����
* {"_id":ObjectId("4d635ba2d549a02801000003"),"column_exp":"HHHHHHHHH","column_fid":123}
*/
//** �������� **/
$where=array('column_name'=>'col');
$newdata=array('column_exp'=>'multiple','91u'=>684435);
$result=$collection->update($where,array('$set'=>$newdata),array('multiple'=>true));
/**
* ����'column_name'='col'�����޸�
*/
//** �Զ��ۼ� **/
$where=array('91u'=>684435);
$newdata=array('column_exp'=>'edit');
$result=$collection->update($where,array('$set'=>$newdata,'$inc'=>array('91u'=>-5)));
/**
* ����91u=684435������,����91u�Լ�5
*/
/** ɾ���ڵ� **/
$where=array('column_name'=>'col685');
$result=$collection->update($where,array('$unset'=>'column_exp'));
/**
* ɾ���ڵ�column_exp
*/
/*
* *
* ������ʽ:update(array $criteria, array $newobj [, array $options = array()  ] )
*       ע��:1.ע�������滻�������޸ĸ���
*    2.ע���������������� array('91u'=>'684435')��array('91u'=>684435)
* ��ϸ����:http://www.mongodb.org/display/DOCS/Updating#Updating-%24bit
* *
*/
//*************************
//**   ɾ���ĵ�     **//
//*************************

$collection->remove(array('column_name'=>'col373'));

//*************************
//**   ��ռ���,�൱����ձ�    **//
//*************************
//$collection->remove();

/** ɾ��ָ��MongoId **/
$id = new MongoId("571f63ed94a1fe041400002a");
$collection->remove(array('_id'=>(object)$id));

/*
* *
*  ʹ������ķ�����ƥ��{"_id":ObjectId("4d638ea1d549a02801000011")},��ѯ������Ҳһ��
*  $id = new MongoId("4d638ea1d549a02801000011");
*  array('_id'=>(object)$id)
* *
*/
//*************************
//**   ��ѯ�ĵ�     **//
//*************************
/** ��ѯ�ĵ��еļ�¼�� **/
echo 'count:'.$collection->count()."<br>"; #ȫ��
echo 'count:'.$collection->count(array('type'=>'user'))."<br>"; #���Լ�������
echo 'count:'.$collection->count(array('age'=>array('$gt'=>50,'$lte'=>74)))."<br>"; #����50С�ڵ���74
echo 'count:'.$collection->find()->limit(5)->skip(0)->count(true)."<br>"; #���ʵ�ʷ��صĽ����
echo '<hr/>';
//�������併��,-1����1����
$lists = $collection->find()->sort(array('age'=>-1))->limit(3);
foreach ($lists as $_id => $_value) {
    echo "$_id: "; var_dump($_value); echo "<br>";
}
echo '<hr/>';
/**
* ע:$gtΪ���ڡ�$gteΪ���ڵ��ڡ�$ltΪС�ڡ�$lteΪС�ڵ��ڡ�$neΪ�����ڡ�$exists������
*/
/** �����������ĵ� **/
$cursor = $collection->find()->snapshot();
foreach ($cursor as $id => $value) {
    echo "$id: "; var_dump($value); echo "<br>";
}
/**
* ע��:
* ����������find()���������$cursor�α�֮������α껹�Ƕ�̬��.
* ���仰˵,����find()֮��,���ҵ��α�ѭ��������ʱ��,������з��������ļ�¼�����뵽collection,��ô��Щ��¼Ҳ�ᱻ$cursor ���.
* ��������ڻ��$cursor֮��Ľ�������仯,��Ҫ��������
* $cursor = $collection->find();
* $cursor->snapshot();
* ���http://www.bumao.com/index.php/2010/08/mongo_php_cursor.html
*/
/** ��õ�һ������ **/
$cursor = $collection->findOne();
/**
*  ע��:findOne()��ý��������ʹ��snapshot(),fields()�Ⱥ���;
*/
/** age,type �в���ʾ **/
$cursor = $collection->find()->fields(array("age"=>false,"type"=>false));
/** ֻ��ʾuser �� **/
$cursor = $collection->find()->fields(array("user"=>true));
/**
* ������д�����:$cursor->fields(array("age"=>true,"type"=>false));
*/
/** (����type,age�ڵ�) and age!=0 and age<50 **/
$where=array('type'=>array('$exists'=>true),'age'=>array('$ne'=>0,'$lt'=>50,'$exists'=>true));
$cursor = $collection->find($where);
/** ��ҳ��ȡ�����  **/
$cursor = $collection->find()->limit(5)->skip(0);
/** ����  **/
$cursor = $collection->find()->sort(array('age'=>-1,'type'=>1)); ##1��ʾ���� -1��ʾ����,�������Ⱥ�Ӱ������˳��
/** ����  **/
$collection->ensureIndex(array('age' => 1,'type'=>-1)); #1��ʾ���� -1��ʾ����
$collection->ensureIndex(array('age' => 1,'type'=>-1),array('background'=>true)); #�����Ĵ������ں�̨����(Ĭ����ͬ������)
$collection->ensureIndex(array('age' => 1,'type'=>-1),array('unique'=>true)); #��������Ψһ��
/**
* ensureIndex (array(),array('name'=>'��������','background'=true,'unique'=true))
* ���:http://www.php.net/manual/en/mongocollection.ensureindex.php
*/
/** ȡ�ò�ѯ��� **/
$cursor = $collection->find();
$array=array();
foreach ($cursor as $id => $value) {
    $array[]=$value;
}
//*************************
//**   �ĵ�����     **//
//*************************

$conn->close(); #�ر�����
/*
��ϵ�����ݿ���MongoDB���ݴ洢������
MySql���� �ṹ:
CREATE TABLE IF NOT EXISTS `column`(
`column_id` int(16)  NOT NULL  auto_increment  COMMENT '����',
`column_name` varchar(32) NOT NULL COMMENT '��Ŀ����',
PRIMARY KEY  (`column_id`)
);
CREATE TABLE IF NOT EXISTS `article`(
`article_id`  int(16)  NOT NULL  auto_increment  COMMENT '����',
`article_caption` varchar(15) NOT NULL COMMENT '����',
PRIMARY KEY(`article_id`)
);
CREATE TABLE IF NOT EXISTS `article_body`(
`article_id` int(16) NOT NULL COMMENT 'article.article_id',
`body` text COMMENT '����'
);
MongoDB���ݽṹ:
$data=array(
'column_name' =>'default',
'article' =>array(
'article_caption' => 'xiaocai',
'body'   => 'xxxxxxxxxx��'
)
);
$inc
�����¼�ĸýڵ���ڣ��øýڵ����ֵ��N������ýڵ㲻���ڣ��øýڵ�ֵ�� ��N
��ṹ��¼�ṹΪ array('a'=>1,'b'=>'t'),����a��5����ô��
$coll->update(
array('b'=>'t'),
array('$inc'=>array('a'=>5)),
)
$set
��ĳ�ڵ���ڸ���ֵ
��ṹ��¼�ṹΪ array('a'=>1,'b'=>'t'),bΪ��f����ô��
$coll->update(
array('a'=>1),
array('$set'=>array('b'=>'f')),
)
$unset
ɾ��ĳ�ڵ�
���¼�ṹΪ array('a'=>1,'b'=>'t')����ɾ��b�ڵ㣬��ô��
$coll->update(
array('a'=>1),
array('$unset'=>'b'),
)
$push
�����Ӧ�ڵ��Ǹ����飬�͸���һ���µ�ֵ��ȥ�������ڣ��ʹ���������飬������һ��ֵ����������ϣ���� �ýڵ㲻�����飬���ش���
���¼�ṹΪarray('a'=>array(0=>'haha'),'b'=& gt;1)���븽�������ݵ��ڵ�a����ô��
$coll->update(
array('b'=>1),
array('$push'=>array('a'=>'wow')),
)
�� �����ü�¼�ͻ��Ϊ��array('a'=>array(0=>'haha',1=>'wow'),'b'=>1)
$pushAll
��$push���ƣ�ֻ�ǻ�һ�θ��Ӷ����ֵ��ĳ�ڵ�
$addToSet
����ý׶ε�������û��ĳֵ�������֮
���¼�ṹΪarray('a'=>array(0=& gt;'haha'),'b'=>1)������븽���µ����ݵ��ýڵ�a����ô��
$coll->update(
array('b'=>1),
array('$addToSet'=>array('a'=>'wow')),
)
�����a�ڵ����Ѿ�����wow,��ô�Ͳ���������µģ����û�У��ͻ�Ϊ�ýڵ�����µ�item����wow��
$pop
��ü�¼Ϊarray('a'=>array(0=>'haha',1=& gt;'wow'),'b'=>1)
ɾ��ĳ����ڵ�����һ��Ԫ��:
$coll->update(
array('b'=>1),
array('$pop=>array('a'=>1)),
)
ɾ��ĳ����ڵ�ĵ�һ��Ԫ��
$coll->update(
array('b'=>1),
array('$pop=>array('a'=>-1)),
)
$pull
����ýڵ��Ǹ����飬��ôɾ����ֵΪvalue���������������飬�᷵��һ������
��ü�¼Ϊ array('a'=>array(0=>'haha',1=>'wow'),'b'=>1)����Ҫɾ��a��valueΪ haha�����
$coll->update(
array('b'=>1),
array('$pull=>array('a'=>'haha')),
)
�� ��Ϊ�� array('a'=>array(0=>'wow'),'b'=>1)
$pullAll
��$pull���ƣ�ֻ�ǿ���ɾ��һ����������ļ�¼��
*/
?>