<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

use service\due\DueTagsService;
use service\due\DueCertService;
use service\due\DueRecommService;
use lib\User;
use lib\due\DueCert;
use service\common\UploadImagesCommon;
use service\user\UserAuthService;
use service\user\UserDataService;

include '../../../include/init.php';
//require(INCLUDE_DIR . 'User.class.php');
require(INCLUDE_DIR . 'lib/User.php');
/**
 * 获取观众信息
 * date 2016-07-11 11:11
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass=isset($_POST['encpass']) ? trim(($_POST['encpass'])) : '';
$luid = isset($_POST['targetUID']) ? (int) ($_POST['targetUID']) : '';

if (empty($luid)) {
    error2(-4013);
}

$userobj = new User($luid);
$userDataService = new UserDataService();
$userDataService->setCaller('api:'.__FILE__);
$userDataService->setUid($luid);
$userDataService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
$userData = $userDataService->getUserInfo();
$info['uid']   = $userData['uid'];
$info['head']  = $userData['pic'];
$info['nick']  = $userData['nick'] ? $userData['nick'] : '';
$info['level'] = $userData['level'];
$info['anchorLevel'] =  getAnchorLevel($luid, $db);

if ($uid && $encpass) {

    $auth = new UserAuthService();
    $auth->setUid($uid);
    $auth->setEnc($encpass);
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

    $isFollow =isOneFollowOne($uid, $luid, $db);
    if($isFollow){
       $info['isFollow']="1";
    }else{
        $info['isFollow']="0";
    }
} else {
    $info['isFollow'] = "0";
}
$info['fansCount']=getFansCount($luid, $db);
$isCertify = $userobj->getCertifyInfo();
if ($isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1) {
    $info['isCertify'] = "1";
} else {
    $info['isCertify'] = "0";
}
/**
 * 陪玩信息拉去
 * ---------
 *
 * @author yalongSun <yalong2017@6.cn>
 */
if (isset($_POST['type']) && $_POST['type'] == 1) {
    $targetUid = intval($_POST['targetUID']);
    $skillObj = new DueCertService();
    $skillLoObj = new DueCert($targetUid);
    $skillObj->setUid($targetUid);
    $certinfo = $skillLoObj->getAllCert();
    $skillinfo = $skillObj->getSkillByUid();
    foreach($skillinfo as $k=>$v){ //剔除 关闭了的技能项
        if($v['switch']!=1) unset($skillinfo[$k]);
    }
    if (! empty($skillinfo)) {
        $skillIds = array_column($skillinfo, "skillId");
        $orderTotals = $skillObj->getOrderTotalBySkillID($skillIds);

        $recemObj = new DueRecommService();
        $skillinfo = $recemObj->getSkillInfos($skillinfo);
        foreach ($skillinfo as $k => $vo) {
            foreach ($certinfo as $v) {
                if ($v['certId'] == $vo['cert_id']) {
                    $skillinfo[$k]['pic'] = UploadImagesCommon::getImageDomainUrl().mb_substr($v['pic_urls'], 0, stripos($v['pic_urls'], ","));
                    unset($skillinfo[$k]['total_score']);
                    unset($skillinfo[$k]['comment_num']);
                }
            }
        }
        foreach ($skillinfo as $k=>$v){
            foreach ($orderTotals as $vo){
                if($v['skillId'] == $vo['skill_id'])
                    $skillinfo[$k]['order_total'] = $vo['order_total'];
            }
        }
    } else
        $skillinfo = [];

    $info['skill_list'] = $skillinfo;

    $tagObj = new DueTagsService();
    $redis = new RedisHelp();
    $data = $tagObj->getUserTagsByUid($targetUid);
    //检验 redis中是否已经生成tag,没有生成则去库里拉去最近一条
//     if(empty($data)){
//         $data = $tagObj->getLastSqlByUid($targetUid);
//     }
    $userTags = $tagObj->getTagsByids($data);
    if(!empty($userTags)){
        foreach ($data as $vo){
            foreach ($userTags as $v){
                if($vo == $v['id']){
                    $arrTags[] = $v;
                }
            }
        }
    }else $arrTags = []; 
    $info['tags'] = !empty($arrTags) ? $arrTags : [];

    //被点击 用户是否是主播的房管
    $is_room_manager = $userobj->isRoomManager(intval($_POST['anchorUid']))==true ? 1 : 0;
    if($uid && $encpass){
        // 是否被禁言
        $userobj->resetUid($uid);
        $info['is_silenced'] = $userobj->isSilenced($targetUid);

        $info['can_silence'] = 1 ; //默认可以禁言被点者
        //是否是房管
        if($uid == intval($_POST['anchorUid']))
        {
            $info['is_room_manager'] = 1;
            if($is_room_manager == 1 && $info['is_room_manager'] ==1)
                $info['can_silence'] = 0; //被点者也为该主播房管
        }else
        {
            $info['is_room_manager'] = $userobj->isRoomManager(intval($_POST['anchorUid']))==true ? 1 : 0;
            //除非 两个都为 主播的房管
            if($is_room_manager == 1 && $info['is_room_manager'] ==1)
                $info['can_silence'] = 0; //被点者也为该主播房管
            if($info['is_room_manager'] == 0)
                $info['can_silence'] = 0; //非主播 房管用户
        }
        //被点者 就是主播的话 其他均不可以禁言
        if($targetUid == intval($_POST['anchorUid'])){
            $info['can_silence'] = 0;
        }
    }
}
//----------
render_json($info);




