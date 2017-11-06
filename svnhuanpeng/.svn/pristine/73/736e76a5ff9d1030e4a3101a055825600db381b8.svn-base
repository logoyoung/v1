<?php
namespace service\common;
use lib\user\SystemLog;

/**
 * db system log
 */

class LogDbService
{

    //封禁（登陆）
    const LOG_DISABLE_LOGIN      = 10;
    //禁言
    const LOG_TYPE_SEILENCED     = 20;
    //禁播
    const LOG_TYPE_DISABLE_LIVE  = 30;
    //公共日志
    const LOG_TYPE_COMMON        = 200;

    public static function log($uid, $content, $type = self::LOG_TYPE_COMMON, $acUid = 0)
    {
        $uid = (int) $uid;
        if(!$uid || !$content)
        {
            return false;
        }

        $type    = $type  ? (int) $type  : self::LOG_TYPE_COMMON;
        $acUid   = $acUid ? (int) $acUid : $uid;
        $content = is_string($content) ? $content : hp_json_encode($content);
        $dbLog   = new SystemLog();

        if(!$dbLog->add($uid,$type,$acUid,$content))
        {
            write_log("error|uid:{$uid}; type:{$type}; acUid:{$acUid};content:{$content}", 'write_log_to_db_error');
            return false;
        }

        return true;
    }

}