<?php 
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年8月30日
 * Time: 上午11:13:19
 * Desc: 活动英雄投票接口
 */
namespace api\activity;
include '../../../include/init.php';
//ini_set("display_errors", 1); 
use service\common\ApiCommon;
use service\activity\VoteActivityConfig;
use service\activity\VoteActivity;
use \service\user\UserDataService;

class vote extends ApiCommon
{
    const RETURN_CODE_01 = 1000; //投票成功
    const RETURN_CODE_02 = 1001; //未登录
    const RETURN_CODE_03 = 1002; //不在活动期
    const RETURN_CODE_04 = 1003; //参数不合法（缺少参数、活动和英雄不匹配）
    const RETURN_CODE_05 = 1004; //已投票
    const RETURN_CODE_06 = 1005; //活动已关闭
    const RETURN_CODE_07 = 1006; //服务器出错
    const RETURN_CODE_08 = 1007; //投票失败 
    const RETURN_CODE_09 = 1008; //该活动不存在 请与 admin_information 核实
    
    static public $returnMsg = [
        self::RETURN_CODE_01 => '投票成功',
        self::RETURN_CODE_02 => '未登录',
        self::RETURN_CODE_03 => '不在活动期',
        self::RETURN_CODE_04 => '参数不合法',
        self::RETURN_CODE_05 => '已投票',
        self::RETURN_CODE_06 => '活动已关闭', 
        self::RETURN_CODE_08=> '投票失败',
        self::RETURN_CODE_09=> '该活动不存在',
    ];
    
    private $returnCode;
    private $returnDesc;
    public $uid;
    public $encpass;
    public $activity_id; 
    public $hero_id;
    public $game_id;
    public $activity;
    public $hero;
    public $voteActivity = null;
    
    //入口安检
    public function __construct(){
        $this->uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        $this->encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        if(!isset($_POST['activity_id']) || !is_numeric($_POST['activity_id'])){
            render_json(self::$returnMsg[self::RETURN_CODE_04],self::RETURN_CODE_04,2);
        }else{
            $this->activity_id = intval($_POST['activity_id']);
        }
        if(!isset($_POST['hero_id']) || !is_numeric($_POST['hero_id'])){
            render_json(self::$returnMsg[self::RETURN_CODE_04],self::RETURN_CODE_04,2);
        }else{
            $this->hero_id = intval($_POST['hero_id']);
        }
        //游戏分类id 暂时只有王者荣耀 默认
        if(!isset($_POST['game_id'])){
            $this->game_id = 190;//王者荣耀
        }else{
            $this->game_id = intval($_POST['game_id']);
        }
        //校验该用户是否登录 
        $this->checkIsLogin(true);
        //之前因安卓端为传递encpass给H5的策略
//        $userData = new UserDataService();
//        $userData->setUid($this->uid);
//        $data = $userData->isExist();
//        if(!$data) render_json (self::$returnMsg[self::RETURN_CODE_02],self::RETURN_CODE_02,2);
        if(is_null($this->voteActivity)){
            $this->voteActivity = new VoteActivity();
        }
        //投票 是否在活动设置时间段内
        $this->activity = $this->regActivity();
        //校验该英雄和活动是否合法对应
        $this->regHero($this->activity);
        //校验该用户是否已投票
        $this->isVote($this->uid,$this->activity_id);
    }
    //校验活动投票是否合法
    private function regActivity(){
        $activity = $this->voteActivity->getVoteActivity($this->activity_id); 
        if($activity['ispublish'] == 0){ 
            render_json(self::$returnMsg[self::RETURN_CODE_06],self::RETURN_CODE_06);
        }
        $now = date("Y-m-d H:i:s");
        if($now<$activity['stime'] || $now>$activity['etime']){
            render_json(self::$returnMsg[self::RETURN_CODE_03],self::RETURN_CODE_03);
        }
        return $activity;
    }
    //校验英雄和活动是否匹配
    private function regHero($activity){
        $hero = VoteActivityConfig::$heroGule[$activity['id']];
        $this->hero = $hero;
        $hero_id = array_column($hero, 'hero_id');
        if(!in_array($this->hero_id, $hero_id)){
            render_json(self::$returnMsg[self::RETURN_CODE_04],self::RETURN_CODE_04);
        }
    }
    //校验该用户是否已投票
    private function isVote(int $uid,int $activity_id){ 
        $result = $this->voteActivity->isVote($uid, $activity_id);
        if(!$result){
            render_json(self::$returnMsg[self::RETURN_CODE_05],self::RETURN_CODE_05);
        }
    }
    //投票
    public function voteHero(){
        //检索 该活动是否已生成 没有生成 即生成
        $result = $this->voteActivity->isHasActivity($this->activity,$this->hero,$this->game_id);
        if(!$result){
            render_json(self::$returnMsg[self::RETURN_CODE_06],self::RETURN_CODE_07);
        } 
        //投票
        $result = $this->voteActivity->vote($this->uid,$this->activity['id'],$this->hero_id);
        if(!$result){
            render_json(self::$returnMsg[self::RETURN_CODE_08],self::RETURN_CODE_08);
        }
        render_json(self::$returnMsg[self::RETURN_CODE_01]);
    } 
}
$obj = new vote();
$obj->voteHero();

