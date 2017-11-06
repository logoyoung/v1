<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 陪玩服务层
 */
namespace service\due;

use lib\due\DueRecommend;
use service\common\AbstractService; 
use service\live\LiveService; 
use service\due\DueTagsService;
use lib\due\DueRecGame;
use service\user\UserDataService;

class DueRecommService extends AbstractService
{

    private $recommendObj;

    private $size;

    private $page;
    
    // 推荐的技能按照时间排序
    const SKILL_ORDER = 1;

    const SKILL_ERROR_CODE_01 = - 1000;

    const GAME_ERROR_01 = - 1001;

    public static $errorMsg = [
        self::SKILL_ERROR_CODE_01 => '加载完毕',
        self::GAME_ERROR_01 => "gameId不能为空"
    ];

    public function __construct()
    {
        $this->recommendObj = new DueRecommend();
    }

    /**
     * 设置每页显示 记录数
     * --------------
     * 
     * @param string $size            
     */
    public function setSize($size = '')
    {
        $this->size = $size ? $size : DueRecommend::SIZE_NUM;
    }

    /**
     * 获取排序规则
     * ---------
     * @reutrn string
     */
    private function setOrderWay()
    {
        return 'order by ctime desc';
    }

    /**
     * 获取推荐列表
     * -------------
     * 
     * @return array
     */
    public function getRecommend()
    {
        $this->recommendObj->setSize();
        
        // 推荐的时间倒序
        $this->recommendObj->setOrderBy(1);
        return $this->recommendObj->getRecommend();
    }

    public function getSkillInfo()
    {   
        // 获取热门推荐 第一页 uid、skill_id
        $skill_ids = $this->getRecommend();
        shuffle($skill_ids);
        $skill_ids = array_slice($skill_ids,0,4);
        $order = $this->setOrderWay();
        return $this->getSkillInfos($skill_ids, $order);
    }

    /**
     * 通过技能ID 获取 技能信息|其用户信息|直播状态
     * ---------------------------------
     * 
     * @param $skill_ids 技能ID，$order
     *            排序规则
     * @return array
     */
    public function getSkillInfos($skill_ids, $order = '')
    {
        $skillInfos = [];
        if (empty($skill_ids)) return false; 
        // 通过skillID获取skill信息
        $skillInfo = $this->getSkillInfoByIds($skill_ids, $order);
        // 批量获取 技能关联游戏信息
        $game_ids = array_column($skillInfo, 'game_id');
        $gameInfo = $this->getGameInfo($game_ids);
        
        // 批量获取技能 平均分  redis评分，因按gameid查询redis那边设计暂时不支持 此注释备用
        // $skillAvgInfo = $this->getSkillAvg(array_column($skill_ids, 'skill_id'));
        
        // 用户信息
        $userInfo = $this->getUserInfo($skill_ids);
        foreach ($skillInfo as $k => $v) {
            foreach ($gameInfo as $vo) {
                if ($v['game_id'] == $vo['gameid'])
                    $skillInfo[$k]['game'] = $vo['name'];
            }
        }
        /**
         * foreach($skillInfo as $k=>$v){
         * foreach($skillAvgInfo as $ko=>$vo){
         * if($v['skillId'] == $ko)
         * $skillsInfo[$k]['avg']=$vo;
         * }
         * } *
         */
        // 获取主播直播状态
        foreach ($skillInfo as $k => $v) {
            foreach ($userInfo as $vo) {
                if ($v['uid'] == $vo['uid']) {
                    $skillInfo[$k]['isLiving'] = $this->getIsLiving($v['uid']);
                    $skillInfo[$k]['tags'] = $this->getUserTagsByUid($v['uid']);
                    $vo = ['nick'=>$vo['nick'],'pic'=>$vo['pic']];
                    $skillInfos[] = array_merge($skillInfo[$k], $vo);
                }
            }
        }
        return $skillInfos;
    }
    /**
     * 通过用户uid获取用户tags
     * -------------------
     */
    public function getUserTagsByUid($uid= 0){
        $tagObj = new DueTagsService();
        $tagIds = $tagObj->getUserTagsByUid($uid);
        return $tagObj->getTagsByids($tagIds);
    }

    /**
     * 通过技能ID获取 平均分
     * ---------------
     *
     * public function getSkillAvg($skill_ids){
     * $comentService = new DueCommentService();
     * //$comentService->createCommentZset();
     * return $comentService->getAvgBySkillId($skill_ids);
     * }
     */
    /**
     * 通过技能ID 获取技能信息
     * -----------------
     * 
     * @param $skill_ids array            
     */
    public function getSkillInfoByIds($skill_ids, $order = '')
    {
        // 获取技能信息
        $skill_ids = array_column($skill_ids, 'skillId');
        $data['skillId'] = $skill_ids;
        $skillObj = new DueCertService();
        $datas = $skillObj->getSkillBySkillId($data, $order);
        $orderNums = $skillObj->getOrderTotalBySkillID($skill_ids);
        foreach($datas as $k=>$v){
            foreach($orderNums as $vo){
                if($v['skillId'] == $vo['skill_id'])
                    $datas[$k]['order_total'] = $vo['order_total'];
            }
            $datas[$k]['unit'] = $skillObj::getUnitName($v['unit']);
        }
        if (! is_array($datas)) {
            throw new \Exception($datas);
            exit();
        }
        foreach($datas as $k=>$v){
            if(!isset($v['order_total'])) 
                $datas[$k]['order_total']=0;
            $datas[$k]['avg_score'] = $v['avg_score']/2;
        }
        return $datas;
    }

    /**
     * 获取game信息
     * ----------
     * 
     * @param game_id
     */
    public function getGameInfo($gameId)
    {
        //直接欢朋游戏分类中拉去
        /* $gameObj = new GameService();
        $gameObj->setGameId($gameId);
        return $gameObj->getGameInfoById($gameObj->getGameId(), new \DBHelperi_huanpeng()); */
        //进入约玩的 due_game表中拉去
        $gameObj = new DueRecGame();
        return $gameObj->getGameInfoByGameId($gameId);
    }

    /**
     * 获取主播直播状态
     * ------------
     * 
     * @param $luid 主播uid            
     * @return int 1 or 0
     */
    public function getIsLiving($luid)
    {
        $liveObj = new LiveService();
        $liveObj->setLuid($luid);
        return $liveObj->isLiving();
    }

    /**
     * 获取用户信息
     * ---------
     */
    public function getUserInfo($skill_ids)
    {
        $uids = array_column($skill_ids,"uid");
        $uids = array_unique($uids);
        $userObj = new UserDataService();
        $userInfo = $userObj->setUid($uids)->batchGetUserInfo();
        return $userInfo;
    }
}

?>