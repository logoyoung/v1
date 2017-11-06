<?php
/**
 * Created by NetBeans.
 * User: yalongSun <yalong_2017@6.cn>
 * Desc: 分享活动配置库
 * 描述：  当上一个活动时 如果是 特殊分享的活动需进行如下配置
 * 1、告知admin_information表中id（活动id）作为活动 键值；
 * 2、告知分享链接地址；
 * 3、确定是否要拼接 相应业务的参数 is_param ；
 * 4、如果 3 成立 则需要 相关活动业务的同学提供 返回 分享链接拼接的参数（提供方法）
 */

namespace service\activity;
use service\activity\InviteActivityConfig;
use service\activity\InviteActivityService;
class ShareActivityConfig {
//    pre、dev 测试活动id
//    const PAY_ACTIVITY_ID     = 141;     //充值活动   
//    const INVITE_ACTIVITY_ID  = 137;     //邀请活动
//    const VOTE_ACTIVITY_ID    = 145;     //投票活动
    
//    2017-09-25 上线 www活动id
    const PAY_ACTIVITY_ID     = 141;    //欢朋直播-欢朋首充有礼        
    const INVITE_ACTIVITY_ID  = 137;    //欢朋直播-邀请好友奖励领不停
    const VOTE_ACTIVITY_ID    = 145;    //欢朋直播-最强英雄争霸赛
    
    static public $shareActivity = [
        self::INVITE_ACTIVITY_ID => [ //  88 特殊分享活动 id 与 admin_information 表中id 对应
            'activity' => '邀请好友奖励领不停', //活动描述 用户 配置与库对应 不返客户端

            'url'      => '/mobile/invite/index.html', //分享链接地址

            'is_param' => 1,  //是否在分享链接地址拼接 动态参数 如：自定义code、渠道id等
            'get_param_function' =>'getInviteActivity' //获取 is_param的方法名称
        ],
    ];
    public $inviteService = null;
    public function __construct() {
        if(is_null($this->inviteService))
            $this->inviteService = new InviteActivityService();
    }
    /*
     * 以下获取参数方法  与上边配置的 获取分享链接拼接参数 get_param_function 值对应
     */
    /**
     *   邀请好友奖励领不停  id：88
     *   返回：分享链接拼接的 邀请code 参数
     */
    public function getInviteActivity($uid){
        $inviteCode = $this->inviteService->checkInviteLink($uid);
        if(empty($inviteCode)){
            //生成邀请链接
            if($this->inviteService->makeInviteCode($uid)){
                $inviteCode = $this->inviteService->checkInviteLink($uid);
            }else {
                render_json(InviteActivityConfig::$inviteReturnDesc[InviteActivityConfig::INVITE_RETURN_CODE_03],InviteActivityConfig::INVITE_RETURN_CODE_03);
            } 
        }
        return $inviteCode[0]; 
    }
    static public function getHttpHost(){
        return DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain-img']; //图片域名;
    }
    static public function getPageHttpHost(){
        return DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain']; //图片域名;
    }
}
