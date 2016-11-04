<?php
/**
 * 魔术方法+反射
 * @author: chenwei
 * @date: 2016/11/04
 */
class Police {
    function notice($params) {
        print_r($params);
    }
}
class Thief {
    private $target = [];
    public  $money  = 0;
    function __construct() {
        $this->target[] = new Police();
    }
    function setMoney($number){
        $this->money = $number;
    }
    function __call($name, $args) {
        foreach ($this->target as $obj) {
            $r = new ReflectionClass($obj);
            if ($method = $r->getMethod($name)) {
                if ($method->isPublic() && !$method->isAbstract()) {
                    return $method->invoke($obj, $args);
                }
            }
        }
    }
}
/*  测试    */
$obj = new Thief();
$obj ->setMoney(1000000);
$data=['info'=>['name'=>'xiaoqiang','city'=>'xiamen','time'=>'2016-11-04'],'money'=>$obj->money];
//运行结果
//Array ( [0] => Array ( [info] => Array ( [name] => xiaoqiang [city] => xiamen [time] => 2016-11-04 ) [money] => 1000000 ) )
$obj->notice($data);