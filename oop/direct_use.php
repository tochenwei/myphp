<?php
/**
 * 直接调用那个类来处理
* @author: chenwei
* @date: 2016/11/04
*/

//小偷类
class Thief {
    private $target = [];
    public  $money  = 0;
    function __construct() {
        
    }
    function setMoney($data){
        $this->money = isset($data['money']) ? floatval($data['money']) : 0;
        $this->target[] = new Police();
        foreach ($this->target as $obj) {
            $obj->notice($data);
        }
    }
}
//警察类
class Police {
    function notice($params) {
        print_r($params);
    }
}
/*  测试    */
$obj = new Thief();
$data=['info'=>['name'=>'xiaoqiang','city'=>'xiamen','time'=>'2016-11-04'],'money'=>1000000];
$obj ->setMoney($data);