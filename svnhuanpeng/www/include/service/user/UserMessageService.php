<?php

namespace service\user;

use service\common\AbstractService;
use lib\Message;

class UserMessageService extends AbstractService {

    private $_uid;

    public function setUid($uid) {
        $this->_uid = $uid;
    }

    private function _getLibUserMessage() {
        return new Message();
    }

    /**
     * 获取用户的消息数据
     * @param type $uid
     * @param type $page
     * @param type $limit
     * @param type $where
     * @return type
     */
    public function getMessages($uid, $page = 1, $limit = 5, $where = ['status' => 0]) {
        $uid = $uid ? $uid : $this->_uid;
        $messageContr = $this->_getLibUserMessage();
        $messageContr->setUid($uid);
        $total = $messageContr->getTotalById($uid, $where);
        $res = $messageContr->getUserMessageByWhere($uid, $limit, $page);
        foreach ($res as $value) {
            $ids[] = $value['msgid'];
        }
        $message = $messageContr->getMessageContentByIds($ids);
        $msglists = [];
        foreach ($message as $v) {
            $list['msgID'] = $v['id'];
            $list['head'] = DEFAULT_PIC;
            $list['title'] = $v['title'];
            $list['comment'] = $v['msg'];
            $list['ctime'] = strtotime($v['stime']);
            $list['overTime'] = time() - (strtotime($v['stime']));
            array_push($msglists, $list);
        }
        return ['total' => $total, 'list' => $msglists, 'page' => $page, 'pageTotal' => ceil($total / $limit)];
    }

}
