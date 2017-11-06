<?php
namespace api\due\rongCloud;

error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../../../../include/init.php';
use lib\User;
use service\due\rongCloud\RongCloudService;
use service\due\rongCloud\RongCloudServiceHelp;
use SDK\rongCloud\RongCloud;

class sendMsg
{
    private $uid;
    private $rongObj;
    private $rongServerObj;
    private $accept_uid;
    private $type;
    private $extraCode;
    private $contents;
    private $sendCategory;
    
    const ERROR_CODE_01 = -1000;
    const ERROR_CODE_02 = -1001;
    const ERROR_CODE_03 = -1013;
    const ERROR_CODE_04 = -1014;
    const ERROR_CODE_05 = -1015;
    
    private $errDes = [
        self::ERROR_CODE_01 => '缺少 uid 参数',
        self::ERROR_CODE_02 => '缺少 encpass 参数',
        self::ERROR_CODE_03 => 'ecnpass验证错误',
        self::ERROR_CODE_04 => '缺少 收信人 uid 参数',
        self::ERROR_CODE_05 => '缺少 消息类型 type 参数',
    ];
    /**
     * 初始化融云|验证用户登录是否合法
     * -----------------------
     * @param $uid        发件人uid
     * @param $encpass    发件人encpass
     * @param $accept_uid 收件人uid
     */
    public function __construct($uid,$encpass,$accept_uid,$type,$sendCategory){
//         $uid =='' ? render_error_json($this->errDes[self::ERROR_CODE_01],self::ERROR_CODE_01) : '';
//         $encpass =='' ? render_error_json($this->errDes[self::ERROR_CODE_02],self::ERROR_CODE_02) : '';
        $accept_uid =='' ? render_error_json($this->errDes[self::ERROR_CODE_04],self::ERROR_CODE_04) : '';
//         $type =='' ? render_error_json($this->errDes[self::ERROR_CODE_05],self::ERROR_CODE_05) : '';
        $this->uid = $uid;
        $this->encpass = $encpass;
        $this->accept_uid = $accept_uid;
        $this->type = $type;
        $this->sendCategory = $sendCategory;
        
        //$code = $this->regUser();
        $code = true;
        if($code!==true){
            $desc=$this->errDes[$code];
            render_error_json($desc,$code);
        }
        $this->rongObj = new RongCloudService();
    }
    /**
     * 验证用户登录是否合法
     * ---------------
     * @return Ambigous <boolean, number>
     */
    private function regUser(){
        $user = new User($this->uid);
        return $user->checkStateError($this->encpass);
    }
    /**
     * 用户发送 单聊文本消息通知
     * ------------------
     * return json
     */
    public function toMsg(){
        $sendCategory = $this->sendCategory==1 ? RongCloudServiceHelp::OBJECT_NAME_13 : RongCloudServiceHelp::OBJECT_NAME_01;
//         echo $sendCategory;
        $this->setContent();
        $rongServer = $this->rongObj->getInstance("android");
        //系统消息测试
        if(isset($_REQUEST['aa'])){
            $res = $rongServer->addSystemMsg( $this->accept_uid, $this->content,0,0);
            echo "<br />系统消息发送：";
            var_dump($res);
            echo "<br />";exit;
        }
        if($_REQUEST['ss'] == 1){
            $content['content'] = '系统消息测试..';
            $content['extra'] = 20001; //传递 其他参数 
            $fromUserId= RongCloudServiceHelp::getSystemAdminInfo(2);
            $content['user']['id']   = $fromUserId['uid'];
            $content['user']['name'] = $fromUserId['nick'];
            $content['user']['icon'] = $fromUserId['pic'];
            var_dump($content);
            $content = json_encode($content);
            $pushData = $content;
//             $toUserId = explode(",", $this->accept_uid); 
            $toUserId = $this->accept_uid;  
            var_dump($rongServer->sendSystemMsg(2, $toUserId, 'ZDY:JSON', $content,'123',$pushData,'0', '0'));
        }else 
            return $rongServer->sendMsg($this->uid,$this->accept_uid,$this->content,$sendCategory,$this->extraCode);
    } 
    /**
     * 消息入队
     */
    public function addMsgList($uid,$accept_uid){
        $rongServer = $this->rongObj->getInstance("android");
        return $rongServer->addMsgList($uid,$accept_uid,"您好",RongCloudServiceHelp::OBJECT_NAME_13,10001);
    }
    /**
     * 获取redis 消息任务队列
     * -----------------
     */
    public function getMsgList(){
        $rongServer = $this->rongObj->getInstance("android");
        return $rongServer->getMsgList();
    }
    /**
     * 设置发送内容|设置消息类型
     * ---------
     * @return string
     */
    private function setContent(){
        switch ($this->type){
            case 1: 
                $this->extraCode = RongCloudServiceHelp::MSG_CG_01;
                $this->content   = '我下单啦，快来接单吧~';
                break;
            case 2: 
                $this->extraCode = RongCloudServiceHelp::MSG_CG_02;
                $this->content   = '我申请退单 麻利的给退了~';
                break;
            case 3: 
                $this->extraCode = RongCloudServiceHelp::MSG_CG_03;
                $this->content   = '我接单了 按时赴约噢~';
                break;
            case 4: 
                $this->extraCode = RongCloudServiceHelp::MSG_CG_04;
                $this->content   = '我拒绝退单 金币有来无回 呼呼哈哈哈~';
                break;
            case 5: 
                $this->extraCode = RongCloudServiceHelp::MSG_CG_04;
                $this->content   = '系统广播消息测试';
                break;
        } 
    }
    public function getRongImgCode(){
        $appKey = 'sfci50a7s1hgi';
        $appSecret = 'w6iSB2PvzPSb';
        $jsonPath = "../../../../include/SDK/rongCloud/jsonsource/";
        $RongCloud = new RongCloud($appKey,$appSecret);
        $result = $RongCloud->SMS()->getImageCode($appKey);
        echo "getImageCode    ";
        print_r($result);
        echo "\n";
    }
}
$uid = intval($_REQUEST['formUid']);
$encpass = $_REQUEST['encpass'];
$touid = $_REQUEST['toUid'] ;
//type 1 用户下单 通知主播；2 用户退单 通知主播；3 主播接单 通知用户；4 主播拒单 通知用户
$type  = intval($_REQUEST['type']); 
$sendCg = isset($_REQUEST['sendCg']) ? intval($_REQUEST['sendCg']) : 1;
$obj = new sendMsg($uid, $encpass, $touid,$type,$sendCg);
if($_REQUEST['imgCode'] == 1){
    $obj->getRongImgCode();
    exit;
}

echo $obj->toMsg();
if(isset($_REQUEST['getMsg'])){ 
    echo $obj->addMsgList($uid,$touid);
    $data = $obj->getMsgList();
    echo "<br />队列任务：<br />";
    var_dump($data);
}

?>
