<?php
/**
 * 身份证上传
 */
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

$IMG_TYPE = array('jpg', 'png', 'jpeg', 'gif');

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

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

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if (!$uid || !$enc) {
    error2(-4013);
}

if (!isset($_POST['type']) || ($_POST['type'] != 'front' && $_POST['type'] != 'back' && $_POST['type'] != 'handheld')) exit('-2');
$checklogin = checkUserState($uid, $enc, $db);
if (true !== $checklogin) {
    error2(-4067, 2);
}
if (!$_FILES) {
    error2(-4018, 2);
}

$imgtype = explode('/', $_FILES['file']['type']);
$e = $_FILES['file']['error'];
$defaultSize = $_FILES['file']['size'];
if ((int)$defaultSize > (int)(1024 * 1024 * MAX_UPLOAD_SIZE)) {
    error2(-4015, 2);
}
switch ($e) {
    case 0 :
        break;
    case 1 :
        error2(-4015, 2);
        break;
    case 2 :
        error2(-4033, 2);
        break;
    case 3 :
        error2(-4016, 2);
        break;
    case 4 :
        error2(-4017, 2);
        break;
    default:
        error2(-4018, 2);
}

if ($imgtype[0] != 'image') error2('-1021');

if (!in_array($imgtype[1], $IMG_TYPE)) error2('-1025');

if (!is_uploaded_file($_FILES['file']['tmp_name'])) error2('-1022');

$tmpdir = $conf['img-dir'] . '/tempImg';
if (!is_dir($tmpdir))
    if (!createDir($conf['img-dir'] . '/', 'tempImg'))
        error2('-1023', 2);

$fileName = md5($uid . $enc . $_POST['type'] . "realname") . '.png';

$dest = $tmpdir . "/" . $fileName;
if (!copy($_FILES['file']['tmp_name'], $dest))
    error2('-1024', 2);

$imgUrl = DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . 'tempImg/' . $fileName . '?t=' . time();
succ(array('img' => $imgUrl));