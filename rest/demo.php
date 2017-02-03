<?php
 
function curlrequest($url,$data,$method='post'){
    $ch = curl_init(); //��ʼ��CURL��� 
    curl_setopt($ch, CURLOPT_URL, $url); //���������URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //��ΪTRUE��curl_exec()���ת��Ϊ�ִ���������ֱ����� 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //��������ʽ
     
    curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: $method"));//����HTTPͷ��Ϣ
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//�����ύ���ַ���
    $document = curl_exec($ch);//ִ��Ԥ�����CURL 
    if(!curl_errno($ch)){ 
      $info = curl_getinfo($ch); 
      echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
    } else { 
      echo 'Curl error: ' . curl_error($ch); 
    }
    curl_close($ch);
     
    return $document;
}
 
$url = 'http://test.cc/rest/server.php';
$data = array("content"=>"request from put method");
$data = serialize($data);
$return = curlrequest($url, $data, 'put');
 
var_dump($return);
?>