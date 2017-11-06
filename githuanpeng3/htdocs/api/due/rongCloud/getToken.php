<?php

/**
 * Created by PhpStorm.
 * User: yalong
 * Date: 17/6/6
 * Time: 下午4:28
 */



//请求参数   uid,encpass
//返回参数  token

include '../../../../include/init.php';
use service\due\rongCloud\RongUserService;
use lib\User;
use GuzzleHttp\json_encode;
use service\user\UserAuthService;

class GetToken{
    private $uid;
    private $encpass;

    const ERROR_CODE_01 = -1000;
    const ERROR_CODE_02 = -1001;
    const ERROR_CODE_03 = -1013;
    private $errDes = [
        -1000 => '缺少 uid 参数',
        -1001 => '缺少 encpass 参数',
        -1013 => 'ecnpass验证错误',
    ];

    public function __construct($uid,$encpass){
        $uid =='' ? render_error_json($this->errDes[self::ERROR_CODE_01],self::ERROR_CODE_01) : '';
        $encpass =='' ? render_error_json($this->errDes[self::ERROR_CODE_02],self::ERROR_CODE_02) : '';
        $this->uid = $uid;
        $this->encpass = $encpass;
    }
    public function returnToken(){
        $code = $this->regUser();
        if($code!==true){
            $desc=$this->errDes[self::ERROR_CODE_03];
            render_error_json($desc,self::ERROR_CODE_03);
            exit;
        }
        $userService = new RongUserService($this->uid);
        $token = $userService->getToken();
        if(!is_array($token)) render_json($token);
        else {
            render_error_json($token['data'],$token['code']);
        }
    }
    private function regUser(){
        $auth = new UserAuthService();
        $auth->setUid($this->uid);
        $auth->setEnc($this->encpass);
        return $auth->checkLoginStatus();
    }
}
$uid = intval($_POST['uid']);
$encpass = $_POST['encpass'];

// render_json("2007");

$getToken = new GetToken($uid , $encpass);
$getToken->returnToken();









