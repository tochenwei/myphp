<?php
header('Content-Type:text/html;Charset=utf-8');  
$arr = array(  
    "user" => $_GET['loginuser'],  
    "pass" => $_GET['loginpass'],  
    "name" => 'response'  
  
);  
echo $_GET['jsoncallback'] . "(".json_encode($arr).")";  
//echo json_encode($arr);  