<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\pack;

use service\common\AbstractService;
use lib\pack\BackpackLog;
use Exception;

/**
 * 背包系统:背包属性.给我100格的大背包...
 */
class BackpackLogService extends AbstractService {

    const PACK_LOG_STATUS_DEFAULT = 0; //默认是失败的
    const PACK_LOG_STATUS_SUCCESS = 1;
    const PACK_LOG_STATUS_CLOSED = -1; //关闭

    public function addpacklog($uid, $memo, $type, $sourceid, $goodsid, $num) {
        $m = new BackpackLog();
        $data = [
            'uid'      => $uid,
            'memo'     => $memo,
            'type'     => $type,
            'sourceid' => $sourceid,
            'goodsid'  => $goodsid,
            'num'      => $num,
        ];
        return $m->addlog($data);
    }

    /**
     * 更新
     * @param type $otid
     * @param type $status
     * @return type
     */
    public function updateStatus($otid, $status) {
        $m = new BackpackLog();
        $data = [
            'otid'   => $otid,
            'status' => $status,
        ];
        return $m->updateLogStatusByOtid($data);
    }

    public function getRowByOtid($otid) {
        $m = new BackpackLog();
        return $m->getRowDataByOtid($otid);
    }

    public function getRowBySourceid($sourceid) {
        $m = new BackpackLog();
        $res = $m->getDataBySourceid($sourceid);
        return empty($res) ? [] : $res;
    }

}
