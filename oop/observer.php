<?php
/**
 * 观察者模式
 * @author: chenwei
 * @date: 2016/11/04
 */
//小偷类
class Thief{
    private $_observers = array();
    public $money = 0;
    public function register($observer){ /*注册观察者 */
        $this->_observers[] = $observer;
    } 
    public function trigger($func,array $data){  /*外部统一访问*/
        $this->money=isset($data['money']) ? floatval($data['money']) : 0;
        if(!empty($this->_observers)){
            foreach($this->_observers as $observer){
                /**mixed call_user_func_array ( callable $callback , array $param_arr )
                 *第一个参数是函数名，第二个参数是参数数组
                 */
                call_user_func_array(array($observer, $func),$data);
            }
        }
    }
}

//警察类
class Police{
    public function notice($v){
        print_r($v);
        return $v;
    }
}

/*  测试    */
$thief = new Thief();
$thief->register(new Police());
$data=['info'=>['name'=>'xiaoqiang','city'=>'xiamen','time'=>'2016-11-04'],'money'=>1000000];
$thief->trigger('notice',$data);
echo $thief->money;
/***********输出结果*************/
//Array ( [name] => xiaoqiang [city] => xiamen [time] => 2016-11-04 ) 1000000