<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/12
 * Time: 上午11:54
 */
exit;
include "../../includeAdmin/init.php";
include INCLUDE_DIR . "live/Review.class.php";
$db = new DBHelperi_admin();

mylog("--setTaskStatus--" . json_encode($_GET), LOGFN_SEND_MSG_ERR);

$request = array('liveid' => 'int', 'livestatus' => 'int', 'tm' => 'int', 'sign' => "str");
foreach ($request as $key => $val) {
    if ($val == 'int')
        $$key = (int)$_GET[$key] ? (int)$_GET[$key] : 0;
    else
        $$key = trim($_GET[$key]) ? trim($_GET[$key]) : '';

    if (!$$key) {
        mylog("--setTaskStatus--" . $key . " is empty", LOGFN_SEND_MSG_ERR);
        error(-1007);
    }
}


//verifySign($_GET, $s);

//设置直播状态
$uid = hp\live\Review::taskHanlder($liveid, $db);
$review = new \hp\live\Review($uid, $db);
//开始直播
if ($livestatus == 1) {
    $review->setTask($liveid);
} elseif ($livestatus == 2) {
    hp\live\Review::setLiveStatus($liveid, $review::live_stop, $db);
    $review->succEnd($liveid);
}