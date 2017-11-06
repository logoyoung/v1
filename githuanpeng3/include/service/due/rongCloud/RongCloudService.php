<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/6
 * Time: 下午12:06
 */

namespace service\due\rongCloud;

use SDK\rongCloud\RongCloud;
use system\RedisHelper;
use GuzzleHttp\json_encode;
use service\common\UploadImagesCommon;
use service\user\UserDataService;
use service\due\DueApplePush;
use service\due\DueOrderService;

class RongCloudService
{
    /**
     * 欢朋 -  测试版保密字  001
     * const IOS_APP_KEY    = 'pvxdm17jpclur';
	 * const IOS_APP_SECRET = 'wkFOtRilEP';
	 * //以下测试号可用 欢朋测试 key 002
	 * AppKey ：pvxdm17jpclmr     
     * App Secret: 3MkNiKlxt7C  
     * //因以上两个测试key跑满 暂换 徐阳提供的测试key如下：
     * AppKey     : sfci50a7s1hgi   
     * App Secret : w6iSB2PvzPSb
     */
    const DEV_APP_KEY    = 'sfci50a7s1hgi';
    const DEV_APP_SECRET = 'w6iSB2PvzPSb';
    
    //正式 环境
	const APP_KEY    = 'uwd1c0sxurgc1'; 
	const APP_SECRET = 'y7mGhqj3GPON0Z'; 


	public static function getInstance( $client = 'android' ):RongCloudServiceHelp
	{
	    if($GLOBALS['env'] == 'PRO'){
    	    $key = self::APP_KEY;
    	    $secret = self::APP_SECRET;
	    }else{
	        $key = self::DEV_APP_KEY;
	        $secret = self::DEV_APP_SECRET;
	    } 
	    return new RongCloudServiceHelp($key, $secret);
	}
}


class RongCloudServiceHelp
{
	static private $_rCloudObj=null;
	static private $userService = null;
	static private $applePush = null;
	
	private $params =array();
	
	
	//发送 消息格式类型 
	const OBJECT_NAME_01 = 'RC:TxtMsg';     //文本消息
	const OBJECT_NAME_02 = 'RC:ImgMsg';     //图片消息
	const OBJECT_NAME_03 = 'RC:VcMsg';      //语音消息
	const OBJECT_NAME_04 = 'RC:ImgTextMsg'; //图文消息
	const OBJECT_NAME_05 = 'RC:LBSMsg';     //位置消息
	const OBJECT_NAME_06 = 'RC:ContactNtf'; //添加联系人消息
	const OBJECT_NAME_07 = 'RC:InfoNtf';    //提示条（小灰条）通知消息
	const OBJECT_NAME_08 = 'RC:ProfileNtf'; //资料通知消息
	const OBJECT_NAME_09 = 'RC:GrpNtf';     //群组通知消息
	const OBJECT_NAME_10 = 'RC:DizNtf';     //讨论组通知消息
	const OBJECT_NAME_11 = 'RC:CmdNtf';     //通用命令通知消息
	const OBJECT_NAME_12 = 'RC:CmdMsg';     //命令消息
	
	//融云 自定义消息格式
	const OBJECT_NAME_13 = 'ZDY:JSON';
	const OBJECT_NAME_14 = 'ZDY:TEXT';
	const OBJECT_NAME_15 = 'ZDY:ORDERCG'; //所有订单数发生改变均多调用此下发通知
	
	//-----消息类型-----
	const MSG_CG_00 = 10000; //默认系统消息类型 不显示 客户端拦截更新使用
	
	const MSG_CG_01 = 10001; //用户下单 通知主播  （主播待处理订单数会更新）        弹出
	const MSG_CG_02 = 10002; //用户退单 通知主播                                                           弹出
	const MSG_CG_03 = 10003; //主播接单 通知用户  （主播待处理订单数会更新）        弹出
	const MSG_CG_04 = 10004; //主播拒单 通知用户  （主播待处理订单数会更新）        弹出
	const MSG_CG_05 = 10005; //主播取消订单 通知用户  （主播待处理订单数会更新）弹出
	
    const MSG_CG_06 = 10006; //暂无使用
    const MSG_CG_07 = 9999;  //订单数发生改变  （主播待处理订单数会更新）    陪玩系统消息 不弹出
    //系统消息
    const MSG_CG_08 = 20001;  //系统广播消息 推送  客户端弹出消息体 点击进入向到 欢朋消息列表
    const MSG_CG_09 = 20002;  //系统广播消息 推送  客户端弹出消息体 点击不进入到 欢朋消息列表
    
    //欢朋系统管理员  类型 
    const SYSTEM_CATEGORY_02 = 2;  //系统广播消息
    const SYSTEM_CATEGORY_03 = 3;  //以下预留
    const SYSTEM_CATEGORY_04 = 4;
 
	
	//约玩系统 管理员uid
	const RONG_MSG_ADMIN_UID = 1;
	//融云 系统广播消息  消息体内容 以及融云发送 相关参数
	const RONG_HAS_MSG_01 = 'rongHasMsg01'; //01has 系统广播消息体
	const RONG_HAS_MSG_PREFIX= 'HasField_'; //多条消息前缀
	//融云 系统广播消息 接收人uid list key
	const RONG_ACCEPT_KEY_PREFIX = 'rongAcceptUids_';
	//融云 系统广播消息 1秒 只能下发100次
	const RONG_SYS_MSG_NUM = 100;
	
    
	public function __construct( $appKey, $appSecret )
	{
	    if(self::$_rCloudObj==null)
		    self::$_rCloudObj = new RongCloud($appKey,$appSecret);
	    if(self::$userService==null)
	        self::$userService = new UserDataService();
	    if(self::$applePush==null)
	        self::$applePush = new DueApplePush();
	}
    
	//获取融云约玩系统管理员 
	public static function getSystemAdminInfo($fromUserId){
	    switch ($fromUserId){
	        case self::RONG_MSG_ADMIN_UID:
	            $data = [
    	            'uid'=>$fromUserId,
    	            'nick'=>'陪玩系统消息',
    	            'pic'=>DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain']."/static/img/due/due_system_icon.png"
                ];
	        break;
	        case self::SYSTEM_CATEGORY_02:
	            $data = [
    	            'uid'=>$fromUserId,
    	            'nick'=>'系统消息',
    	            'pic'=>DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain']."/static/img/due/systemIcon.png"
                ];
	        break;
	    }
	    return $data;
	} 
    //获取融云token
	public function getToken($userId, $userName, $userPic)
	{
		$result = self::$_rCloudObj->user()->getToken($userId,$userName,$userPic);

		return $result;
	}
	//设置单聊参数
    public function setParams($fromUserId,$toUserId,$content,$type,$param='',$userInfo){
        $this->params[] = $fromUserId;   //fromUserId
        $this->params[] = $toUserId;    //toUserId
        $this->params[] = $type; //objectName
        //融云自定义 消息下发json格式
        $arrData['content'] = $content;
        $arrData['user']['id'] = $userInfo['uid'];
        $arrData['user']['name'] = $userInfo['nick'];
        $arrData['user']['icon'] = $userInfo['pic'];
        $arrData['extra'] = strval($param); 
        if($type == self::OBJECT_NAME_13){
            $this->params[] = json_encode($arrData);//content
            //$this->params[] = $content; //pushContent
            $this->params[] = ""; //pushContent
            $this->params[] = json_encode($arrData);//pushData
        }else{
            //融云 系统消息下发
            $this->params[] = json_encode($arrData); //content
            //$this->params[] = $content; //pushContent
            $this->params[] = ""; //pushContent
            $this->params[] = json_encode($arrData); //pushData
        }
        $this->params[] = "4";
        $this->params[] = "0";
        $this->params[] = "0";
        $this->params[] = "0";
        $this->params[] = "0";
    }
    /**
     * 发送消息
     * ---------------
     * @return json
     */
	public function sendMsg($fromUserId, $toUserId, $content,$type=self::OBJECT_NAME_01,$param=self::MSG_CG_01){ 
	    //fromUserId ==1 为系统管理员 下发消息
	     if(intval($fromUserId)==1){  
	        $data = self::getSystemAdminInfo($fromUserId);
	        $userInfo = $data;
	    }else{ 
            self::$userService->setUid($fromUserId);
            $userInfo = self::$userService->getUserInfo();
	    }
	    $this->setParams($fromUserId, $toUserId, $content,$type,$param,$userInfo);
	    @$params = $this->params;
	    $this->params = []; 
	    if(in_array($param, [10001,10002,10003,10004]) && $param!=10101){
	        $sendCg = self::OBJECT_NAME_01;
	        $result = self::$_rCloudObj->Message()->publishPrivate($params[0],[$params[1]],$sendCg,$params[3],$params[4],$params[5],$params[6],$params[7],$params[8],$params[9],$params[10],$params[11]) ;  
	        file_put_contents("/data/logs/due_popMsgList.log" . date("Ymd"),"融云".$sendCg."类型消息推送：".$result." | ".date("Y-m-d H:i:s")."\n",FILE_APPEND);
	        //推iOS 离线消息
	        $orderService = new DueOrderService();
	        $toDoNum = $orderService->getOrderNumByLuid($toUserId);
	        if($fromUserId!=1){
    	        $pic = $userInfo['pic'];
    	        $nick = $userInfo['nick'];
	        }else{
	            $data = self::getSystemAdminInfo(1);
	            $pic  = $data['pic'];
	            $nick = $data['nick'];
	        }
// 	        self::$applePush->setPic($pic);
// 	        self::$applePush->setNick($nick);
// 	        self::$applePush->setFromUid($fromUserId);
// 	        self::$applePush->setToDoNumber($toDoNum);
// 	        $applePushResult  = self::$applePush->sendMsg($fromUserId, $toUserId, $content, $param);
// 	        file_put_contents("/data/logs/due_popMsgList.log" . date("Ymd"), date("Y-m-d H:i:s")."发送者：$fromUserId  接收人：$toUserId ----苹果离线推送状态：".$applePushResult."------".PHP_EOL, FILE_APPEND);
	        //var_dump($applePushResult);
// 	        file_put_contents('/data/logs/yalong1.log',$applePushResult." | "+date("Y-m-d H:i:s")."\n",FILE_APPEND);
	    }
// 	    file_put_contents("/data/logs/due_popMsgList.log" . date("Ymd"), '*********'.json_encode($params).PHP_EOL, FILE_APPEND);
        $code =  self::$_rCloudObj->Message()->publishPrivate($params[0],[$params[1]],$params[2],$params[3],$params[4],$params[5],$params[6],$params[7],$params[8],$params[9],$params[10],$params[11]) ;
        file_put_contents("/data/logs/due_popMsgList.log" . date("Ymd"),"融云".$params[2]."类型消息推送：".$code." | ".date("Y-m-d H:i:s")."\n",FILE_APPEND);
        return $code;
	}
	/**
	 * 发送系统消息  缓冲池
	 * ---------------
	 * @param $fromUserId int 发送者uid
	 * @param $toUserId   string 接收者uid 多个uid 以英文  , 逗号隔开
	 * @param $content    string 消息内容
	 * @param $type       int 0系统广播消息管理员下发通知；其他现在默认陪玩系统管理员id
	 * @param $appPushWay int 0 客户端接收到消息体 点击进入到 欢朋消息列表；1点击不进入欢朋消息列表（预留现没有此情况）
	 * @return bool
	 */
	public function addSystemMsg(string $toUserId,string $msg,$type = 0 ,$appPushWay= 0){
	    if($toUserId == '' || $msg == '')
	    { 
	        write_log(__CLASS__." 第".__LINE__."行 接收者uid为空或下发消息体为空",'rongSystemMsgError');
	        return false;
	    }
	    //做redis 缓存 
	    $redisObj = RedisHelper::getInstance("huanpeng");
	    // 拼装消息体    | 将消息体 存入redis hastab
	    if($appPushWay== 0){
	        $extra = self::MSG_CG_08;   
	    }else{ //加其他发送类型   往下扩展
	        $extra = self::MSG_CG_09;   
	    }
	    $sendCg = self::OBJECT_NAME_13;   
	    if($type==0){  //系统广播消息类型  发送者id
	        $fromUserId = self::SYSTEM_CATEGORY_02;
	    }else $fromUserId=self::RONG_MSG_ADMIN_UID; //陪玩系统消息   以后elseif 往下扩展
	    $content['content'] = $msg;
	    $content['extra'] = $extra; //下发系统消息类型
	    $fromUserId= $this->getSystemAdminInfo($fromUserId);
// 	    $userInfo = self::$userService->getUserInfo();
	    $content['user']['id']   = $fromUserId['uid'];
	    $content['user']['name'] = $fromUserId['nick'];
	    $content['user']['icon'] = $fromUserId['pic'];
	    $datas['fid'] = $fromUserId['uid'];
	    $datas['sendCg'] = $sendCg;
	    $datas['content'] = $content;
	    $datas['pushContent'] = '';
	    $datas['pushData'] = $content;
	    $datas['isCounted'] = "0";
	    $datas['isPersisted'] = "0";
	    $datas = json_encode($datas);
	    $toUserId = explode(",", $toUserId);
	    $redisObj->hDel(self::RONG_HAS_MSG_01);
	    $haslen = $redisObj->hLen(self::RONG_HAS_MSG_01);
	    $haslen+=1;  //用作 has消息体 和 接收人uid list 进行一一对应的标识
	    //设置多条消息  has
	    $result = $redisObj->hSet(self::RONG_HAS_MSG_01,self::RONG_HAS_MSG_PREFIX.$haslen,$datas);
	    //将接收uid用户压入redis list 等待下发 popMsg  
	    foreach($toUserId as $v){
	        $v = intval($v);
	        $redisObj->rPush(self::RONG_ACCEPT_KEY_PREFIX.$haslen,$v);
	    } 
// 	    var_dump($redisObj->hGetAll(self::RONG_HAS_MSG_01));
// 	    var_dump($redisObj->lrange(self::RONG_SYS_ACCEPT_KEY,0,-1));
	    return $result ? true : false;
	    //return $this->sendSystemMsg($toUserId, $msg);
	}
	/**
	 * 融云发送系统消息
	 * @param $fromUserId 发送者uid
	 * @param $toUserId   接收者uid 多个uid 以英文  , 逗号隔开
	 * @param $content    消息内容
	 * @return bool
	 */
	public function sendSystemMsg($fromUserId,$toUserId,$objectName,$content, $pushContent = '', $pushData = '', $isPersisted, $isCounted){
	    $res = self::$_rCloudObj->Message()->PublishSystem($fromUserId, $toUserId,  $objectName, $content, $pushContent = '', $pushData = '', $isPersisted, $isCounted);
	    echo $fromUserId."-->".$toUserId." | ";var_dump($res);
	    $res = json_decode($res,true);
	    if($res['code'] == 200) return true;
	    else{
	        write_log(__CLASS__."第".__LINE__."行  错误信息：".$res,'rongSystemMsgError.'.date('Y-m'));
	        return false;
	    }
	}
	
	
	/**
	 * 发消息的任务 扔 redis 队列
	 * ---------------------
	 */
	const RONG_MSG_LIST_KEY = 'rongMsgListKey';
	const MSG_ERROR_CODE_01 = 2001;
	const MSG_ERROR_CODE_02 = 2002;
	const MSG_ERROR_CODE_03 = 2003;
	const MSG_ERROR_CODE_04 = 2004;
	const MSG_ERROR_CODE_05 = 2005;
	const MSG_CODE_01 = 2222;
	private $msgDes = [
	    self::MSG_ERROR_CODE_01 => "缺少发送人uid",
	    self::MSG_ERROR_CODE_02 => "缺少接受人uid",
	    self::MSG_ERROR_CODE_03 => "消息内容不能为空",
	    self::MSG_ERROR_CODE_04 => "发送类型不能为空",
	    self::MSG_ERROR_CODE_05 => "发消息 redis入队失败",
	    self::MSG_CODE_01 => "消息入队成功 下发中...",
	];
	
	public function addMsgList($fromUid,$toUid,$content,$sendCg,$extraCode=11111){ 
	    // 校验 参数
	    if($fromUid == '') render_error_json($this->msgDes[self::MSG_ERROR_CODE_01],self::MSG_ERROR_CODE_01);
	    if($toUid == '') render_error_json($this->msgDes[self::MSG_ERROR_CODE_02],self::MSG_ERROR_CODE_02);
	    if($content == '') render_error_json($this->msgDes[self::MSG_ERROR_CODE_03],self::MSG_ERROR_CODE_03);
	    if($sendCg == '') render_error_json($this->msgDes[self::MSG_ERROR_CODE_04],self::MSG_ERROR_CODE_04);
	    // ---------------
	    $msglist = [
	        'fromuid' =>$fromUid,
	        'touid'   =>$toUid,
	        'content' =>$content,
	        'sendCg'  =>$sendCg,
	        'extraCode'=>$extraCode
	    ];
	    $redisObj = RedisHelper::getInstance("huanpeng");
	    //$redisObj->delete(self::RONG_MSG_LIST_KEY);
	    $result = $redisObj->
	    rPush(self::RONG_MSG_LIST_KEY,json_encode($msglist));
	    if(in_array($extraCode, [10001,10003,10004,10005,10006,9999])){
	        $msglist = [
	            'fromuid' =>$fromUid,
	            'touid'   =>$toUid,
	            'content' =>"订单状态需更新",
	            'sendCg'  =>self::OBJECT_NAME_15,
	            'extraCode'=>10101
	        ];
	        $result2 = $redisObj->
	        rPush(self::RONG_MSG_LIST_KEY,json_encode($msglist));
	    }
	    //if($result) render_json($this->errDesc[self::MSG_CODE_01],self::MSG_CODE_01);
        if($result) {
            return true;
        }else{ 
        $content = "===========================\n".date("Y-m-d H:i:s")."  发送者uid：$fromUid-->接收者uid：$toUid\n发送内容：$content\n===========================\n\n";
        file_put_contents("/data/logs/due_sendMsgList.log",$content,FILE_APPEND);
	    }
	}
	//获取reids 消息队列
	public function getMsgList(){
	    $redisObj = RedisHelper::getInstance("huanpeng");
	    return $redisObj->lrange(self::RONG_MSG_LIST_KEY,0,-1);
	}
}