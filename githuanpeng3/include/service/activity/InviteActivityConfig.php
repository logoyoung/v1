<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年9月5日
 * Time: 下午2:48:33
 * Desc: 邀请奖励活动 配置
 */
namespace service\activity;

class InviteActivityConfig
{  
    const VALIDITY_TIME   = 7;  //礼包有效期（通过邀请注册的新人 从注册时间开始 礼包有效期7天后过期）
    const BEANS_NUMS      = 100;//100欢朋豆
    const INVITE_TIMES    = 10; //一个邀请链接 最多可注册 10个 *新* 用户 
    
    //邀请返回码及其信息
    const INVITE_RETURN_CODE_01 = 1000; //生成邀请链接成功
    const INVITE_RETURN_CODE_02 = 1001; //邀请奖励活动已关闭（暂不能邀请好友）
    const INVITE_RETURN_CODE_03 = 1002; //生成邀请链接失败
    static public $inviteReturnDesc = [
        self::INVITE_RETURN_CODE_01 => '生成邀请链接成功',
        self::INVITE_RETURN_CODE_02 => '暂不能邀请好友',
        self::INVITE_RETURN_CODE_03 => '生成邀请链接失败'
    ];
    //领取返回码及其信息
    const RECEIVE_RETURN_CODE_01 = 2000; //领取成功
    const RECEIVE_RETURN_CODE_02 = 2001; //该手机号已领取
    const RECEIVE_RETURN_CODE_03 = 2002; //领取失败
    const RECEIVE_RETURN_CODE_04 = 2003; //已经领取完了
    static public $receiveReturnDesc = [
        self::RECEIVE_RETURN_CODE_01 => '领取成功',
        self::RECEIVE_RETURN_CODE_02 => '该手机号已领取',
        self::RECEIVE_RETURN_CODE_03 => '领取失败',
        self::RECEIVE_RETURN_CODE_04 => '已经领取完了',
    ];
    static public function getHttpHost(){
        return DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain-img']; //图片域名;
    }
    static public function getPageHttpHost(){
        return DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain']; //图片域名;
    }
}
