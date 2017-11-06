<?php

include '../../../include/init.php';

use service\user\UserDataService;
use service\due\DueOrderService;
use service\due\DueCertService;
use service\common\ApiCommon;

/**
 * 我的约玩订单列表
 * @author longgang chen <longgang@6.cn>
 * @date 2017-06-08 17:59:38
 * @copyright (c) 2017, 6.cn 
 * @version 1.0.0
 */
class myReceivedOrderList extends ApiCommon {

    //用户登录状态有误
    const ERROR_USER_LOGIN = 710001;
    //获取认证主播约玩接单列表失败
    const ERROR_MY_RECEIVED_ORDER_LIST = 710002;

    public static $errorMsg = [
        self::ERROR_USER_LOGIN => '用户登录状态有误',
        self::ERROR_MY_RECEIVED_ORDER_LIST => '获取认证主播约玩接单列表失败',
    ];
    private $_enc;
    private $_uid;
    private $_page;
    private $_size;

    //初始化
    private function _init() {
        $this->_uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
        $this->_enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $this->_size = isset($_POST['size']) ? trim($_POST['size']) : 5;
        if (!$this->_uid || !$this->_enc) {
            render_error_json(['LoginStatus' => 0]);
            exit;
        }
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    public function getMyReceivedOrderList() {
        $userDataService = new UserDataService();
        //$userDataService->setCaller('api:' . __FILE__);
        $userDataService->setUid($this->_uid);
        $userDataService->setEnc($this->_enc);

        $dueOrderService = new DueOrderService();
        $total = $dueOrderService->getTotalNumberByCertUid($this->_uid);
        if (empty($total)) {
            return ['total' => $total, 'list' => []];
        }
        //$dueOrderService->setCaller('api:' . __FILE__);
        $dueOrderService->setUid($this->_uid);
        $dueOrderService->setPage($this->_page);
        $dueOrderService->setSize($this->_size);
        //获取用户订单信息
        $orderData = $dueOrderService->getMyCertOrderList();
        if ($orderData) {
            //获取用户信息
            $ids = array_column($orderData, "cert_id");
            $uids = array_column($orderData, "uid");
            $userDataService->setUid($uids);
            $userData = $userDataService->batchGetUserInfo();
            $certObj = new DueCertService();
            $certInfo = $certObj->getCertInfoByCertIds($ids);
            foreach ($orderData as $k => $v) {
                $orderData[$k]['ctime'] = strtotime($orderData[$k]['ctime']);
                $orderData[$k]['otime'] = strtotime($orderData[$k]['otime']);
                $orderData[$k]['stime'] = strtotime($orderData[$k]['stime']);
                $orderData[$k]['rtime'] = strtotime($orderData[$k]['rtime']);
                $gameName = $certObj->getGameNameByGameId($certInfo[$v['cert_id']]['game_id']);
                $pic = explode(",", $certInfo[$v['cert_id']]['pic_urls']);
                $orderData[$k]['skill'] = $gameName;
                $orderData[$k]["skill_pic"] = service\common\UploadImagesCommon::getImageDomainUrl() . $pic[0];
                $orderData[$k]['canComment'] = $dueOrderService->getCanComment($this->_uid, $orderData[$k]);
                $orderData[$k]['userAction'] = $dueOrderService->getUserOrderAction(\lib\due\DueOrder::ORDER_USER_ROLE_TYPE_02_ANCHOR, $orderData[$k]['status']);
                $tmpMessage = $dueOrderService->getOrderStatusMessage($v['status'], \lib\due\DueOrder::ORDER_USER_ROLE_TYPE_02_ANCHOR, $orderData[$k]['reason']);
                $orderData[$k]['statusDes'] = $tmpMessage['order'];
                $orderData[$k]['reason'] = $tmpMessage['reason'];
                $orderData[$k]['uid'] = $userData[$orderData[$k]['uid']]['uid'];
                $orderData[$k]['nick'] = $userData[$orderData[$k]['uid']]['nick'];
                $orderData[$k]['pic'] = $userData[$orderData[$k]['uid']]['pic'];
                $orderData[$k]['income'] = $dueOrderService->myIncome($v);
                $orderData[$k]['unit'] = $certObj->getUnitName($orderData[$k]['unit']);
            }
        }
        //获取订单总数
        $dueOrderService->getPage();
        return ['total' => $total, 'list' => $orderData];
    }

    public function display() {
        $this->_init();

        $list = $this->getMyReceivedOrderList();

        if (!$list) {
            $code = self::ERROR_MY_RECEIVED_ORDER_LIST;
            $msg = self::$errorMsg[$code];
            $log = "Notice | error_code:{$code};msg:{$msg};uid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json([]);
            exit;
        }

        render_json($list);
    }

}

$obj = new myReceivedOrderList();
$obj->display();
