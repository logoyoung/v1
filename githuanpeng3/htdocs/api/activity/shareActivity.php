<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年9月5日
 * Time: 上午11:49:37
 * Desc: 分享活动时返回活动信息
 */
namespace api\activity;
include '../../../include/init.php';
// ini_set("display_errors", 1);
use service\activity\InviteActivityConfig;
use service\common\ApiCommon;
use service\activity\InviteActivityService;
use service\activity\ShareActivityConfig;

class shareActivity extends ApiCommon
{
    public $uid;
    public $encpass;
    public $is_login;
    public $activity_id;
    private $inviteService = null;
    private $shareActivity = null;
     
    const RETURN_CODE_01 = 1001; //活动未发布
    const RETURN_CODE_02 = 1002; //活动获取失败 
    const RETURN_CODE_03 = 1003; //未到活动期
    const RETURN_CODE_04 = 1004; //活动期已过期
    const RETURN_CODE_05 = 1005; //未传递 islogin
    const RETURN_CODE_06 = 1006; //未传递 活动id activity_id
    const RETURN_CODE_10 = 9000; //缺少必要参数
    static public $codeDesc = [
        self::RETURN_CODE_01=>'活动还未发布',
        self::RETURN_CODE_02=>'活动获取失败',
        self::RETURN_CODE_03=>'未到活动期',
        self::RETURN_CODE_04=>'活动期已过期',
        self::RETURN_CODE_10=> '缺少必要参数'
    ];

    public function __construct(){
        $this->uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        $this->encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';  
        if(isset($_POST['activity_id']) && !empty($_POST['activity_id'])){
            $this->activity_id = intval($_POST['activity_id']);
        }else{
            render_json(self::$codeDesc[self::RETURN_CODE_10],self::RETURN_CODE_06);
        }
        if(is_null($this->inviteService))
            $this->inviteService = new InviteActivityService();
        if(is_null($this->shareActivity))
            $this->shareActivity = new ShareActivityConfig();
    }
    public function returnData($code){
        render_json(self::$codeDesc[$code],$code);
    }
    public function returnActivityInfo($data){  
        if($data['is_login'] == 1){
            $this->checkIsLogin(true);
        }
        $now = date("Y-m-d H:i:s"); 
        if($now < $data['stime']){ 
            $this->returnData (self::RETURN_CODE_03);
        } 
        if($now > $data['etime']){ 
            $this->returnData (self::RETURN_CODE_04);
        }
        if(!empty($data)){
            //获取 特殊 单独配置的活动信息
            $activityData = ShareActivityConfig::$shareActivity[$data['id']]; 
            if(!empty($activityData)){
                if($activityData['is_param'] == 1){
                    $function = $activityData['get_param_function'];  
                    $inviteCode = $this->shareActivity->$function($this->uid); 
                    return ShareActivityConfig::getPageHttpHost().$activityData['url']."?code=".$inviteCode['invite_code'];
                }
            }else{ //普通走库的 分享活动信息
                return $data['url'];
            } 
        }else{ 
            $this->returnData (self::RETURN_CODE_02);
        }
    }
    public function display(){ 
        //获取邀请活动
        $data = $this->inviteService->inviteActivityInfo($this->activity_id);  
        if($data == false){
            write_log(__CLASS__." 第 ".__LINE__." 行 获取活动失败  活动id为：".$this->activity_id."请 去admin_information表中核实",'shareActivity');
            $this->returnData (self::RETURN_CODE_02);
        }
        //获取 分享URL链接
        $share_url = $this->returnActivityInfo($data); 
        //获取邀请活动icon
//        $arr['icon']  = $data['poster'];
        //获取邀请活动icon
        $arr['share_thumb_img']  = InviteActivityConfig::getHttpHost().$data['thumbnail'];
        //获取邀请活动标题
        $arr['title'] = $data['title'];
        //获取邀请活动描述
        $arr['descMsg']  = $data['content'];
        //获取邀请活动URL
        $arr['url']   = $share_url; 
        render_json($arr); 
    }
}

$obj = new shareActivity();
$obj->display();

