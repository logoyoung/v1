<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年8月30日
 * Time: 上午11:42:04
 * Desc: 报名
 */
namespace api\due;
include '../../../include/init.php';
ini_set("display_errors", 1);
use service\common\ApiCommon;
use service\due\DueVoteActivity;
use service\due\DueVoteActivityConfig;

class activityEnroll extends ApiCommon
{
    const RETURN_CODE_01 = 1000; //报名成功
    const RETURN_CODE_02 = 1001; //报名失败
    const RETURN_CODE_03 = 1002; //已报名
    const RETURN_CODE_04 = 1003; //检查参数合法性
    const RETURN_CODE_05 = 1004; //该游戏分类无此活动
    public static $returnMsg = [
        self::RETURN_CODE_01=>"报名成功",
        self::RETURN_CODE_02=>"报名失败",
        self::RETURN_CODE_03=>"您已报名",
        self::RETURN_CODE_04=>"检查参数合法性",
        self::RETURN_CODE_05=>"该游戏分类无此活动",
    ];    
    
    private $vote; 
    public $uid;
    public $encpass;
    //接收参数
    private $dataParam = null;
    
    //入口安检
    public function __construct(){
        //接收参数
        $this->getParams();
        //是否登录
        $this->checkIsLogin(true);
        //校验是否是上传 报名段位图片请求
        $this->vote = new DueVoteActivity();
        if(!empty($_FILES)){
            $this->uploadLevel();
            exit;
        }
        //检查 该游戏分类下是否有此活动
        $activity = DueVoteActivityConfig::$activityGule[$this->dataParam['game_id']]; 
        $activity = array_column($activity, "activity_id");
        if(!in_array($this->dataParam['activity_id'], $activity)){
            render_json(self::$returnMsg[self::RETURN_CODE_05],self::RETURN_CODE_05,2);
        }
        //是否已经报名
        $result = $this->vote->isEnroll($this->uid,$this->dataParam['game_id'],$this->dataParam['activity_id']);
        if($result){ 
            render_json(self::$returnMsg[self::RETURN_CODE_03],self::RETURN_CODE_03);
        }
    }
    //接收请求参数
    private function getParams(){
        $this->uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        $this->encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        if(empty($_FILES)){
            //活动id
            if(!isset($_POST['activity_id']) || !is_numeric($_POST['activity_id'])){
                render_json(self::$returnMsg[self::RETURN_CODE_04]." [-1 activity_id]",self::RETURN_CODE_04,2);
            }else{
                $this->dataParam['activity_id'] = intval($_POST['activity_id']);
            }
            //游戏id
            if(!isset($_POST['game_id']) || !is_numeric($_POST['game_id'])){
                render_json(self::$returnMsg[self::RETURN_CODE_04]." [-1 game_id]",self::RETURN_CODE_04,2);
            }else{
                $this->dataParam['game_id'] = intval($_POST['game_id']);
            }
            //游戏昵称；如（王者荣耀游戏 中玩家昵称为：来啊伤害啊）
            if(!isset($_POST['game_nick']) || empty($_POST['game_nick'])){
                render_json(self::$returnMsg[self::RETURN_CODE_04]." [-1 game_nick]",self::RETURN_CODE_04,2);
            }else{
                $this->dataParam['game_nick'] = trim($_POST['game_nick']);
            }
            //qq
            if(!isset($_POST['qq']) || !is_numeric($_POST['qq'])){
                render_json(self::$returnMsg[self::RETURN_CODE_04]." [-1 qq]",self::RETURN_CODE_04,2);
            }else{
                $this->dataParam['qq'] = intval($_POST['qq']);
            }
            //等级
            if(!isset($_POST['level']) || empty($_POST['level'])){
                render_json(self::$returnMsg[self::RETURN_CODE_04]." [-1 level]",self::RETURN_CODE_04,2);
            }else{
                $this->dataParam['level'] = trim($_POST['level']);
            }
            //等级截图地址
            if(!isset($_POST['img']) || empty($_POST['img'])){
                render_json(self::$returnMsg[self::RETURN_CODE_04]." [-1 img]",self::RETURN_CODE_04,2);
            }else{
                $this->dataParam['img'] = trim($_POST['img']);
            }
        }
    }
    //上传段位图片
    private function uploadLevel(){
        $data = $this->vote->uploadLevelImg($this->uid);
        render_json($data);
    }
    //报名
    public function index(){
        $result = $this->vote->enroll($this->uid,$this->dataParam);
        $result ? render_json(self::$returnMsg[self::RETURN_CODE_01]) : render_json(self::$returnMsg[self::RETURN_CODE_02],self::RETURN_CODE_02);
    }
} 
$obj = new activityEnroll();
$obj->index();   
