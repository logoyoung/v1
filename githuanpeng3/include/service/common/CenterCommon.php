<?php

/**
 * 个人中心基类:所有数据在服务中获取;禁止操作lib; init -> check -> set
 * 1 个人中心如果没有登录,跳转到登录页
 * 2 所有给页面赋值的方法assign开头,会自动调用
 * 
 * @author liupeng
 * @since 
 */

namespace service\common;

use service\common\PcCommon;
use service\user\UserDataService;
use service\cookie\CookieService;
use service\room\RoomManagerService;

/**
 * 个人中心基类
 * init -> check -> set
 */
class CenterCommon extends PcCommon {

    //function
    const METHOD_PREFIX = 'assign';
    // user
    const CENTER_SIDE_INFORMATION = 'information';
    const CENTER_SIDE_MESSAGE = 'message';
    const CENTER_SIDE_FOLLOW = 'follow';
    const CENTER_SIDE_BEANCHOR = 'beanchor';
    const CENTER_SIDE_GIFT_RECORD = 'giftRecord';
    const CENTER_SIDE_RECHARGE = 'recharge';
    // anchor
    const CENTER_SIDE_HOME_PAGE = 'homepage';
    const CENTER_SIDE_ZONE = 'zone';
    const CENTER_SIDE_PROPERTY = 'property';
    const CENTER_SIDE_ROOM_ADMIN = 'roomadmin';

    /**
     * 全部菜单内容
     * @var type 
     */
    public static $side_bar = [
        self::CENTER_SIDE_INFORMATION => '个人资料',
        self::CENTER_SIDE_MESSAGE => '我的消息',
        self::CENTER_SIDE_FOLLOW => '我的关注',
        self::CENTER_SIDE_BEANCHOR => '我做主播',
        self::CENTER_SIDE_GIFT_RECORD => '送礼记录',
        self::CENTER_SIDE_RECHARGE => '充值',
        self::CENTER_SIDE_HOME_PAGE => '主播资料',
        self::CENTER_SIDE_ZONE => '我的空间',
        self::CENTER_SIDE_PROPERTY => '我的收益',
        self::CENTER_SIDE_ROOM_ADMIN => '我的房管',
    ];

    /**
     * 默认菜单显示内容
     * @var type 
     */
    public static $side_bar_show = [
        self::CENTER_SIDE_INFORMATION => TRUE,
        self::CENTER_SIDE_MESSAGE => TRUE,
        self::CENTER_SIDE_FOLLOW => TRUE,
        self::CENTER_SIDE_BEANCHOR => FALSE,
        self::CENTER_SIDE_GIFT_RECORD => TRUE,
        self::CENTER_SIDE_RECHARGE => TRUE,
        self::CENTER_SIDE_HOME_PAGE => FALSE,
        self::CENTER_SIDE_ZONE => FALSE,
        self::CENTER_SIDE_PROPERTY => FALSE,
        self::CENTER_SIDE_ROOM_ADMIN => FALSE,
    ];
    ###[  side init ]###
    public $selectedMenu = 'information';
    public $sidebarCenter = [];
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
        parent::__construct();
        $this->_init();
    }

    /**
     * 个人中心页面初始化
     */
    private function _init() {
        
        $this->_initUser();
        $this->_initService();
        $this->_setSidebarShow();
        $this->setSidebarSelect();
        ##[ assign ]##
        $methods = get_class_methods($this);
        foreach ($methods as $value) {
            if (strpos($value, self::METHOD_PREFIX) === 0) {
                $this->$value();
            }
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
     * 角色判断
     */
    private function checkIsAnchor() {
        if ($this->UserDataService->isAnchor()) {
            $this->isAnchor = TRUE;
            $this->RoomManagerService = new RoomManagerService();
            $this->RoomManagerService->setUid($this->uid);
        }
    }

    /**
     * 设置显示的菜单栏
     */
    private function _setSidebarShow() {
        $sidebarCenter[self::CENTER_SIDE_INFORMATION]['url'] = WEB_PERSONAL_URL;
        $sidebarCenter[self::CENTER_SIDE_INFORMATION]['text'] = '个人资料';
        $sidebarCenter[self::CENTER_SIDE_INFORMATION]['icon'] = 'anchorIcon';
        $sidebarCenter[self::CENTER_SIDE_INFORMATION]['li-class'] = 'li-personal';

        $sidebarCenter[self::CENTER_SIDE_MESSAGE]['url'] = WEB_PERSONAL_URL . "pm";
        $sidebarCenter[self::CENTER_SIDE_MESSAGE]['text'] = '我的消息';
        $sidebarCenter[self::CENTER_SIDE_MESSAGE]['icon'] = 'msgIcon';
        $sidebarCenter[self::CENTER_SIDE_MESSAGE]['li-class'] = 'li-msg';

        $sidebarCenter[self::CENTER_SIDE_FOLLOW]['url'] = WEB_PERSONAL_URL . "follow";
        $sidebarCenter[self::CENTER_SIDE_FOLLOW]['text'] = '我的关注';
        $sidebarCenter[self::CENTER_SIDE_FOLLOW]['icon'] = 'followIcon';
        $sidebarCenter[self::CENTER_SIDE_FOLLOW]['li-class'] = 'li-follow';

        if ($this->isAnchor) {

            $sidebarCenter[self::CENTER_SIDE_HOME_PAGE]['url'] = WEB_PERSONAL_URL . "homepage";
            $sidebarCenter[self::CENTER_SIDE_HOME_PAGE]['text'] = '主播资料';
            $sidebarCenter[self::CENTER_SIDE_HOME_PAGE]['icon'] = 'homePageIcon';
            $sidebarCenter[self::CENTER_SIDE_HOME_PAGE]['li-class'] = 'li-homepage';
        } else {
            $sidebarCenter[self::CENTER_SIDE_BEANCHOR]['url'] = WEB_PERSONAL_URL . 'beanchor.php';
            $sidebarCenter[self::CENTER_SIDE_BEANCHOR]['text'] = '我做主播';
            $sidebarCenter[self::CENTER_SIDE_BEANCHOR]['icon'] = 'beAnchorIcon';
            $sidebarCenter[self::CENTER_SIDE_BEANCHOR]['li-class'] = 'li-beanchor';
        }
        $sidebarCenter[self::CENTER_SIDE_GIFT_RECORD]['url'] = WEB_PERSONAL_URL . 'giftRecord';
        $sidebarCenter[self::CENTER_SIDE_GIFT_RECORD]['text'] = '送礼记录';
        $sidebarCenter[self::CENTER_SIDE_GIFT_RECORD]['icon'] = 'giftHistoryIcon';
        $sidebarCenter[self::CENTER_SIDE_GIFT_RECORD]['li-class'] = 'li-gift';

        if ($this->isAnchor) {
            $sidebarCenter[self::CENTER_SIDE_ZONE]['url'] = WEB_PERSONAL_URL . 'zone';
            $sidebarCenter[self::CENTER_SIDE_ZONE]['text'] = '我的空间';
            $sidebarCenter[self::CENTER_SIDE_ZONE]['icon'] = 'zoneIcon';
            $sidebarCenter[self::CENTER_SIDE_ZONE]['li-class'] = 'li-zone';

            $sidebarCenter[self::CENTER_SIDE_PROPERTY]['url'] = WEB_PERSONAL_URL . 'property';
            $sidebarCenter[self::CENTER_SIDE_PROPERTY]['text'] = '我的收益';
            $sidebarCenter[self::CENTER_SIDE_PROPERTY]['icon'] = 'myCoinIcon';
            $sidebarCenter[self::CENTER_SIDE_PROPERTY]['li-class'] = 'li-property';

            $sidebarCenter[self::CENTER_SIDE_ROOM_ADMIN]['url'] = WEB_PERSONAL_URL . 'roomadmin.php';
            $sidebarCenter[self::CENTER_SIDE_ROOM_ADMIN]['text'] = '我的房管';
            $sidebarCenter[self::CENTER_SIDE_ROOM_ADMIN]['icon'] = 'roomManageIcon';
            $sidebarCenter[self::CENTER_SIDE_ROOM_ADMIN]['li-class'] = 'li-admin';
        }

        $sidebarCenter[self::CENTER_SIDE_RECHARGE]['url'] = WEB_PERSONAL_URL . 'recharge.php';
        $sidebarCenter[self::CENTER_SIDE_RECHARGE]['text'] = '充值';
        $sidebarCenter[self::CENTER_SIDE_RECHARGE]['icon'] = 'rechargeIcon';
        $sidebarCenter[self::CENTER_SIDE_RECHARGE]['li-class'] = 'li-recharge';

        $sidebar_beAnchor['url'] = $sidebarCenter[self::CENTER_SIDE_BEANCHOR]['url'];
        $sidebar_beAnchor['text'] = "我要做主播";

        if ($this->isAnchor) {
            $roomid = $this->RoomManagerService->getRoomid();
            $this->roomId = $roomid;
            $sidebar_beAnchor['url'] = WEB_ROOT_URL . $roomid;
            $sidebar_beAnchor['text'] = "进入直播间";
        }
        $sidebar_beAnchor['icon'] = $this->isAnchor ? 'beAnchorIcon-anchor' : 'beAnchorIcon-user';
        $this->sidebarCenter = $sidebarCenter;
        $this->smarty->assign('sidebar', $sidebarCenter);
        $this->smarty->assign('sidebar_beAnchor', $sidebar_beAnchor);
    }

    /**
     * 浏览记录设置
     */
    private function _setBrowseLog() {
        $this->ref_url = self::getIn('get.ref_url', self::getIn('server.HTTP_REFERER'));
    }

    /**
     * 设置选中的菜单
     */
    public function setSidebarSelect() {


        $this->sidebarCenter;
        $active = $this->selectedMenu;
        array_walk($this->sidebarCenter, function(&$item, $key) use($active) {
            if ($key == $active) {
                $item['active'] = 'active';
            } else {
                $item['active'] = '';
            }
        });
        $this->smarty->assign('sidebar', $this->sidebarCenter);
    }

    /**
     * 没登录去登陆
     */
    public function displayLogin() {
        /**
         * @todo 去登陆
         */
        $this->smarty->display('index.tpl');
    }

    /**
     * 非法请求跳转
     */
    public function displayError() {
        /**
         * @todo 去哪里?
         */
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
        is_array($data) && array_walk_recursive($data, 'xss_clean');
        return $data;
    }

    ###[help]###

    public function __toString() {
        return __FILE__ . ':' . __CLASS__;
    }

}
