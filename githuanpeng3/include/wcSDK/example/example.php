<?php

//上传文件例子：
//引入自动加载文件和命名空间，上传类的命名空间为Wcs\Upload, Uploader类为上传类
require '../vendor/autoload.php';
use Wcs\Upload\Uploader;

//请先填入相关参数
//关于参数的详细说明，请参见wcs 文档
$userParam = '';
$userVars = '';
$mimeType = '';
$bucketName = '';
$fileKey = '';
$localFile = '';
$returnBody = '';
$callbackUrl = '';
$callbackBody = '';
$cmd = '';
$notifyUrl = '';


//实例化一个Uploader类
$client = new Uploader($userParam, $userVars, $mimeType);/*传入可选参数*/
//$client = new Uploader(); /*可选参数不传入*/

//普通上传函数
$client->upload_return($bucketName, $fileKey, $localFile, $returnBody);
//回调上传
$client->upload_callback($bucketName, $fileKey, $localFile, $callbackUrl, $callbackBody);
//通知上传
$client->upload_notify($bucketName, $fileKey, $localFile, $cmd, $notifyUrl);