<?php

//上传文件例子：
//引入自动加载文件和命名空间，上传类的命名空间为Wcs\Upload, Uploader类为上传类
require '../vendor/autoload.php';
use Wcs\Upload\Uploader;
use Wcs\SrcManage\FileManager;
use Wcs\SrcManage\FileDownloader;
use Wcs\Fmgr\Fmgr;
use Wcs\PersistentFops\Fops;
use Wcs\ImageProcess\ImageInfo;
use Wcs\ImageProcess\ImageView;
use Wcs\ImageProcess\ImageWatermark;
use Wcs\ImageProcess\ImageMogr;

//参数
$bucket = 'wuyikun';
$key = 'test.001.jpg';
$host = '';
$upload = new Uploader();
print_r('upload test.001.jpg'."\n");
print_r($upload->upload_return($bucket, $key, $key)."\n\n");
$client = new FileManager();

//list
print_r("list\n");
$limit = 10;
print_r($client->bucketList($bucket, $limit));
print_r("\n");
print_r("\n");

//stat
print_r("stat\n");
print_r($client->stat($bucket, $key));
print_r("\n");
print_r("\n");

//move
print_r("move\n");
print_r($client->move($bucket, $key, $bucket, $key.'.move'));
print_r("\n");
print_r("\n");


//copy
print_r("copy\n");
print_r($client->copy($bucket, $key, $bucket, $key.'.copy'));
print_r("\n");
print_r("\n");

//update
print_r("update\n");
print_r($client->updateMirrorSrc($bucket, $key));
print_r("\n");
print_r("\n");



//delete
print_r("delete\n");
$client = new FileManager();
print_r($client->delete($bucket, $key));
print_r("\n");
print_r("\n");


$upload->upload_return($bucket, $key, $key);
print_r("upload img success\n");

//img 
$client = new ImageInfo();

//img info
print_r("img info\n");
print_r($client->imginfo($bucket, $key));
print_r("\n");
print_r("\n");

//img exif
print_r("img exif\n");
print_r($client->imgEXIF($bucket, $key));
print_r("\n");
print_r("\n");

//img view
print_r("img view\n");
$img = new ImageView(1);
$img->width = 200;
$img->height = 200;
print_r($img->exec($bucket, $key));
print_r("\n");
print_r("\n");

//img mogr
print_r("img mogr\n");
$img = new ImageMogr();
$img->thumbnail = '!10p';
print_r($img->exec($bucket, $key));
print_r("\n");
print_r("\n");

//img watermark
print_r("img watermark\n");
$img = new ImageWatermark(2, "test");
print_r($img->exec($bucket, $key, 'watermark.png'));
print_r("\n");
print_r("\n");

//persistent 
$bucket = 'azfundb-test001';
$key = '{9FD7F46E-6E2C-43DA-A98F-D2F5DF93181E}.mkv';

//参数设置
$notifyURL = 'http://xc.db.dz11.com/playstream/notifyUrl';
$force = 0;
$separate = 0;
//$save1 = \Wcs\url_safe_base64_encode('<input key>');
//$save2 = \Wcs\url_safe_base64_encode('<input key>');

$fops = 'avthumb/m4a/ab/64k;avthumb/flv/ab/64k';
//$fops = 'vframe/jpg/offset/10|saveas/'.$save1.';vframe/jpg/offset/15|saveas/'.$save2;
$client = new Fops($bucket, $key, $fops, $notifyURL, $force, $separate);
print_r("PersistentFops\n");
$result = $client->exec();
print_r($result);
print_r("\n");
print_r("\n");

//persistent status
print_r("persistent status\n");
$tmp = json_decode($result, true);
if(isset($tmp['persistentId'])) {
    $id = $tmp['persistentId'];
    print_r(Fops::status($id));
    print_r("\n");
    print_r("\n");
}


//fmgr
//可选参数
$notifyURL = '';
$force = 0;
$separate  = 0;

//fops参数
$fetchURL = \Wcs\url_safe_base64_encode('https://www.baidu.com/img/bd_logo1.png');
$bucket = \Wcs\url_safe_base64_encode('wuyikun');
$key = \Wcs\url_safe_base64_encode('fmgr_fetch_img.png');
$prefix = \Wcs\url_safe_base64_encode('fmgr');
//$md5 = null;

$fops = 'fops=fetchURL/'.$fetchURL.'/bucket/'.$bucket.'/key/'.$key.'/prefix/'.$prefix.'&notifyURL='.\Wcs\url_safe_base64_encode($notifyURL).'&force='.$force.'&separate='.$separate;

$client = new Fmgr($notifyURL, $force, $separate);

print_r("fmgr fetch\n");
print_r($client->fetch($fops));
print_r("\n");
print_r("\n");

//fmgr copy
$notifyURL = '';
$force = 0;
$separate  = 0;

//fops参数
$resource = \Wcs\url_safe_base64_encode('wuyikun:test.001.jpg');
$bucket = \Wcs\url_safe_base64_encode('wuyikun');
$key = \Wcs\url_safe_base64_encode('fmgr_copy_test.001.jpg');
$prefix = \Wcs\url_safe_base64_encode('fmgr');
$fops = 'fops=resource/'.$resource.'/bucket/'.$bucket.'/key/'.$key.'/prefix/'.$prefix.'&notifyURL='.\Wcs\url_safe_base64_encode($notifyURL).'&force='.$force.'&separate='.$separate;

print_r("fmgr copy\n");
print_r($client->copy($fops));
print_r("\n");
print_r("\n");

//fgmr move
$key = \Wcs\url_safe_base64_encode('fmgr_copy_test.001.jpg');
$fops = 'fops=resource/'.$resource.'/bucket/'.$bucket.'/key/'.$key.'/prefix/'.$prefix.'&notifyURL='.\Wcs\url_safe_base64_encode($notifyURL).'&force='.$force.'&separate='.$separate;
print_r("fmgr move\n");
print_r($client->move($fops));
print_r("\n");
print_r("\n");

//fmgr delete
$bucket = \Wcs\url_safe_base64_encode('wuyikun');
$key = \Wcs\url_safe_base64_encode('test.001.jpg');
$fops = 'fops=bucket/'.$bucket.'/key/'.$key.'&notifyURL='.\Wcs\url_safe_base64_encode($notifyURL).'&force='.$force.'&separate='.$separate;

print_r("fmgr delete\n");
print_r($client->delete($fops));
print_r("\n");
print_r("\n");

//fmgr deletePrefix
$bucket = \Wcs\url_safe_base64_encode('wuyikun');
$prefix = \Wcs\url_safe_base64_encode('fmgr');
$output = \Wcs\url_safe_base64_encode('wuyikun:fmg_deletePrefix');
$fops = 'fops=bucket/'.$bucket.'/prefix/'.$prefix.'&notifyURL='.\Wcs\url_safe_base64_encode($notifyURL).'&force='.$force.'&separate='.$separate;

print_r("fmgr deletePrefix\n");
$result = $client->deletePrefix($fops);
print_r($result);
print_r("\n");
print_r("\n");

//fmgr status
print_r("fmgr status\n");
$tmp = json_decode($result, true);
if(isset($tmp['persistentId'])) {
    $id = $tmp['persistentId'];
    print_r($client->status($id));
    print_r("\n");

}

//download
//print_r("download\n");
//$client = new FileDownloader($bucket, $key, 'test.download');
//print_r($client->download());
//print_r("\n");
//print_r("\n");
