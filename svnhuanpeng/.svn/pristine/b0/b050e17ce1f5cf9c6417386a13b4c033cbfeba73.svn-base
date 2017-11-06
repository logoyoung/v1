<?php
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * 实名认证接口
 */
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

    foreach ($arr as $v) {
        $temp .= $v . '/';
        $dir = $preDir . $temp;
        if (!is_dir($dir))
            $result = mkdir($dir);
    }

    return $result;
}

function checkTheDate($date)
{
    $dateArray = explode('-', $date);
    $year = $dateArray[0];
    $month = $dateArray[1];
    $day = $dateArray[2];
    if (!$year || !$month || !$day) {
        return false;
    }

    if (!preg_match("/^1[89]|20\d{2}$/", $year)) {
        return false;
    }
    if (!preg_match("/^0[1-9]|1[0-2]$/", $month)) {
        return false;
    }

    $reg = '';
    if (preg_match("/^0[13578]|1[02]$/", $month)) {
        $reg = "/^0[1-9]|[12]\d{1}|3[01]/";
    } else {
        if ($month == '02') {
            if ((($year % 4) == 0) && (($year % 400) != 0 || ($year % 400) == 0)) {
                $reg = "/^0[1-9]|1\d{1}|2[0-9]$/";
            } else {
                $reg = "/^0[1-9]|1\d{1}|2[0-8]/";
            }
        } else {
            $reg = "/^0[1-9]|[12]\d{1}|30/";
        }
    }
    if (preg_match($reg, $day))
        return true;
    else
        return false;
}

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if (!$uid || !$enc) {
    error2(-4013);
}
$code = checkUserState($uid, $enc, $db);
if ($code !== true) error2(-4067, 2);

$isBind = checkUserIsBindMobile($uid, $db);
if (false === $isBind) {
    error2(-5026, 2);
}
$identStatus = get_userIdentCertifyStatus($uid, $db);
if ($identStatus['identstatus'] == RN_WAIT) {
    error2(-4045, 2);
}
if ($identStatus == RN_PASS) {
    error2(-4046, 2);
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$identID = isset($_POST['identID']) ? trim($_POST['identID']) : '';
$outTime = isset($_POST['outTime']) ? trim($_POST['outTime']) : '';
$paperstype = isset($_POST['paperstype']) ? trim($_POST['paperstype']) : 0; //0身份证 1 军官证 2港澳台同胞证件
if (!$name)
    error2(-4036, 2);

if (true !== identCodeValid($identID))
    error2(-4037, 2);

if (!checkTheDate($outTime))
    error2(-4038, 2);

if (!in_array($paperstype, array(0, 1, 2))) {
    error2(-4094, 2);
}
$checkIdent = checkIdentID($identID, $paperstype, $db);//检测证件是否被使用
if (false === $checkIdent) {
    error2(-5017, 2);
}
if ($checkIdent) {
    error2(-4095, 2);
}
//if(strtotime($outTime) < time())
//	error2(-4039,2);


$front_fileName = md5($uid . $enc . "front" . "realname") . '.png';
$back_fileName = md5($uid . $enc . "back" . "realname") . ".png";
$handheld_fileName = md5($uid . $enc . "handheld" . "realname") . '.png';

$tempDir = $conf['img-dir'] . "/tempImg/";

if (!is_file($tempDir . $front_fileName) || !is_file($tempDir . $back_fileName) || !is_file($tempDir . $handheld_fileName)) {
    error2(-4040, 2);
}

$saveDir = $uid . "/realname";
if (!is_dir($conf['img-dir'] . $saveDir)) {
    if (!createDir($conf['img-dir'] . "/", $saveDir))
        error2('-1023', 2);
}

$dirArray = array(
    'db_front' => $front_fileName,
    'db_back' => $back_fileName,
    'db_handheld' => $handheld_fileName
);
$pre = $conf['img-dir'] . '/' . $saveDir . "/";
foreach ($dirArray as $key => $value) {
    $oldDir = $tempDir . $value;
    $newDir = $pre . $value;
    if (copy($oldDir, $newDir)) {
        $$key = $db->realEscapeString("/" . $saveDir . "/" . $value);
    } else {
        error2('-1024', 2);
    }
}

$cur_time = date("Y-m-d H:i:s");
$sql = "insert into userrealname (`name`,papersid,papersetime,face,back,uid,ctime,status,handheldPhoto) values(
    							'$name','$identID','$outTime','$db_front', '$db_back', $uid,'$cur_time',1,'$db_handheld')  
    							on duplicate key update 
    							`name`='$name',
    							papersid='$identID', 
    							papersetime='$outTime', 
    							face='$db_front', 
    							back='$db_back',
    							handheldPhoto='$db_handheld',
    							ctime='$cur_time',
    							status=1";

if ($db->query($sql)) {
    $id = $db->insertID;
	if($id){
		$sql = "insert into admin_certRealName(certifyid) value($id) on duplicate key update certifyid = $id";
		$db->query($sql);
	}
    succ();
} else
    error2(-4041, 2);