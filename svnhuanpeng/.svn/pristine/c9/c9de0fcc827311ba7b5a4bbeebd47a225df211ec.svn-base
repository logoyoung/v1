<?php
//require_once 'redis.class.php';
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/22
 * Time: 上午10:05
 */

/**
 * 类使用说明
 *
 * 在发送短信的时候 首先调用静态方法sendMobileMSg::createCodeId生成一条记录
 *
 */


/**
 * Class sendMobileMsg
 *
 * 消息发送类
 */
class sendMobileMsg
{
    const appid = 102;
    const key = 'ekxklhuangTSDpengfkjekldc';

    const t_register = 1; //注册
    const t_apply = 2; //申请主播
    const t_getBackPasswd = 3; //找回密码
    const t_apply_filed = 4; //申请失败
    const t_apply_success = 5; //申请成功
    const t_bindobile = 6; //邦定手机


    const sendSuccStatus = 1;

    const tableName = "send_mobileMsg_record";

    const dev_sendURL = 'http://dev.liveuser.6.cn/api/pubSendSmsCodeApi.php?';
    const pro_sendURL = 'http://liveuser/api/pubSendSmsCodeApi.php?';

    const dev_callBackURL = 'http://dev.liveuser.6.cn/api/callBackSendSmsCode.php?';
    const pro_callBackURL = 'http://liveuser/api/callBackSendSmsCode.php?';

    const getBalanceURL = 'http://liveuser/api/getBalanceInfo.php?appid=102';

    const status_send = 0;
    const status_back = 1;

    const timeLimit = 900;

    const sendTimeLimit = 3;

    const url_get_failed = -4070;

    const redis_msgBalance = 'mobileMsgBalance';

    static $db = null;
    static $redis = null;

    static $mobileCode = 0;
    static $mobile = 0;
    static $sendType = 0;
    static $codeid = 0;


    public static function setDb($db = null)
    {
        if (!static::$db)
            if ($db)
                static::$db = $db;
            else
                static::$db = new DBHelperi_huanpeng();
    }

    public static function setRedis(RedisHelp $redis = null)
    {
        if (!static::$redis)
            if ($redis)
                static::$redis = $redis;
            else
                static::$redis = new RedisHelp();
    }

    public static function canSendMsg($type, $mobile, $db, $limit = 0)
    {
        self::setDb($db);
        if (!$limit)
            $limit = self::sendTimeLimit;

        $stime = date("Y-m-d") . " 00:00:00";
        $etime = date("Y-m-d") . " 23:59:59";
        $sql = "select count(*)  as count from " . self::tableName . " where type=$type and mobile='$mobile' and ctime between '$stime' and '$etime' and errorNo=" . self::sendSuccStatus;
        $res = static::$db->query($sql);
        if (!$res) {
            //logs
            return false;
        }

        $row = $res->fetch_assoc();
        $nums = (int)$row['count'];
        if ($nums > $limit) {
            return false;
        }

        return true;
    }

    public static function sendMsg($codeid, DBHelperi_huanpeng $db = null, RedisHelp $redis = null, $subMsg = '')
    {
        static::setDb($db);
        static::setRedis($redis);

        $data = array(
            'appid' => self::appid,
            'codeid' => (int)$codeid,
            'mobile' => static::$mobile,
            'type' => static::$sendType,
            'sms' => static::getSms(static::$sendType, static::$mobileCode, $subMsg)
        );

        $sign = static::createSign($data);
        $data['sign'] = $sign;

        $str = http_build_query($data);

        $env = strtoupper($GLOBALS['env']);
        if ($env != "PRO") {
            $send_url = self::dev_sendURL . $str;
        } else {
            $send_url = self::pro_sendURL . $str;
        }
        $ret = file_get_contents($send_url);
        if ($ret) {
            $ret = json_decode($ret, true);
            static::setRecordError($codeid, $ret['resuNo'], static::status_send);
            if ($ret['resuNo'] == 1) {

                static::$redis->set(static::return_redis_key(static::$sendType, static::$mobile), $codeid, self::timeLimit);
                return true;
            } else {
                return false;
            }
        }

        static::setRecordError($codeid, self::url_get_failed, static::status_send);

        return false;
    }

    /**
     * 用于统计回调，统计验证码是否发送成功
     *
     * @param $codeid
     * @param DBHelperi_huanpeng|null $db
     */
    public static function sendMsgCallBack($codeid, DBHelperi_huanpeng $db = null)
    {
        static::setDb($db);
        $data = array(
            'appid' => self::appid,
            'codeid' => (int)$codeid,
            'tm' => (int)time()
        );
        $sign = static::createSign($data);
        $data['sign'] = $sign;

        $str = http_build_query($data);

        $env = strtoupper($GLOBALS['env']);
        if ($env == 'DEV') {
            $send_url = self::dev_callBackURL . $str;
        } else {
            $send_url = self::pro_callBackURL . $str;
        }

        $ret = file_get_contents($send_url);
        if ($ret) {
            $ret = json_decode($ret, true);

            static::setRecordError($codeid, $ret['resuNo'], static::status_back);
        } else {
            static::setRecordError($codeid, self::url_get_failed, static::status_back);
        }
    }

    public static function getRecordInfo($codeid, $db = null)
    {
        self::setDb($db);
        $sql = "select id,code,mobile,type,ctime from " . self::tableName . " where id=$codeid";
        $res = static::$db->query($sql);
        if (!$res) {
            //logs
            return false;
        }
        $row = $res->fetch_assoc();
        if ($row['id']) {
            static::$codeid = $codeid;
            static::$mobile = $row['mobile'];
            static::$mobileCode = $row['code'];
            static::$sendType = $row['type'];
            return $row;
        }

        return false;
    }

    public static function checkSuccess($type, $mobile, $code, DBHelperi_huanpeng $db = null, RedisHelp $redis = null, $timeout = 0, $autoClear = true)
    {
        self::setDb($db);
        self::setRedis($redis);
        $codeid = static::getCache($type, $mobile);
        if (!$codeid)
            return false;

        if (!$timeout) $timeout = self::timeLimit;
        $info = static::getRecordInfo($codeid, $db);

        if (false == $info || $info['mobile'] != $mobile || $info['code'] != $code || (time() - strtotime($info['ctime'])) > $timeout) {
            return false;
        }

        //clear redis
        if ($autoClear)
            static::clearCache($type, $mobile, $redis);
        return true;
    }

    public static function getCache($type, $mobile, RedisHelp $redis = null)
    {
        self::setRedis($redis);
        $ckey = static::return_redis_key($type, $mobile);
        $codeid = static::$redis->get($ckey);
        return $codeid;
    }

    public static function clearCache($type, $mobile, RedisHelp $redis = null)
    {
        static::setRedis($redis);
        $redis_keys = static::return_redis_key($type, $mobile);
        static::$redis->del($redis_keys);
    }

    public static function return_redis_key($type, $mobile)
    {
        return "sendMsgRedis:$type:$mobile";
    }


    public static function createCodeId($type, $mobile, $businessid, $db = null, $code = 0)
    {
        static::setDb($db);
        $port = '';
        $ip = ip2long(fetch_real_ip($port));
        if (!$code) {
            $code = static::createMobileCode();
        }

        $data = array(
            'type' => $type,
            'mobile' => $mobile,
            'businessid' => $businessid,
            'code' => $code,
            'ip' => $ip,
            'port' => $port
        );
        if (static::$db->insert(self::tableName, $data)) {
            static::$mobileCode = $code;
            static::$mobile = $mobile;
            static::$sendType = $type;
            static::$codeid = static::$db->insertID;
            return static::$codeid;
        } else {
            return false;
        }
    }

    public static function setRecordError($codeid, $errno, $status, $db = null)
    {
        self::setDb($db);
        $data = array(
            'status' => $status,
            'errorNo' => $errno,
            'etime' => date('Y-m-d H:i:s')
        );
        static::$db->where("id=$codeid")->update(self::tableName, $data);
    }

    public static function createMobileCode($length = 6)
    {
		$min = '1'.str_repeat('0', $length-1);
		$max = str_repeat('1',$length) * 9;
        return rand($min,$max);//random($length, 1);
    }

    public static function createSign($data)
    {
        ksort($data);
        return md5(json_encode($data) . self::key);
    }

    public static function getSms($type, $code, $submsg = '')
    {
        $code = (int)$code;
        $sms = array(
            1 => '【欢朋直播】验证码是' . $code . '，用于注册手机验证，15分钟内有效。切勿泄露他人。欢朋直播App下载：'.WEB_ROOT_URL."download.php",//http://www.huanpeng.com/downloadPc.php?reftype=app
            2 => '【欢朋直播】验证码是' . $code . '，用于申请主播手机验证，15分钟内有效。切勿泄露他人。',
            3 => '【欢朋直播】验证码是' . $code . '，用于找回密码手机验证，15分钟内有效。切勿泄露他人。',
            4 => '【欢朋直播】很遗憾，你的主播认证申请未通过审核。原因：' . $submsg . '。你可以通过手机客户端或者网站重新提交认证',
            5 => '【欢朋直播】恭喜你！你的主播认证申请通过审核。请前往欢朋直播开启你的直播生涯吧！欢朋直播App下载：'.WEB_ROOT_URL."download.php",
            6 => '【欢朋直播】验证码是' . $code . '，用于绑定手机验证，15分钟内有效。切勿泄露他人'
        );

        return $sms[$type];
    }


    public static function getMsgBalance(RedisHelp $redis = null)
    {
        static::setRedis($redis);
        $balance = (int)$redis->get(self::redis_msgBalance);
        if ($balance) {
            return true;
        } else {
            return false;
        }
    }
    public static function getMsgBalanceByUrl(){
        $res = file_get_contents(self::getBalanceURL);
        $res = json_decode($res);
        if(!res || $res->resuNo != 1){

            return false;
        }
    }
}