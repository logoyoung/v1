<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/12/29
 * Time: 11:17
 */
//todo
//get file from server
$file = $_GET['type']?$_GET['type']:'';
$base = '../../liveTools/PC/';
$soft = array(
    'huanpeng'=>'HuanpengSetup.exe',
    'bonjour32'=>'Bonjour.msi',
    'bonjour64'=>'Bonjour64.msi'
);
$file =  in_array($file,array_keys($soft))?$base.$soft[$file]:$base.$soft['huanpeng'];
header('Content-Description: File Transfer'); //描述页面返回的结果
header('Content-Type: application/octet-stream'); //返回内容的类型，此处只知道是二进制流。具体返回类型可参考http://tool.oschina.net/commons
header('Content-Disposition: attachment; filename='.basename($file));//可以让浏览器弹出下载窗口
header('Content-Transfer-Encoding: binary');//内容编码方式，直接二进制，不要gzip压缩
header('Expires: 0');//过期时间
header('Cache-Control: must-revalidate');//缓存策略，强制页面不缓存，作用与no-cache相同，但更严格，强制意味更明显
header('Pragma: public');
header('Content-Length: ' . filesize($file));//文件大小，在文件超过2G的时候，filesize()返回的结果可能不正确
readfile($file);