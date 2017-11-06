<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/15
 * Time: 下午4:14
 */

include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

/**
 * 创建路径
 *
 * @param string $preDir
 * @param string $lastDir
 * @return bool
 */
function createDir($preDir, $lastDir)
{
	$temp = '';
	$result = true;

	$arr = explode('/', $lastDir);

	foreach ($arr as $v)
	{
		$temp .= $v.'/';
		$dir = $preDir.$temp;
		if( !is_dir($dir) )
			$result = mkdir($dir);
	}

	return $result;
}

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$bankGroup = array(
	'icbc',
	'ccb',
	'abc',
	'bcm',
	'boc',
	'cmbc',
	'cmb',
	'psbc'
);

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if(!$uid || !$enc)  exit('-1');

$bank = isset($_POST['bank']) ? trim($_POST['bank']) : '';
$cardid = isset($_POST['cardid']) ? trim($_POST['cardid']) : '';

if(!$bank || !in_array($bank, $bankGroup))
	exit(-2);

if(!$cardid) exit(-3);

$code = checkUserState($uid, $enc , $db);
if($code !== true) error($code);

$front_fileName = md5($uid . $enc . "front" . "bank") . '.png';
$back_fileName = md5($uid . $enc . "back" . "bank") . '.png';

$tmpDir =  $conf['img-dir'] . '/tempImg/';

if(!is_file($tmpDir . $front_fileName) || !is_file($tmpDir . $back_fileName)){
	exit(-4);//文件不存在
}

$saveDir = $uid . "/bank";
if(!is_dir($conf['img-dir'] . $saveDir)){
	if(!createDir($conf['img-dir']."/", $saveDir)){
		error('-1023');
	}
}

$file['front'] = $conf['img-dir'] . '/' . $saveDir . "/" . $front_fileName;
$file['back'] = $conf['img-dir'] . '/' . $saveDir . "/" . $back_fileName;

if( !copy( $tmpDir . $front_fileName,  $file['front']) || !copy($tmpDir . $front_fileName, $file['back']))
	error('-1024');

$db_front = $db->realEscapeString( '/' . $saveDir . "/" . $front_fileName);
$db_back = $db->realEscapeString('/' . $saveDir . "/" . $back_fileName);
$cur_time = date('Y-m-d H:i:s');

$sql = "insert into userbankcard (bank, cardid, bankface, bankback, uid, status, ctime) VALUES ('$bank', '$cardid', '$db_front', '$db_back', $uid, 1, '$cur_time')";
if($db->query($sql)){
	exit(json_encode(array('isSuccess' => 1)));
}else{
	exit(json_encode(array('isSuccess' => 0)));
}