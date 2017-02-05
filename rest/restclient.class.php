<?php
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2016-2017                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original chenwei <tochenwei@163.com>                        |
// +----------------------------------------------------------------------+
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
class restClient {
    private $ch;
    //public $http_code;
    private $http_url;
    public $api_url;
    public $timeout = 10;
    public $connecttimeout = 30;
    public $ssl_verifypeer = false;
    public $format = '';
    public $decodeFormat = 'json';
    //public $http_info = array();
    public $http_header = array();
    private $contentType;
    private $postFields;
    private static $supportExtension = array(
        'json',
        'xml'
    );
    private $file = null;
    private static $userAgent = 'Mozilla/4.0 (compatible; MSIE .0; Windows NT 6.1; Trident/4.0; SLCC2;)';
	//请求的token
    public static $token='HxI8xE10lsf3e6654103nmDx';
    public function __construct() {
        $this->ch = curl_init();
        //curl_setopt($this->ch, CURLOPT_USERAGENT, self::$userAgent);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);
        
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, array(
            $this,
            'getHeader'
        ));
	
        curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
    }
    public function call($url, $method, $postFields = null, $contentType = null) {
        if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0 && !empty($this->format)) {
            $url = "{$this->api_url}{$url}.{$this->format}";
        }
        $this->http_url = $url;
        $this->contentType = $contentType;
		
		$sign = Sign::getInstance();
		$sign->setKey(self::$token);
		$_params=array();
		if(in_array($method,array('GET','POST'))){
			$_params['sign'] = $sign->encrypt(json_encode($postFields));
		}else{
			$_params = '';
			$_params = $sign->encrypt(json_encode($postFields));
		}
        $this->postFields = $_params;
        $url = $this->to_url($method);
        is_object($this->ch) or $this->__construct();
        switch ($method) {
            case 'POST':
                curl_setopt($this->ch, CURLOPT_POST, TRUE);
                if ($this->postFields != null) {
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postFields);
                }
                break;

            case 'DELETE':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				$header[] = "application/x-www-form-urlencoded";//定义header，可以加多个
				curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);//定义header
                if ($this->postFields != null) {
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postFields);
                }
                break;

            case 'PUT':
			    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				$header[] = "application/x-www-form-urlencoded";//定义header，可以加多个
				curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);//定义header
                if ($this->postFields != null) {
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postFields);
                }
                break;
        }
        
        $this->contentType != null && curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-type:' . $this->contentType
        ));
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $response = curl_exec($this->ch);
        //$this->http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        //$this->http_info = array_merge($this->http_info, curl_getinfo($this->ch));
        return $response;
    }
    public function _POST($url, $params = null,  $contentType = null) {
        return $this->call($url, 'POST', $params, $contentType);
    }
    public function _PUT($url, $params = null, $contentType = null) {
		
        return $this->call($url, 'PUT', $params, $contentType);
    }
    public function _GET($url, $params = null, $contentType = null) {
        return $this->call($url, 'GET', $params, $contentType);
    }
    public function _DELETE($url, $params = null, $contentType = null) {
        return $this->call($url, 'DELETE', $params, $contentType);
    }
    public function get_http_url() {
        $parts = parse_url($this->http_url);
        $port = @$parts['port'];
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = @$parts['path'];
        $port or $port = ($scheme == 'https') ? '443' : '80';
        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        return "$scheme://$host$path";
    }
    public function to_url($method) {
        $post_data = in_array($method,array('PUT','DELETE')) ? $this->postFields : $this->to_postdata();
        $out = $this->get_http_url();
        if ($post_data) {
			$out.= '?';
			if($method =='GET'){
				$out.= $post_data;
			}
			/**
			*  服务端要校验下面参数
			*  $_GET[HTTP_CLIENTCODE] => b758e510038a94b027c9dcc0c740ebb1
			*/
			$out.=  "&HTTP_CLIENTCODE=".$this->setAuthorization();
        }
        return $out;
    }
    public function to_postdata() {
        return http_build_query($this->postFields);
    }
    public function close() {
        curl_close($this->ch);
    }
    public function setURL($url) {
        $this->url = $url;
    }
    public function setDecodeFormat($format = null) {
        if ($format == null) return false;
        $this->decodeFormat = $format;
        return true;
    }
    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }
	/**
     * 生成授权码
     * @return string 授权码
     */
    private function setAuthorization(){
        return md5(substr(md5(self::$token), 8, 24).self::$token);
    }
    public function setMethod($method) {
        $this->method = $method;
    }
    public function setParameters($params) {
        $this->postFields = $params;
    }
    public function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
	/**
     * 关闭curl连接
     */
    public function __destruct(){
        curl_close($this->ch);
    }
}
header("Content-type: text/html; charset=utf-8");
$data = array('id'=>110,'amount'=>'20','info'=>'没错，就是三五警察在街上来回的“溜达”
　　最近，有网友不理解警察这样“溜达”有什么作用，于是向中国<<警察>>网发起了提问：');

$url   = 'http://www.testhrguo.cc/rest/server.php';
$client = new restClient;
$result = $client->_GET($url,$data);
print_r($result);
$result = $client->_POST($url,$data);
print_r($result);
$result = $client->_PUT($url,$data);
print_r($result);
$result = $client->_DELETE($url,$data);
print_r($result);
?>