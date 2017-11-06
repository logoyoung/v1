<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年8月30日
 * Time: 下午12:06:59
 * Desc: 投票、报名等配置信息
 */
namespace service\activity;
use service\activity\ShareActivityConfig;

class VoteActivityConfig
{
    // 投票活动的游戏分类
    const GAME_01 = 190;                            //王者荣耀  
    
    /* 活动
     * [
     *      游戏id => [[活动1],[活动2]…],
     *      游戏id => [[活动1],[活动2]…],
     * ]
     * 注：activity_id 递增添加  不可重复
     */
//    static public $activityGule = [
//        self::GAME_01 => [                           //游戏分类下的多个活动 新活动往下罗
//            [
//                'activity_id' => self::ACTIVITY_ID,          //活动ID
//                'game_id' => self::GAME_01,                  //活动ID
//                'activity'   => '王者荣耀最强英雄投票',   //活动标题 
//                'desc'       => '',                  //活动描述
//                'status'     => 1 ,                  //活动状态 0 该活动前端不展示；1活动展示 *只是用来控制前端*
//                'stime'      => '2017-07-30',        //活动开始时间(即 投票、报名开始时间)
//                'etime'      => '2017-10-20'         //活动结束时间(即 投票、报名结束时间) 
//            ],  
////             [
////                 'activity_id' => 2,                  //活动ID
////                 'game_id' => self::GAME_01,                  //活动ID
////                 'activity'   => '王者荣耀射手英雄投票',   //活动标题 
////                 'desc'       => '',                  //活动描述
////                 'status'     => 0,                   //活动状态 0 该活动前端不展示；1活动展示 *只是用来控制前端*
////                 'stime'      => '2017-08-30',        //活动开始时间(即 投票、报名开始时间)
////                 'etime'      => '2017-09-20'         //活动结束时间(即 投票、报名结束时间)
////             ],
//        ],
//    ];
    /* 活动人物
     * [
     *      活动id => [[英雄1],[英雄2]…],
     *      活动id => [[英雄1],[英雄2]…],
     * ]
     * 注：hero_id 递增添加  不可重复
     */
    static public $heroGule = [
        ShareActivityConfig::VOTE_ACTIVITY_ID => [ //投票活动  admin_information 表中id 
            [
                'hero_id' => 1,      //英雄id
                'hero'   => '花木兰',  //人物昵称
                'img'    => '/static/img/vote_activity/activity_001/mulan.png',      //人物图片路径
                'desc'   => '水晶猎龙者',
                'bgImg'    => '',     //人物背景图片路径
            ],
            [
                'hero_id' => 2,      //英雄id
                'hero'   => '貂蝉', //人物昵称
                'img'    => '/static/img/vote_activity/activity_001/diaochan.png',     //人物图片路径
                'desc'   => '逐梦之音',
                'bgImg'    => '',     //人物背景图片路径
            ],
            [
                'hero_id' => 3,      //英雄id
                'hero'   => '露娜',  //人物昵称
                'img'    => '/static/img/vote_activity/activity_001/luna.png',     //人物图片路径
                'desc'   => '绯红之刃',
                'bgImg'    => '',     //人物背景图片路径
            ],
        ]
    ]; 
    //
    static public function imgHostUrl(){ 
        return DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain-img'];
    }
}
 
