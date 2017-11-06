<?php
/**
 * 上传截图接口
 * 参数：uid｜encpass｜liveID
 * 
 *   */
//暂时关掉直播截图

include '../../../include/init.php';
succ();
include (INCLUDE_DIR . 'upload.class.php');
include (INCLUDE_DIR . 'MyImage.class.php');
include (INCLUDE_DIR . 'Anchor.class.php');
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function convert($size){
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}


$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? $_POST['encpass'] : '';
/* if($uid==2326){
    $uid = 15;
    $encpass = '63ee451939ed580ef3c4b6f0109d1fd0';
} */
// $liveid = isset($_POST['liveID']) ? trim($_POST['liveID']) : '';
$header = json_encode($_SERVER); // var_dump(json_encode($GLOBALS['_POST'],JSON_UNESCAPED_UNICODE));
mylog($header, LOGFN_VIDEO_SAVE_ERR);
mylog($_FILES['file']['size'] . '---' . json_encode($GLOBALS['_POST'], JSON_UNESCAPED_UNICODE), LOGFN_VIDEO_SAVE_SUC);
$uid = checkInt($uid);
$anchor = new AnchorHelp($uid);
$liveid = $anchor->getLastLiveid();
mylog("--uid:$uid--enc:$encpass--liveid:$liveid", LOGFN_VIDEO_SAVE_ERR);
$liveid = checkInt($liveid);
$encpass = checkStr($encpass);

$db = new DBHelperi_huanpeng();

$code = checkUserState($uid, $encpass, $db);

if (true !== $code)
    error2(-4067,2);

//直播截图每次用新的替换，不随机名字和目录
$upConfig = array('rand'=>false,'randdir'=>false);
$dstfile = md5($liveid).'.'.pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
$_FILES['file']['name'] =  $dstfile;
$upPath = $conf['img-dir'] . '/live/'.$dstfile[0].'/'.$dstfile[1].'/';
//创建目录
if(!is_dir($upPath)&&!mkdirs($upPath)){
    mylog('create dir failed',LOGFN_VIDEO_SAVE_ERR);
    error2(-6002,2);
}

$upObj = new UpLoad($upPath,$upConfig);
$picUrl = $upObj->exec($_FILES['file']); // 上传文件
$err=$upObj->getErrCode();
$picUrl = 'live/'.$dstfile[0].'/'.$dstfile[1].'/'.$picUrl[0];
mylog('2step:' . $picUrl.'---'.$err, LOGFN_VIDEO_SAVE_SUC);
if ($errcode = $upObj->getErrCode()){
    error2($errcode); // 上传失败
}
    

$sql = "SELECT `orientation` FROM `live` WHERE `liveid` = $liveid";
$res = $db->query($sql);
$row = $res->fetch_row();
$orientation = $row[0];
$degrees = $orientation * 90;
/* 
$img = new MyImage($conf['img-dir'] . '/' . $picUrl);
$f = $img->rotate($degrees); // 旋转操作

$test = $f?1:0;
if (! $f)
 * 
    error($img->errcode);  */ 


$picUrl = $db->realEscapeString($picUrl);
mylog('4-step:' . $picUrl, LOGFN_VIDEO_SAVE_SUC);
$t=time();
$sql = "update live set poster='$picUrl?$t' where liveid=$liveid";//var_dump($sql);
mylog('3step:--1' . $sql, LOGFN_VIDEO_SAVE_SUC);
$res = $db->query($sql);
succ();

