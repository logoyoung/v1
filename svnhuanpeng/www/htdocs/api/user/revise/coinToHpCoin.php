<?php

/**
 * 金币兑换欢朋币
 * @since 2017-05-02 11:52:51
 */
include '../../../../include/init.php';

use lib\Finance;
use lib\Anchor;
use lib\AnchorExchange;
use lib\User;

class coinToHpCoin {

    private $_uid;
    private $_encpass;
    private $_number;
    private $_finance;
    private $_db;
    private $_desc = [];
    private $_anchor;
    private $_user;
    private $_exchange;

    public function __construct($uid = 0, $encpass = '', $number = 0) {
        $this->_uid = intval($uid);
        $this->_encpass = trim($encpass);
        $this->_number = intval($number);
        $this->_init();
    }

    /**
     * init
     */
    private function _init() {
        $this->_db = new DBHelperi_huanpeng();

        $this->_anchor = new Anchor($this->_uid, $this->_db);
        $this->_user = new User($this->_uid, $this->_db);
        $this->_finance = new Finance($this->_db);
        $this->_exchange = new AnchorExchange($this->_uid, '', $this->_db);
        $this->_check();
    }

    /**
     * 如果错误,则退出
     * @param type $code
     * @return boolean
     */
    public static function checkCode($code) {
        if ($code !== TRUE) {
            error2($code);
        }
        return TRUE;
    }

    /**
     * check
     * @return type void
     */
    protected function _check() {
        self::checkCode($this->_checkParam());
        self::checkCode($this->_checkNumber());
        self::checkCode($this->_checkLogin());
        self::checkCode($this->_checkIsAnchor());
        self::checkCode($this->_checkBalance());
        self::checkCode($this->_checkCompany());
            if($GLOBALS['env'] != 'DEV' && $GLOBALS['env'] != 'PRE' ){
            self::checkCode($this->_checkOnlineTime());
        }
        return;
    }

    /**
     * _checkNumber  主播收益“金币”可以兑换为“欢朋币”，兑换最低金额、最高金额（单位：金币）；50金币、1000金币
     * @return  mixed:int or bool 
     */
    protected function _checkNumber() {
        if ($this->_number >= 30 && $this->_number <= 1000) {
            return TRUE;
        } else {
            return -4105;
        }
    }

    /**
     * check param
     * @return  mixed:int or bool 
     */
    protected function _checkParam() {
        if ($this->_uid == 0 || empty($this->_encpass) || $this->_number <= 0) {
            return -4013;
        } else {
            return TRUE;
        }
    }
     /**
     * 修改上线时间
     * @return boolean
     */
    protected function _checkOnlineTime() {
        // 1496246400 2017-06-01 00:00:00
        if (time() < 1496246400) {
            return -4108;
        } else {
            return TRUE;
        }
    }


    /**
     * checkLogin
     * @return  mixed:int or bool 
     */
    protected function _checkLogin() {
        return CheckUserIsLogIn($this->_uid, $this->_encpass, $this->_db);
    }

    /**
     * check is  anchor
     * @return  mixed:int or bool 
     */
    protected function _checkIsAnchor() {
        return Anchor::isAnchor($this->_uid, $this->_db);
    }

    /**
     * 主播签约渠道检验   渠道15是平台的,渠道0是未签约的,其他的是经纪公司的
     * @todo 待确认
     * @return boolean
     */
    protected function _checkCompany() {
        $res = $this->_db->field("uid,cid")->where("uid={$this->_uid}")->limit(1)->select('anchor');
        if (false !== $res && !empty($res) && isset($res[0]['cid']) && $res[0]['cid'] != 15 && $res[0]['cid'] != 0) {
            return -4097;
        } else {
            return TRUE; //其它忽略
        }
    }

    /**
     * 判断余额是否足够
     * <pre>
     *  array(4) {
      ["hb"]=>
      int(324)
      ["gb"]=>
      float(770.32)
      ["hd"]=>
      int(196)
      ["gd"]=>
      float(0.9)
     */
    protected function _checkBalance() {
        $res = $this->_finance->getBalance($this->_uid);
        if (is_array($res) && isset($res['gb']) && $this->_number <= $res['gb']) {
            return TRUE;
        } else {
            return -5023;
        }
    }

    /**
     * (主播)金币兑换欢朋币
     */
    public function exchange() {
        //初始记录
        $this->_db->autocommit(FALSE);
        $anchorMoney = $this->_anchor->getAnchorProperty();
        $userMoney = $this->_user->getUserProperty();
        $this->_desc[] = "beforeCoin[" . $anchorMoney['coin'] . "]beforeHpcoin[" . $userMoney['coin'] . "]";
        //write log
        $otid = getOtid();
        $data = array(
            'otid' => $otid,
            'uid' => $this->_uid,
            'type' => Finance::EXC_GB_HB,
            'beforefrom' => $anchorMoney['coin'],
            'beforeto' => $userMoney['coin'],
            'afterfrom' => 0,
            'afterto' => 0,
            'number' => $this->_number,
            'message' => implode('', $this->_desc),
            'ctime' => date('Y-m-d H:i:s'),
            'status' => AnchorExchange::EXCHANGE_STATUS_01
        );
        $relog = $this->writeLog($data);
        //财务处理
        $res = $this->financialTransaction($otid);
        if ($res != false) {
            //更新数据
            $upAnchor = $this->_anchor->updateAnchorCoin($res['coin']);
            $upUser = $this->_user->updateUserHpCoin($res['hpcoin']);
            $this->_desc[] = "afterCoin[" . $res['coin'] . "]afterHpcoin[" . $res['hpcoin'] . "]";
            $updata = array(
                'afterfrom' => $res['coin'],
                'afterto' => $res['hpcoin'],
                'tid' => $res['tid'],
                'message' => implode('', $this->_desc),
                'status' => AnchorExchange::EXCHANGE_STATUS_02
            );
            $re = $this->writeLog($updata, $otid);
        }
        $doBool = false;
        if ($relog && $upAnchor && $upUser && $re) {
            $doBool = $this->_db->commit();
        } else {
            $this->_db->rollback();
        }
        $this->_db->autocommit(TRUE);
        //return
        if ($doBool) {
            succ($res);
        } else {
            error2(-4099);
        }
    }

    /**
     * 财务处理
     * @return type array
     */
    protected function financialTransaction($otid = 0) {
        $result = false;
        $res = $this->_finance->exchange($this->_uid, $this->_number, implode('', $this->_desc), $otid);
        if ($res != FALSE && is_array($res)) {
            $result = array(
                "coin" => $res['gb'],
                "hpcoin" => $res['hb'],
                "tid" => $res['tid'],
            );
        }
        return $result;
    }

    /**
     * 
     * @param type $data  更新数据
     * @param type $otid  唯一ID
     * @return type
     */
    public function writeLog($data, $otid = 0) {
        $otid = intval($otid);
        if ($otid) {
            $otid = $this->_exchange->update($otid, $data);
        } else {
            $otid = $this->_exchange->insert($data);
        }
        return $otid;
    }

    /**
     * 清理相应的缓存
     */
    public function __destruct() {
        
    }

}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$number = isset($_POST['number']) ? trim($_POST['number']) : 0;

$obj = new coinToHpcoin($uid, $encpass, $number);
$obj->exchange();
