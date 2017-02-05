<?php
class Sign {
    private $key;
    //保存类实例的静态成员变量
	private static $_instance;
	//单例方法,用于访问实例的公共的静态方法
	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	public function setKey($key){
		$this->key = $key;
		return true;
	}	
	function encrypt($input) { 
		$size = mcrypt_get_block_size('des', 'ecb');    //本函数用来取得编码方式的区块大小
		$input = $this->pkcs5_pad($input, $size);    
		$td = mcrypt_module_open('des', '', 'ecb', '');     
		$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);		
		@mcrypt_generic_init($td, $this->key, $iv);     
		$data = mcrypt_generic($td, $input);     
		mcrypt_generic_deinit($td);     
		mcrypt_module_close($td);     
		$data = base64_encode($data);     
		return $data; 
	} 
	function decrypt($encrypted) { 
		$encrypted = base64_decode($encrypted);     
		$td = mcrypt_module_open('des','','ecb',''); //使用MCRYPT_DES算法,cbc模式          
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);       
		$ks = mcrypt_enc_get_key_size($td);         
		@mcrypt_generic_init($td, $this->key, $iv);       //初始处理           
		$decrypted = mdecrypt_generic($td, $encrypted);       //解密           
		mcrypt_generic_deinit($td);       //结束          
		mcrypt_module_close($td);               
		$y=$this->pkcs5_unpad($decrypted);        
		return $y; 
	} 
	function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);    
		return $text . str_repeat(chr($pad), $pad); 
	} 
	function pkcs5_unpad($text) { 
		$pad = ord($text{strlen($text)-1}); 
		if ($pad > strlen($text)) return false; 
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) 
		return false;    
		return substr($text, 0, -1 * $pad); 
	}  
}
/**
 * 调试函数
 * author: chenwei
 * */
function debug_dump($text,$sign=''){
    $logdir=dirname(__FILE__).'/log/';
    if(!is_dir($logdir))return false;
    if(!is_writeable($logdir))@chmod($logdir,0777);
    static $debug_times = 1;
    $file_mark = $sign=='' ? $debug_times : $sign;
    $logfilename = $logdir.'dump_' . date('Y_m_d_').$file_mark . '.txt';
    $_stamp = '';
    $_stamp .= "-------------------------------------------------------------\n";
    $_stamp .= $_SERVER['REQUEST_URI']."\n";
    $_stamp .= "-------------------------------------------------------------\n";

    ob_start();
    echo '<pre>';
    print_r($text);
    $str = ob_get_contents();
    $str .= "\n\n";
    $str .= $_stamp;
    $str = preg_replace("/^<pre>/",'',$str);
    ob_end_clean();

    if(!empty($sign)){
        $str = '('.$sign."):\n".$str;
    }
    file_put_contents($logfilename, $str, FILE_APPEND);
    $debug_times++;
}
/**
     * 生成授权码
     * @return string 授权码
     */
function getAuthorization($token){
	return md5(substr(md5($token), 8, 24).$token);
}
if(empty($_GET['HTTP_CLIENTCODE'])){
	echo 'Forbidden';
	exit;
}
$client_token = 'HxI8xE10lsf3e6654103nmDx';
if(getAuthorization($client_token) != $_GET['HTTP_CLIENTCODE']){
	echo 'authorize_error';
	exit;
}

print_r($_GET);
echo '<br/>';

print_r($_POST);
echo '<br/>';


$request_data = array();
switch($_SERVER['REQUEST_METHOD']){
	case 'GET':
		$request_data = $_GET;
		break;
    case 'POST':
		$request_data = $_POST;
		break;
	case 'PUT':
		$request_data = file_get_contents('php://input');
		break;
    case 'DELETE':
		$request_data = file_get_contents('php://input');
		break;
	default:
	    break;
}
$sign = Sign::getInstance();
$sign->setKey($client_token);
$_params=array();
switch($_SERVER['REQUEST_METHOD']){
	case 'GET':
	case 'POST':
	$_params = json_decode($sign->decrypt($request_data['sign']),true);
	break;
	case 'PUT':
	case 'DELETE':
	$_params = json_decode($sign->decrypt($request_data),true);
	break;
}
print_r($_params);
echo '<br/>';