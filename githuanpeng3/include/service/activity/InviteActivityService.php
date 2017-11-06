<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年9月6日
 * Time: 下午2:25:25
 * Desc: 邀请活动服务层
 */
namespace service\activity;

use lib\activity\InviteActivityLib;
use system\DbHelper;
use service\user\UserDataService;
use lib\pack\Backpack;

class InviteActivityService
{
    public $libinvite;
    public function __construct(){
        $this->libinvite = new InviteActivityLib();
    }
    //检索当前用户是否已经生成邀请链接
    public function checkInviteLink(int $uid){
        $data = $this->libinvite->_checkInviteLink($uid);
        return !empty($data) ? $data : []; 
    }
    //生成邀请码
    private function getInviteCode(int $uid){
        return md5($uid.$_SERVER['REQUEST_TIME'].rand());
    }
    //获取邀请活动 送礼礼包666  的礼包id
    private function getInvitePackage(){
        //调用 liupeng@6.cn的背包层 
        return Backpack::GOODSID_SIX_SIX_SIX;
    }
    //生成邀请链接信息
    public function makeInviteCode(int $uid){
        $inviteCode = $this->getInviteCode($uid);
        $nums = InviteActivityConfig::INVITE_TIMES;
        $channer_id = 0;//渠道id预留
        $package_id = $this->getInvitePackage();
        $beans = InviteActivityConfig::BEANS_NUMS;
        $data = $this->libinvite->_makeInviteCode($uid,$inviteCode,$nums,$channer_id,$package_id,$beans);
        return !empty($data) ? true : false; 
    }
    //检查邀请码是否正确
    public function checkInviteCode($code){
        return $this->libinvite->_checkInviteCode($code);
    }
    //检查是否有领取剩余
    public function isReceiveMore($code){
        return $this->libinvite->_isReceiveMore($code);
    }
    //检查 该手机是否已经领取过
    public function checkIsReceive($phone){
        return $this->libinvite->_checkIsReceive($phone);
    }
    //领取
    public function getReward($phone,$code){
        return $this->libinvite->_getReward($phone,$code);
    }
    //查手机号是否已注册
    public function isRegMobile($phone){
        $userDataService = new UserDataService();
        $userDataService->setPhone($phone);
        return $userDataService->isExist();
    }
    //通过uid 获取礼物信息、以及邀请人信息
    public function inviteRewardInfo(int $phone){
        //新用户领取奖励记录
        $inviteData = $this->libinvite->_inviteRewardInfo($phone);
        if(!empty($inviteData)){
            //邀请人uid
            $fromUid = $this->libinvite->inviteFrom($inviteData[0]['invite_code']);
            if(!empty($fromUid))
                return ['fromUid'=>$fromUid[0]['uid'],'reward'=>$inviteData[0]]; 
        }
        return false;
    }
    //回写uid
    public function callbackInsertUid(int $phone,int $uid){
        $res = $this->libinvite->_callbackInsertUid($phone,$uid);
        return !empty($res) ? true : false;
    }
    //邀请活动信息
    public function inviteActivityInfo(int $activity_id){
        $res = $this->libinvite->_inviteActivityInfo($activity_id);
        return !empty($res) ? $res : false;
    }
    //点击邀请活动 预生成领取记录
    public function preCreateInviteReceive($inviteCode){ 
        $nums = InviteActivityConfig::INVITE_TIMES; 
        $package_id = $this->getInvitePackage();
        $beans = InviteActivityConfig::BEANS_NUMS;
        return $this->libinvite->_preCreateInviteReceive($package_id, $beans, $inviteCode,$nums);
    }
}

