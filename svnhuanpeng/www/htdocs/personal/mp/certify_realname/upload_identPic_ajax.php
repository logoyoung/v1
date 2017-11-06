<?php

include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

$IMG_TYPE = array('jpg', 'png', 'jpeg','gif');

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

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

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if(!$uid || !$enc)  exit('-1');

if(!isset($_POST['type']) || ($_POST['type'] != 'front' && $_POST['type'] != 'back' && $_POST['type'] != 'handheld')) exit('-2');

if(true !== checkUserState($uid, $enc, $db))
	exit('-3');

if(!$_FILES) exit(-4);

$imgtype = explode('/', $_FILES['file']['type']);

if( $imgtype[0] != 'image') error('-1021');

if( !in_array($imgtype[1], $IMG_TYPE) ) error('-1025');

if( !is_uploaded_file( $_FILES['file']['tmp_name'] ) ) error('-1022');

$tmpdir = $conf['img-dir'] . '/tempImg';
if(!is_dir($tmpdir))
	if(!createDir($conf['img-dir'].'/', 'tempImg'))
		error('-1023');

$fileName = md5($uid . $enc . $_POST['type'] . "realname") . '.png';

$dest = $tmpdir . "/" . $fileName;
if(!copy($_FILES['file']['tmp_name'], $dest))
	error('-1024');

$imgUrl = "http://" . $conf['domain-img'] . '/' . 'tempImg/' . $fileName;
exit(json_encode(array('isSuccess'=>1, 'img' => $imgUrl)));