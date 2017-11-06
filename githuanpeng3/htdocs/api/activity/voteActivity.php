<?php 
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年8月30日
 * Time: 上午11:23:57 
 * Desc: 投票活动信息拉去
 */
namespace api\activity;
include '../../../include/init.php';
//ini_set("display_errors", 1); 
use service\activity\VoteActivity;
use service\activity\ShareActivityConfig;
use service\activity\VoteActivityConfig;

class voteActivitys
{
    const RETURN_CODE_01 = 1000; // 当前时间在活动期内
    const RETURN_CODE_02 = 1001; // 活动还未开始，敬请期待
    const RETURN_CODE_03 = 1002; // 活动已结束
    const RETURN_CODE_04 = 1003; // 暂无活动
    
    static public $returnMsg = [
        self::RETURN_CODE_01 => '活动进行中',
        self::RETURN_CODE_02 => '活动还未开始，敬请期待',
        self::RETURN_CODE_03 => '活动已结束',
        self::RETURN_CODE_04 => '暂无活动',
    ];
    
    private $gameId;
    private $voteActivity = NULL;
    //入口安检
    public function __construct(){
        //游戏类别获取对应活动  默认是 王者荣耀 190；注 gameId与 due_game表中的gameid一致
        $this->gameId = !isset($_POST['gameId']) ? 190 : intval($_POST['gameId']);
        if(is_null($this->voteActivity))
            $this->voteActivity = new VoteActivity();
    }
    //拉去 活动信息（活动标题、内容、英雄列表、活动起止时间）
    private function index(){
        //配置文件获取活动数据
//        $activity = VoteActivityConfig::$activityGule[$this->gameId];
//        $activity = array_reverse($activity,true);
        //数据表 admin_information 中拉去 活动数据 
        $data = $this->voteActivity->getVoteActivity(ShareActivityConfig::VOTE_ACTIVITY_ID);
        $activity['activity_id'] = ShareActivityConfig::VOTE_ACTIVITY_ID;
        $activity['game_id'] = VoteActivityConfig::GAME_01;
        $activity['activity'] = $data['title'];
        $activity['desc'] = $data['content'];
        $activity['status'] = $data['ispublish'];
        $activity['stime'] = $data['stime'];
        $activity['etime'] = $data['etime']; 
        $activity['thumbnail'] = VoteActivityConfig::imgHostUrl().$data['thumbnail']; 
        if($activity['status'] === 1 ){
            $voteNums = $this->getVoteNums($activity['activity_id']); //投票活动投票数
            $voteVoteNums = 0;
            foreach($voteNums as $vo){
                $voteVoteNums += $vo['nums'];
            } 
            $hero = VoteActivityConfig::$heroGule[$activity['activity_id']];  
            foreach($hero as $ko=>$vo){
                $hero[$ko]['percent'] = 0;
                foreach($voteNums as $vs){
                    if($vo['hero_id'] == $vs['hero_id']){ 
                        
                        $hero[$ko]['percent'] = $vs['nums'] == 0 ? 0 : round($vs['nums']/$voteVoteNums,2)*100;
                    }
                }
                $hero[$ko]['img'] = ShareActivityConfig::getPageHttpHost().$vo['img'];
            }
            $activity['heros'] = $hero;
            $returnActivity[] = $activity; 
            unset($activity);
            return $returnActivity;
        }  
        return [];
    }
    //获取活动各英雄投票数
    private function getVoteNums(int $activity_id){
        return $this->voteActivity->returnVoteNums($activity_id);
    }
    //返回 注：如果不在活动起止时间内 则返回一个status ：0 让前端进行相应显示
    public function display(){
        $data = $this->index();
        foreach($data as $k=>$vo){
            $now = date("Y-m-d H:i:s");
            if($now < $vo['stime']){
                $data[$k]['code'] = self::RETURN_CODE_02;
                $data[$k]['codeDesc'] = self::$returnMsg[self::RETURN_CODE_02];
                continue;
            }
            if($now > $vo['etime']){
                $data[$k]['code'] = self::RETURN_CODE_03;
                $data[$k]['codeDesc'] = self::$returnMsg[self::RETURN_CODE_03];
                continue;
            }
            $data[$k]['code'] = self::RETURN_CODE_01;
            $data[$k]['codeDesc'] = self::$returnMsg[self::RETURN_CODE_01];
        }
        $data = $data[0]; 
        !empty($data) ? render_json($data) : render_json(self::$returnMsg[self::RETURN_CODE_04],self::RETURN_CODE_04);
    } 
}
$obj = new voteActivitys();
$obj->display();
