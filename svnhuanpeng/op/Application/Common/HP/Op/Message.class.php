<?php

// +----------------------------------------------------------------------
// | Op message
// +----------------------------------------------------------------------
// | Author: zwq 2017年5月17日
// +----------------------------------------------------------------------

namespace HP\Op;

class Message
{

    /**
     * 发送消息
     * @param type $sendId
     * @param type $title
     * @param type $message
     * @param type $type
     * @param type $db
     * @return int 成功返回1 失败返回0
     */
    public static function sendMessages($sendId, $title, $message, $type)
    {
        if (empty($sendId) || empty($title) || empty($message)) {
            return false;
        }
        if (!in_array($type, array(0, 1))) {
            return false;
        }
        //一对一
        if ($type == 0) {
            $addMsgRes = self::addMessagesText($title, $message, $type, $group = 1, $sendid = 0);
            $adduseRes = self::addUserMessages($sendId, $addMsgRes);
            if ($adduseRes) {
                $res = self::updateUserMailStatus($sendId);
            } else {
                $res = false;
            }
        }
        //一对多
        if ($type == 2) {
            $res = self::addMessagesText($title, $message, $type, $group = 2, $sendid = 0);
        }
        if ($res !== false) {
            $back = 1;
        } else {
            $back = 0;
        }
        return $back;
    }
    
    /**
     * 添加一条新的站内消息
     * @param string $title
     * @param string $message
     * @param object $db
     * @return string
     */
    public function addMessagesText($title, $message, $type, $group = '', $sendid = 0)
    {
        $db = D('Sysmessage');
        $data = array(
            'title' => $title,
            'msg' => $message,
            'type' => $type,
            'group' => $group,
            'sendid' => $sendid
        );
        $res = $db->add($data);
        return $res;
    }
    
    /**
     * 添加一条用户消息
     * @param type $uid
     * @param type $msgid
     * @param type $db
     * @return type
     */
    public function addUserMessages($uid, $msgid)
    {
        $db = D('Usermessage');
        $data = array(
            'uid' => $uid,
            'msgid' => $msgid
        );
        $res = $db->add($data);
        return $res;
    }
    
    /**
     * 更改用户站内信数量
     * @param int $uid
     * @param object $db
     * @return bool
     */
    public function updateUserMailStatus($uid)
    {
        $db = D('Useractive');
        $res = $db->where(['uid'=>$uid])->setInc('readsign');
        return $res;
    }
}
