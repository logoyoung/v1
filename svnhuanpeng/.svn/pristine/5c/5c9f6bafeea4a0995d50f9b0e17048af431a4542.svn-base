<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/12
 * Time: 上午11:55
 */
exit;
include '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
//msgtype : 1:notice, 2:stoplive, 3:killuser
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();
$url = DOMAIN_PROTOCOL . "dev.huanpeng.com/admin2/api/live/liveStop.php";

function  getLiveId_By_Uid($anchorid,$db){
 $res=$db->field("liveid")->where("uid=$anchorid and status=".LIVE)->limit(1)->select("live");
    if(false !==$res){
        if(!empty($res)){
            return $res[0]['liveid'];
        }else{
            return  0;
        }
    }else{
        return false;
    }
}


$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$anchorid= isset($_POST['anchorid']) ? (int)$_POST['anchorid'] : ''; //主播id
$reason = isset($_POST['reason']) ? (int)$_POST['reason'] : ''; //原因
$msgType = isset($_POST['msgType']) ? (int)$_POST['msgType'] : ''; //原因
if (empty($uid) || empty($encpass) || empty($type) || empty($anchorid) ||empty($reason) || empty($msgType)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$liveid= getLiveId_By_Uid($anchorid,$db);
if($liveid){
    $data=array(
        'liveid'=>$liveid,
        'reason'=>$reason,
        'msgType'=>$msgType,
        'uid'=>$uid,
        'encpass'=>$encpass
    );
    $res=CurlPost($url,$data);
    exit($res);
}else{
    succ();
}






