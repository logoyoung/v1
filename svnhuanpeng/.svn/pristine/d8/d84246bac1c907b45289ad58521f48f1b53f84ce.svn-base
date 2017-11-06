<?php
/**
 * Created by PhpStorm.
 * User: yalong
 * Date: 17/6/7
 * Time: 下午4:28
 * Desc: 批量获取用户头像
 */
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include '../../../../include/init.php';
use service\user\UserDataService;
use service\due\rongCloud\RongCloudServiceHelp;

class GetPic{
    
    private $uids;
    
    const ERROR_CODE_01 = -1000;
    
    private $errDes = [
        self::ERROR_CODE_01=>'缺少 uid 参数。注：多个uid以逗号隔开'
    ];
    
    public function __construct($uids){
        if(!$uids) render_error_json($this->errDes[self::ERROR_CODE_01],self::ERROR_CODE_01);
        $this->uids = $uids ? $uids : "";
    }
    /**
     * 批量获取头像
     * ---------
     * @param uids
     * @return array 
     */
    public function getPic(){
        $userObj = new UserDataService();
        $uids = explode(",", $this->uids); 
        if(empty(array_filter($uids))) return array('list'=>[]);
        if(count($uids)<2 && in_array(1, $uids)){}
        else{
            $userObj->setUid($uids);
            $userInfo = $userObj->batchGetUserInfo(); 
            foreach ($userInfo as $v){
                $pic[] = array("uid"=>$v['uid'],'pic'=>$v['pic'],'nick'=>$v['nick']);
            }
        }
        if(in_array(1, $uids)){
            //系统管理员头像昵称 追加  以后等后台 开发功能 做替换暂时生写
            //$pic[] = array("uid"=>1,'pic'=>DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain-img']."/userPic/due_system_icon.png",'nick'=>'陪玩系统消息'); //UploadImagesCommon::getImageDomainUrl().
            $pic[] = array("uid"=>1,'pic'=>DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain']."/static/img/due/due_system_icon.png",'nick'=>'陪玩系统消息'); 
        }
        return !empty($pic) ? array('list'=>$pic) : array('list'=>[]);
    } 
}

$uids = is_string($_POST['uid']) && isset($_POST['uid']) ? filter_var($_POST['uid'],FILTER_SANITIZE_STRING) : "";
$obj = new GetPic($uids);

$pic  = $obj->getPic(); 
if($pic) render_json($pic);



?>
