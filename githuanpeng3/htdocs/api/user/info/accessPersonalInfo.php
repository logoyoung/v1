<?php


include '../../../../include/init.php';
require(INCLUDE_DIR . 'User.class.php');
require_once INCLUDE_DIR.'Anchor.class.php';
use service\due\DueCertService;
use service\due\DueOrderService;
use service\due\DueCouponService;
use service\user\UserDataService;
use service\user\UserAuthService;
use service\user\UserCenterCountService;
use service\anchor\AnchorGetDataService;
use service\room\RoomManagerService;
use service\activity\ShareActivityConfig;
use service\pack\PackEvnentService;

$db = new DBHelperi_huanpeng();

/**
 * 用户头像状态
 * @param int $uid
 * @param obj $db
 * @return string
 */
function userPicStatus($uid, $db)
{
    $res = $db->field('status')->where("uid=$uid")->select('admin_user_pic');
    if (empty($res)) {
        $status = '1';
    } else {
        if ($res[0]['status'] == 0|| $res[0]['status'] == 3) {
            $status = '0'; //审核中
        }
        if ($res[0]['status'] == 1 ) {
            $status = '1'; //已通过
        }
        if ($res[0]['status'] == 2 || $res[0]['status'] == 4) {
            $status = '2'; //未通过
        }
    }
    return $status;
}
function nickStatus($uid, $db)
{
    $res = $db->field('status')->where("uid=$uid")->select('admin_user_nick');
    if (empty($res)) {
        $status = '1';
    } else {
        if ($res[0]['status'] == 0 || $res[0]['status'] == 3) {
            $status = '0'; //审核中
        }
        if ($res[0]['status'] == 1) {
            $status = '1'; //已通过
        }
        if ($res[0]['status'] == 2 || $res[0]['status'] == 4) {
            $status = '2'; //未通过
        }
    }
    return $status;
}

function  getProvince($pid,$db){
    if(empty($pid)){
        return false;
    }
    $res=$db->field('id,name')->where("id=$pid")->select('province');
    if(false !==$res){
        return $res[0]['name'];
    }else{
        return false;
    }
}
function  getCity($pid,$cid,$db){
    if(empty($pid) || empty($cid)){
        return false;
    }
    $res=$db->field('id,name')->where("id=$cid  and pid=$pid")->select('city');
    if(false !==$res){
        return $res[0]['name'];
    }else{
        return false;
    }
}

function getUserModifyNickIsFree($uid,$db){
	$sql = "select isfree from userstatic where uid=$uid";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	return (int)$row['isfree'];
}

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (empty($uid) || empty($enc))
    error2(-4013);

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($enc);
//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');

    error2(-4067,2);
}

$userService = new UserDataService();
$userService->setUid($uid);
$userService->setCaller('api:'.__FILE__);
$userService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
$userData = $userService->getUserInfo();

$user['uid']      = $userData['uid'];
$user['nick']     = $userData['nick'];
$user['pic']      = $userData['pic'];
$user['level']    = $userData['level'];
$user['hpbean']   = $userData['hpbean'];
$user['hpcoin']   = $userData['hpcoin'];
$user['readsign'] = $userData['readsign'];
$user['integral'] = (int) $userData['integral'];
$user['modifyNickFree'] = $userData['isfree'];
$user['phonestatus']    = $userData['phone'] ? 1 : 0 ;
$user['levelIntegral']  = $userData['level_to_integral'];
$user['unreadMsg']      = $userData['readsign'];
$anchorService = new AnchorGetDataService();
$anchorService->setUid($uid);
$anchorData    = $anchorService->getAnchorData();
$user['anchorLevel']    = isset($anchorData['level']) ? (int) $anchorData['level'] : 1;
unset($anchorData);
$user['nickStatus']     = nickStatus($uid, $db);
$user['picCheckStat']   = userPicStatus($uid, $db);
$user['picCheckUrl']    = WEB_ROOT_URL . "/api/user/info/getUserHead.php?" . http_build_query(array('time' => time(), 'uid' => $uid, 'enc' => $enc));//"time=" . time() ."";

$user['head'] = $userData['pic'];
$user['uid']  = "$uid";

$lastInterval = get_user_integral_by_level($user['level'] -1);

$user['integral'] = $user['integral'] - $lastInterval;
$user['levelIntegral'] = $user['levelIntegral'] - $lastInterval;
$user['gapIntegral'] = ceil($user['levelIntegral'] - $user['integral']);
$roomManager = new RoomManagerService();
$roomManager->setUid($uid);
$roomid      = $roomManager->getRoomidByUid();
if($roomid) {
	$user['roomUrl']= WEB_ROOT_URL.$roomid;
}

unset($user['pic']);
unset($user['readsign']);
$fansCount = getFansCount($uid, $db);

if(!empty($userData['city'])){
    $user['isbindAddr'] = 1;//地址已填写
    $province = get_address_province_by_pid($userData['province']);
    $city     = get_address_city_by_cid_pid($userData['city'],$userData['province']);
    if((false !== $province) && (false !== $city)){
        $user['pid'] = $userData['province'];
        $user['cid'] = $userData['city'];
        if(in_array($user['pid'],array(1,2,3,4))){
            $user['addr'] = $city.$userData['address'];
        }else{
            $user['addr'] = $province.$city.$userData['address'];
        }
    }else{
        $user['pid'] = 0;
        $user['cid'] = 0;
        $user['addr'] = $userData['address'];
    }
}else{
    $user['isbindAddr'] = 0;//未绑定
    $user['pid'] = 0;
    $user['cid'] = 0;
    $user['addr'] = '';
}
$user['fansCount'] = $fansCount ? "$fansCount" : "0";
$user['isAnchor']  = !RN_MODEL  ? ($auth->checkIsAnchor() ? 1 : 0 ) : ($auth->checkAnchorCertStatus() ? 1 : 0 );

//$anchorHelp = new AnchorHelp( $uid, $db );
//if( $anchorHelp->isAnchor() )
//{
//	$user['isAnchor'] = '1';
//}
//else
//{
//	$user['isAnchor'] = '0';
//}
/**
 * 获取用户是否认证过 陪玩资质
 */

//陪玩资质 (并开启技能)
$user['is_cert'] = $auth->checkIsDueAnchor() ? 1 : 0;

if ($user['isAnchor']) {
    $orderObj = new DueOrderService();
    $user['todoOrderNum'] = $orderObj->getOrderNumByLuid($uid);
} else {
    $user['todoOrderNum'] = 0;
}
## 是否有新的优惠券
$couponNum =  DueCouponService::getNewCouponTagNum($user['uid']) ;
$user['isShowCouponTag'] = $couponNum > 0 ? 1 :0;
$user['couponTagContent'] = '';
## 是否有首冲标识
$now = date("Y-m-d");
$packevent = new PackEvnentService();
$isShowPayTag = intval($packevent->checkActivityTime(service\activity\ShareActivityConfig::PAY_ACTIVITY_ID));

$user['isShowPayTag'] = $isShowPayTag;
$user['payTagContent'] = '首充';
## 是否展示 邀请的button
$user['isShowInvitatoryButton'] = $isShowPayTag;
$user['invitatoryButtonId'] = ShareActivityConfig::INVITE_ACTIVITY_ID; 

## backpack
$backPackNum = UserCenterCountService::getValue($user['uid'], UserCenterCountService::HASH_TABLE_FIELD_BACKPACK_NUM);
$user['isShowBackpackTag'] = $backPackNum > 0 ? 1 :0;
$user['backpackTagContent'] = '';

render_json($user);