<?php
namespace service\event;
use service\common\AbstractService;

abstract class EventAbstract extends  AbstractService
{
    //用户注册
    const ACTION_USER_REGISTER       = 101;

    //用户资料修改
    const ACTION_USER_INFO_UPDATE    = 102;

    //用户资金变动
    const ACTION_USER_MONEY_UPDATE   = 103;

    //用户认证信息变更
    const ACTION_USER_CERT_UPDATE    = 104;

    //调整封禁用户
    const ACTION_USER_UPATE_DISABLE_LOGIN  = 105;

    //用户添加禁言
    const ACTION_USER_ADD_SILENCE    = 107;

    //用户去除禁言
    const ACTION_USER_REMOVE_SILENCE = 108;

    //用户登陆成功
    const ACTION_USER_LOGIN          = 109;

    //用户头相变更
    const ACTION_USER_UPDATE_HEAD    = 110;

    //用户消息更新
    const ACTION_USER_MSG_UPDATE     = 111;


    //用户重新构造缓存
    const ACTION_USER_RESET_CACHE    = 188;


    //开始直播
    const ACTION_LIVE_START          = 201;

    //结束直播
    const ACTION_LIVE_STOP           = 202;

    //更新直播信息
    const ACTION_UPDATE_LIVE_INFO    = 203;

    //封禁主播 直播
    const ACTION_ADD_DISABLE_LIVE    = 301;

    //解禁主播 直播
    const ACTION_DEL_DISABLE_LIVE    = 302;

    //主播审核通过
    const ACTION_ANCHOR_CHECK_SUCC   = 330;

    //重置主播信息缓存
    const ACTION_ANCHOR_RESET_CACHE  = 331;

    //主播资料修改
    const ACTION_ANCHOR_DATA_UPDATE  = 332;
    const ACTION_ROOMID_DATA_UPDATE  = 333;
    //添加房管
    const ACTION_ADD_ROOM_MANAGER    = 334;
    //取消房管
    const ACTION_DELETE_ROOM_MANAGER = 335;
    //重中房管缓存
    const ACTION_RESET_ROOM_MANAGER  = 336;

    //芝麻认证通过
    const ACTION_ZHIMA_CERT_SUCC     = 401;

    //礼品信息变更
    const ACTION_GITF_UPDATE         = 501;

    //直播间活动信息变更
    const ACTION_ROOM_PROMOTION_UPDATE = 503;

    abstract public function trigger($action,$param);
}