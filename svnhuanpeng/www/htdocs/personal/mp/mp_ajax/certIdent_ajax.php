<?php
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

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
function checkTheDate($date){
	$dateArray = explode('-', $date);
	$year = $dateArray[0];
	$month = $dateArray[1];
	$day = $dateArray[2];
	if(!$year || !$month || !$day){
		return false;
	}

	if(!preg_match("/^1[89]|20\d{2}$/",$year)){
		return false;
	}
	if(!preg_match("/^0[1-9]|1[0-2]$/", $month)){
		return false;
	}

	$reg = '';
	if(preg_match("/^0[13578]|1[02]$/", $month)){
		$reg = "/^0[1-9]|[12]\d{1}|3[01]/";
	}else{
		if($month == '02'){
			if((($year % 4) == 0) && (($year % 400) != 0 || ($year % 400) == 0)){
				$reg = "/^0[1-9]|1\d{1}|2[0-9]$/";
			}else{
				$reg = "/^0[1-9]|1\d{1}|2[0-8]/";
			}
		}else{
			$reg = "/^0[1-9]|[12]\d{1}|30/";
		}
	}
	if(preg_match($reg, $day))
		return true;
	else
		return false;
}

function identCodeValid($identCode){
	$pass = true;
	$city = array(
		11,12,13,14,15,
		21,22,23,
		31,32,33,34,35,36,37,
		41,42,43,44,45,46,
		50,51,52,53,54,
		61,62,63,64,65,
		71,81,82,91
	);
	$identCodeReg = "/^[1-9]\d{5}((18|19|20)\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dx]$/i";
	if(!$identCode || !preg_match($identCodeReg, $identCode)){
		$pass = -4;
	}elseif(!in_array((int)substr($identCode, 0, 2), $city)){
		$pass = -5;
	}else{
//		$identCode = explode('',$identCode);
		$factor = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
		$parity = array( 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 );

		$sum = $ai = $wi = 0;

		for($i = 0; $i < 17; $i++ ){
			$ai = $identCode[$i];
			$wi = $factor[$i];
			$sum += $ai * $wi;
		}
		if($parity[$sum % 11] != $identCode[17]){
			$pass = -6;
		}
	}

	return $pass;
}
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if(!$uid || !$enc)  exit(-4009);
$code = checkUserState($uid, $enc, $db);
if($code !== true) error($code);

$identStatus = get_userIdentCertifyStatus($uid, $db);
if($identStatus['identstatus'] == RN_WAIT){
    error(-4045);
}
if($identStatus == RN_PASS){
    error(-4046);
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$identID = isset($_POST['identID']) ? trim($_POST['identID']) : '';
$outTime = isset($_POST['outTime']) ? trim($_POST['outTime']) : '';

if(!$name)
	error(-4036);

if(true !== identCodeValid($identID))
	error(-4037);

if(!checkTheDate($outTime))
	error(-4038);

if(strtotime($outTime) < time())
	error(-4039);

$front_fileName = md5($uid . $enc . "front" . "realname") . '.png';
$back_fileName = md5($uid . $enc . "back" . "realname") . ".png";
$handheld_fileName = md5($uid . $enc . "handheld" . "realname") . '.png';

$tempDir = $conf['img-dir'] . "/tempImg/";

if(!is_file($tempDir . $front_fileName) || !is_file($tempDir . $back_fileName) || !is_file($tempDir . $handheld_fileName)){
	error(-4040);
}

$saveDir = $uid . "/realname";
if(!is_dir($conf['img-dir'] . $saveDir)){
	if(!createDir($conf['img-dir'] . "/", $saveDir))
		error('-1023');
}

$dirArray = array(
	'db_front' => $front_fileName,
	'db_back' => $back_fileName,
	'db_handheld' => $handheld_fileName
);
$pre = $conf['img-dir'] . '/' . $saveDir . "/" ;
foreach ($dirArray as $key => $value) {
	$oldDir = $tempDir . $value;
	$newDir = $pre . $value;
	if(copy($oldDir, $newDir)){
		$$key = $db->realEscapeString("/" . $saveDir . "/" . $value);
    }else{
		error('-1024');
	}
}

$cur_time = date("Y-m-d H:i:s");
$sql = "insert into userrealname (`name`,papersid,papersetime,face,back,uid,ctime,status,handheldPhoto) values(
    							'$name','$identID','$outTime','$db_front', '$db_back', $uid,'$cur_time',1,'$db_handheld')";

if($db->query($sql)){

    $id = $db->insertID;
    $sql = "insert into admin_certRealName(certifyid) value($id) on duplicate key update certifyid = $id";
    $db->query($sql);

    exit(json_encode(array('isSuccess' => 1)));
}
else
	error(-4041);