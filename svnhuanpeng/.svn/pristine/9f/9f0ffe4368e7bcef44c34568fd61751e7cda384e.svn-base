<?php

/**
 * API 基类
 * @author liupeng
 * @since 
 */

namespace service\common;

use service\user\UserDataService;
use service\cookie\CookieService;
use service\room\RoomManagerService;
use service\rule\TextService;
use lib\due\UpLoad;
use service\user\UserAuthService;

/**
 * api基类
 * init -> check -> set
 */
class ApiCommon {

    /**
     * 参数验证规则
     */
    const PARAM_RULE_01_NOT_NULL = 1;
    const PARAM_RULE_02_GT_0 = 2;

    /**
     * 以init开头的方法自动执行
     */
    const AUTO_EXEC_METHOD_PREFIX = 'init';
    const RESULT_DATA_STATUS_SUCCESS = 'success';
    const RESULT_DATA_STATUS_FAIL = 'fail';

    ### result

    public $resultData = [];

    ###[ userinfo ]###
    public $uid = 0;
    public $encpass = '';
    public $isAnchor = FALSE;
    public $roomId = 0;



    ###[ url ]###
    public $ref_url = 0;

    ###[ server ]###
    public $UserDataService;
    public $RoomManagerService;

    public function __construct() {
        $this->_init();
    }

    /**
     * 初始化
     */
    private function _init() {

        $this->_initUser();
        $this->_initService();
        ##[ 自定义初始化 ]##
        $methods = get_class_methods($this);
        foreach ($methods as $value) {
            if (strpos($value, self::AUTO_EXEC_METHOD_PREFIX) === 0) {
                $this->$value();
            }
        }
    }

    /**
     * 接口请求日志
     */
    public function initWriteLog() {
        if (isset($this->param)) {
            $user = ['_uid' => ['name' => 'uid', 'default' => ''], '_encpass' => ['name' => 'encpass', 'default' => '']];
            $param = array_merge($user, $this->param);
            $data = self::getParam($param) ?? [];
            $data = array_merge(['classname' => get_class($this)], $data);
            write_log(json_encode($data), 'Apicomment');
        }
    }

    private function _initUser() {
        $this->uid = self::getIn('uid', CookieService::getUid());
        $this->encpass = self::getIn('encpass', CookieService::getEnc());
    }

    /**
     * 初始化需要的服务
     */
    private function _initService() {
        $this->UserDataService = new UserDataService();
        $this->UserDataService->setUid($this->uid)->setEnc($this->encpass);
    }

    /**
     * 判断是否登录
     * @param bool $die true json退出;false返回bool值
     * @return type
     */
    public function checkIsLogin(bool $die = false) {
        //uid,encpass 校验登陆状态
        $auth = new UserAuthService();
        $auth->setUid($this->uid);
        $auth->setEnc($this->encpass);
        //校验encpass、用户是否被封禁
        $res = $auth->checkLoginStatus();
        if ($res !== true) {
            //获取校验结果
            $result = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg = $result['error_msg'];
            if ($die) {
                render_error_json($errorMsg, $errorCode, 2);
            }
        }
        return $res;
    }

    /**
     * 留言反垃圾
     * @param type $content 内容
     * @param type $channel 文本渠道
     */
    public static function textFilter($uid, $content, $channel = TextService::CHANNEL_DYNAMIC_COMMENT) {
        if (empty($content)) {
            return TRUE;
        }
        $textService = new TextService();
        $textService->setCaller('api:' . __FILE__ . ';line:' . __LINE__);
        $port = 0;
        $textService->addText($content, $uid, $channel)->setIp(fetch_real_ip($port));
        //反垃圾过滤
        if (!$textService->checkStatus()) {
            render_error_json('内容包含敏感内容', -8010, 2);
        }
    }

    /**
     * 角色判断
     */
    public function checkIsAnchor() {
        if ($this->UserDataService->isAnchor()) {
            $this->isAnchor = TRUE;
            $this->RoomManagerService = new RoomManagerService();
            $this->RoomManagerService->setUid($this->uid);
        }
    }

    /**
     * 获取指定的参数
     * @param array $param  获取参数
     * <br />
     * <pre>
     * [
     *      'key'=>['name'=>'uid','default'=>'0','rule' => ApiCommon::PARAM_RULE_01_NOT_NULL ],
     * ]
     * </pre>
     * 
     * @param type $isCheckRule  是否检查参数
     * @param type $mustPOST   是否限制为POST参数
     * @return array
     */
    public static function getParam(array $param, $isCheckRule = FALSE, $mustPOST = TRUE): array {
        $res = [];
        foreach ($param as $key => $value) {
            if ($mustPOST) {
                $res[$key] = self::getIn('post.' . $value['name'], $value['default']);
            } else {
                $res[$key] = self::getIn($value['name'], $value['default']);
            }
            if (isset($value['rule'])) {
                $checkData[$value['name']] = $res[$key];
                $rule[$value['name']] = $value['rule'];
            }
        }
        if ($isCheckRule && !empty($rule)) {
            self::checkParams($checkData, $rule);
        }
        return $res;
    }

    /**
     * 验证必要参数以及状态
     * @param $chackData @待验证数据
     * @param $checkRule @验证数据规则
     * @return bool
     */
    public static function checkParams($chackData, $checkRule) {
        foreach ($checkRule as $key => $rule) {
            $paraKey = array_key_exists($key, $chackData);
            if ($paraKey) {
                //规则
                switch ($rule) {
                    //不能为空
                    case self::PARAM_RULE_01_NOT_NULL :
                        if (empty($chackData[$key])) {
                            $code = -993;
                            $msg = errorDesc($code) . '[' . $key . ']';
                            render_error_json($msg, $code, 1);
                        }
                        break;
                    case self::PARAM_RULE_02_GT_0 :
                        if ($chackData[$key] <= 0) {
                            $code = -993;
                            $msg = errorDesc($code) . '[' . $key . ']';
                            render_error_json($msg, $code, 1);
                        }
                        break;
                    default:
                        return true;
                }
            } else {
                $code = -2004;
                $msg = errorDesc($code) . '[' . $key . ']';
                render_error_json($msg, $code, 1);
            }
        }
        return true;
    }

    /**
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * getIn('id',0); 获取id参数 自动判断get或者post
     * getIn('post.name','','htmlspecialchars'); 获取$_POST['name']
     * getIn('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法   默认是htmlspecialchars
     * @param mixed $datas 要获取的额外数据源;从本数据z
     * @return mixed
     */
    public static function getIn($name, $default = '', $filter = null, $datas = null) {
        static $_PUT = null;
        if (strpos($name, '/')) { // 指定修饰符
            list($name, $type) = explode('/', $name, 2);
        } elseif (TRUE) { // 默认强制转换为字符串
            $type = 's';
        }
        if (strpos($name, '.')) { // 指定参数来源
            list($method, $name) = explode('.', $name, 2);
        } else { // 默认为自动判断
            $method = 'param';
        }
        switch (strtolower($method)) {
            case 'get' :
                $input = & $_GET;
                break;
            case 'post' :
                $input = & $_POST;
                break;
            case 'put' :
                if (is_null($_PUT)) {
                    parse_str(file_get_contents('php://input'), $_PUT);
                }
                $input = $_PUT;
                break;
            case 'param' :
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input = $_POST;
                        break;
                    case 'PUT':
                        if (is_null($_PUT)) {
                            parse_str(file_get_contents('php://input'), $_PUT);
                        }
                        $input = $_PUT;
                        break;
                    default:
                        $input = $_GET;
                }
                break;
            case 'path' :
                $input = array();
                if (!empty($_SERVER['PATH_INFO'])) {
                    $depr = C('URL_PATHINFO_DEPR');
                    $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
                }
                break;
            case 'request' :
                $input = & $_REQUEST;
                break;
            case 'session' :
                $input = & $_SESSION;
                break;
            case 'cookie' :
                $input = & $_COOKIE;
                break;
            case 'server' :
                $input = & $_SERVER;
                break;
            case 'globals' :
                $input = & $GLOBALS;
                break;
            case 'data' :
                $input = & $datas;
                break;
            default:
                return null;
        }
        if ('' == $name) { // 获取全部变量
            $data = $input;
            $filters = isset($filter) ? $filter : 'htmlspecialchars'; // 将特殊字符转换为HTML实体
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                }
                foreach ($filters as $filter) {
                    $data = array_map_recursive($filter, $data); // 参数过滤
                }
            }
        } elseif (isset($input[$name])) { // 取值操作
            $data = $input[$name];
            $filters = isset($filter) ? $filter : 'htmlspecialchars'; // 将特殊字符转换为HTML实体
            if ($filters) {
                if (is_string($filters)) {
                    if (0 === strpos($filters, '/')) {
                        if (1 !== preg_match($filters, (string) $data)) {
                            // 支持正则验证
                            return isset($default) ? $default : null;
                        }
                    } else {
                        $filters = explode(',', $filters);
                    }
                } elseif (is_int($filters)) {
                    $filters = array($filters);
                }

                if (is_array($filters)) {
                    foreach ($filters as $filter) {
                        if (function_exists($filter)) {
                            $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                        } else {
                            $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                            if (false === $data) {
                                return isset($default) ? $default : null;
                            }
                        }
                    }
                }
            }
            if (!empty($type)) {
                switch (strtolower($type)) {
                    case 'a': // 数组
                        $data = (array) $data;
                        break;
                    case 'd': // 数字
                        $data = (int) $data;
                        break;
                    case 'f': // 浮点
                        $data = (float) $data;
                        break;
                    case 'b': // 布尔
                        $data = (boolean) $data;
                        break;
                    case 's':   // 字符串
                    default:
                        $data = (string) $data;
                }
            }
        } else { // 变量默认值
            $data = isset($default) ? $default : null;
        }
        if (is_array($data)) {
            $data = xss_clean($data);
        }
//        is_array($data) && array_walk_recursive($data, 'xss_clean');
        return $data;
    }

    ###[help]###

    public function __toString() {
        return __FILE__ . ':' . __CLASS__;
    }

    /**
     * 获取格式【今天 09:20】时间字符串
     * @param int $time
     * @return type
     */
    public static function getFormatDayTime(int $time, $format = FALSE, $chinaFormat = FALSE) {
        if ($format) {
            $his = date(" H:i", $time);
            $day = intval($time / 86400);
            $today = intval(time() / 86400);
            if ($today == $day) {
                $pre = '今天';
            } else if ($today + 1 == $day) {
                $pre = '明天';
            } else if ($today + 2 == $day) {
                $pre = '后天';
            } else if ($chinaFormat) {
                $pre = date("Y年m月d日", $time);
            } else {
                $pre = date("Y-m-d", $time);
            }
            $res = $pre . $his;
        } else {
            $res = date("Y-m-d H:i:s", $time);
        }
        return $res;
    }

    /**
     * 判断内容长度
     * @param type $message   内容
     * @param type $maxLenth  长度
     * @param type $encoding  字符集
     * @param type $report    是否展示错误
     * @return boolean
     */
    public static function checkStringLength($message, $maxLenth = 50, $report = false, $encoding = 'latin1') {
        $legth = mb_strlen($message, $encoding);
        if ($legth >= $maxLenth) {
            if ($report) {
                $code = -100;
                $msg = "输入框内容超过最大字数";
                render_error_json($msg, $code, 2);
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }

}
