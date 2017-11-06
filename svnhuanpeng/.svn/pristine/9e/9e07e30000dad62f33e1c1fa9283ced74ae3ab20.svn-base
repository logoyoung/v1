<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 陪玩 游戏类服务层
 */
namespace service\due;

use lib\due\DueRecGame;
use GuzzleHttp\json_encode;

class DueRecGameService
{

    private $gameObj;

    private $skillObj;

    private $recObj;
    
    // ord 字段升序排
    const GAME_ORDER_01 = 1;
    // 默认陪玩页面多分类 每个分类显示条数
    const GMAE_REC_SIZE = 4;
    // 区别是陪玩多分类调用 还是某个分类调用
    const LIST_CATEGORY_01 = 'gameRecList';
    //每页显示 游戏分类个数
    const GAME_NUMS = 5 ;

    const REC_GAME_ERROR_CODE_01 = - 1000;

    public static $errorMsg = [
        self::REC_GAME_ERROR_CODE_01 => '加载完毕'
    ];

    public function __construct()
    {
        $this->gameObj = new DueRecGame();
        $this->recObj = new DueRecommService();
    }

    /**
     * 获取约玩 后台推荐的游戏分类
     * --------------------
     * 
     * @return array
     */
    public function getGames($page,$size)
    { 
        $this->gameObj->setSkillOrd(self::GAME_ORDER_01);
        return $this->gameObj->getGame($page,$size);
    }

    /**
     * 设置排序规则
     * ---------
     * 
     * @return String
     */
    private function setOrderWay()
    {
        return 'order by avg_score desc';
    }

    /**
     * 按评分平均分倒序排
     * -------------
     * 
     * @param $page 分页            
     * @param $gameId 游戏分类id
     *            注：走默认时为陪玩页面提供多游戏分类技能列表；另通过某游戏id获取相应技能列表
     * @return array
     */
    public function getGamesByComment(int $page, int $gameId = 0,int $size = 5)
    { 
        // 设置排序规则
        $order = $this->setOrderWay(1);
        $this->gameObj->setNewSkillPageCom($page);
        // 默认为陪玩页面 提供多个游戏下的技能列表
        if ($gameId === 0) {
            $games = array();
            //默认一页 查询 5个游戏分类
            $gameids = $this->getGames($page,self::GAME_NUMS);
            foreach ($gameids as $v) {
                $gameInfo = $this->recObj->getGameInfo([
                    $v['game_id']
                ]);
                //楼层展示量
                $number = $gameInfo[$v['game_id']]['number'];
                $this->gameObj->setPageSize($number);
                $info = $this->getGameInfo($v['game_id']);
                if ($info == false)
                    continue;  
                $gameInfo[$v['game_id']]['list'] = $info;
                $games[] = $gameInfo[$v['game_id']]; 
            }
        } else {
            // 通过游戏分类id 获取该分类下的 技能
            $this->gameObj->setPageSize($size);
            $games = $this->getGameInfo($gameId, self::LIST_CATEGORY_01);
        }
        return $games;
    }

    /**
     * 通过游戏id获取相关信息
     * ----------------
     * 
     * @param $gameId 游戏分类id            
     * @param $type 区别是多分类执行，还是单分类执行调用          
     * @param $number 楼层展示数量  
     * @return array
     */
    public function getGameInfo($gameId, $type = 0)
    {
        $order = $this->setOrderWay();
       
        $skill_ids = $this->gameObj->getGameByCom($gameId,$type);
        if (empty($skill_ids)) {
            // 可再次记日志
            return false;
        }
        /* if ($type !== self::LIST_CATEGORY_01)
            $skill_ids = array_slice($skill_ids, 0, self::GMAE_REC_SIZE); */
        
        $skillInfo = $this->recObj->getSkillInfos($skill_ids, $order);
        return $skillInfo;
    }

    /**
     * 新人推荐列表 按注册时间
     * ----------------
     * 
     * @return array
     */
    public function getNewUsers(int $page,int $game_id,$size=4)
    {
        $this->gameObj->setNewSkillPage($page);
        $this->gameObj->setPageSize($size);
        return $this->gameObj->getNewSkillList($game_id);
    }
    /**
     * 通过gameID获取技能总数
     * ------------------
     */
    public function getGameCountByGameId($game_id){
        return $this->gameObj->getGameCountByGameId($game_id);
    }
    /**
     * 通过gameID 获取技能列表
     * ------------------
     */
    public function getSkillByGameId($page, $game_id,$size = 10){ 
        $data = $this->gameObj->getSkillByGameId($page, $game_id,$size);
        //var_dump($data);
        if(!empty($data)){ 
            $DueRecommonService = new DueRecommService();
            $games = $DueRecommonService->getGameInfo($game_id) ;
            foreach ($data as $k=>$v){
                $data[$k]['game'] = $games[$game_id]['name'];
            }
            $userinfo = $DueRecommonService->getUserInfo($data);
            // 获取主播直播状态
            foreach ($data as $k => $v) {
                foreach($userinfo as $vo){
                    if($v['uid'] == $vo['uid']){
                        $data[$k]['isLiving'] = $DueRecommonService->getIsLiving($v['uid']);
                        $data[$k]['tags'] = $DueRecommonService->getUserTagsByUid($v['uid']);
                        $vo = ['nick'=>$vo['nick'],'pic'=>$vo['pic']];
                        $datas[] = array_merge($data[$k], $vo);
                    }
                }
            } 
            $skill_ids = array_column($datas,"skillId");
            $skillObj = new DueCertService();
            $orderNums = $skillObj->getOrderTotalBySkillID($skill_ids);
            //var_dump($orderNums);
            foreach($datas as $k=>$v){
                foreach($orderNums as $vo){
                    if($v['skillId'] == $vo['skill_id'])
                        $datas[$k]['order_total'] = $vo['order_total'];
                }
                $datas[$k]['unit'] = $skillObj::getUnitName($v['unit']);
            }
            return $datas;
        }else{
            return [];
        }
    }
}

?>
