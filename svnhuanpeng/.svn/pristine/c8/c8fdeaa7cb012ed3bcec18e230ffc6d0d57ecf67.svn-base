<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/25
 * Time: 13:17
 */

/**
 * 获取所有 以 HTTP开头的header参数
 * @return array
 */


/*var_dump($_SERVER);
exit;*/
function getAllHeaders(){
	$headers = array();

	foreach($_SERVER as $key=>$value){
		if(substr($key, 0, 5)==='HTTP_'){
			$key = substr($key, 5);
			//$key = str_replace('_', ' ', $key);
			//$key = str_replace(' ', '-', $key);
			//$key = strtolower($key);
			$headers[$key] = $value;
		}
	}

	return $headers;

}


$post_data = $_POST;  //获取post参数作为对比
$header = getAllHeaders();

$ret = array();
$ret['post'] = $post_data;
$ret['header'] = $header;

//echo json_encode($_SERVER,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
//注意:$_SERVER 可以获取所有 header的参数
//所有在header中自定义的参数 例如:自定义参数名:username  那么 获取方法 $_SERVER['HTTP_USERNAME']  所有均是大写
//echo $_SERVER['HTTP_USERNAME'];
//$retdata = json_encode($ret)."\n";
//file_put_contents('/data/logs/flash.log',$retdata,FILE_APPEND);


header('content-type:application/json;charset=utf8');
echo json_encode($ret, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);