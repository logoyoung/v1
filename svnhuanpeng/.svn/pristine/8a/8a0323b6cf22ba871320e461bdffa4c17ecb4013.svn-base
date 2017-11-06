<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\due;

use system\RedisHelper;
use lib\due\DueAppeal;

class DueAppealService {

    public $libDueAppeal = null;
    public $_redis = null;

    /**
     * 获取redis资源
     * @return type
     */
    public function getRedis() {
        if (is_null($this->_redis)) {
            $this->_redis = RedisHelper::getInstance($this->redisConfig);
        }
        return $this->_redis;
    }

    public function getDueAppeal() {
        if (is_null($this->libDueAppeal)) {
            $this->libDueAppeal = new DueAppeal();
        }
        return $this->libDueAppeal;
    }

    /**
     * 
     * @param type $uid
     * @param type $orderid
     * @param type $content
     * @param type $pic
     */
    public function insertAppeal($uid, $orderid, $content, $pic) {
        $data = [
            'uid' => $uid,
            'order_id' => $orderid,
            'content' => $content,
            'pic' => $pic,
        ];
        return $this->getDueAppeal()->addAppeal($data);
    }

    /**
     * 
     * @param type $orderid
     * @param type $status
     * @param type $reply
     * @return type
     */
    public function updateAppealByOrderId($orderid, $status, $reply = '') {
        $data = [
            'status' => $status,
            'reply' => $reply,
            'order_id' => $orderid,
        ];
        return $this->getDueAppeal()->updateAppealByOrderId($data);
    }

}
