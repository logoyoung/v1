<?php

include '../../../include/init.php';

use lib\Anchor;

/**
 * 主播信息接口
 */
class anchorInfo {

    public $db;
    public $uid;
    public $enc;
    public $data;
    public $anchorObj;

    public function __construct() {
        $this->db = new DBHelperi_huanpeng();
        $this->uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
        $this->enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_init();
    }

    private function _init() {
        //check param
        if (empty($this->uid) || empty($this->enc)) {
            error2(-4013);
        }
        //check login
        $this->anchorObj = new Anchor($this->uid, $this->db);
        $code = $this->anchorObj->checkStateError($this->enc);
        if ($code !== true) {
            error2($code, 2);
        }
        //check anchor
        $code = $this->anchorObj->isAnchor($this->uid, $this->db);
        if ($code !== true) {
            error2($code, 2);
        }
    }

    /**
     * 输出
     */
    public function display() {
        $this->_setResultData();
        $this->_setLiveLength();
        succ($this->data);
    }

    /**
     * 设置返回的用户信息
     */
    private function _setResultData() {
        $userInfo = $this->anchorObj->getUserInfo();
        $this->data['nick'] = $userInfo['nick'];
        $this->data['pic'] = $userInfo['pic'];

        $this->data['roomID'] = $this->anchorObj->getRoomID();
        $level = $this->anchorObj->getAnchorLevel(); //'level' => 0, 'integral' => 0
        $this->data['level'] = $level['level'];

        $cid = getCidByUid($this->uid, $this->db);
        if ($cid == 15) {
            $this->data['type'] = 1;
            $this->data['salary'] = BASE_SALARY;
        } else {
            $this->data['type'] = 0;
            $this->data['salary'] = 0;
        }
        $this->data['integral'] = $level['integral'] - $this->anchorObj->getIntegralByAnchorLevel($this->data['level'] - 1);
        $this->data['levelIntegral'] = $this->anchorObj->getIntegralByAnchorLevel($this->data['level']) - $this->anchorObj->getIntegralByAnchorLevel($this->data['level'] - 1);
        $this->data['gapIntegral'] = ceil($this->data['levelIntegral'] - $this->data['integral']);
        $this->data['fansCount'] = $this->anchorObj->getFollowNumber();
    }

    /**
     * 获取直播时长 单位:秒
     * @return int
     */
    private function _getLiveTime() {
        $res = $this->db->field('sum(length)  as length')->where("uid ={$this->uid} ")->select('live_length');
        if (false !== $res) {
            if ($res) {
                return $res[0]['length'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * 设置返回时长
     */
    private function _setLiveLength() {
        $time = $this->_getLiveTime();
        $d = floor($time / 3600 / 24);
        $h = floor(($time % (3600 * 24)) / 3600);  //%取余
        $m = floor(($time % (3600 * 24)) % 3600 / 60);
        $this->data['timeLength'] = (24 * $d) + $h . '小时' . $m . '分钟';
    }

}

$do = new anchorInfo();
$do->display();


