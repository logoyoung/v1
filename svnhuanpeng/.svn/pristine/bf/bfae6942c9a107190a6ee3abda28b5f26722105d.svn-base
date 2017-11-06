<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/23
 * Time: 上午9:50
 */

class MailSend{
	const APPID = '102';
	const key = "ekxklhuangTSDpengfkjekldc";
	const CALL_BACK_URL = 'mp/certify_email/certify.php?';

	const T_CERT_EMAIL = 'registemail_102';


	const TABLE_NAME = 'send_email_record';
	const DEV_SEND_URL = 'http://dev.liveuser.6.cn/api/pubSendEmailApi.php?';
	const PRO_SEND_URL = 'http://liveuser/api/pubSendEmailApi.php?';

	const STATUS_SEND = 0;
	const STATUS_BACK = 1;

	const TIME_LIMIT = 86400;
	const SEND_TIME_LIMIT = 3;

	const URL_GET_FAILED = -4070;

	const SEND_SUCC_STATUs = 1;

	static $db = null;
	static $redis = null;
	static $codeid = 0;
	static $sendType = 0;
	static $email = '';
	static $cert_time = 0;

	public static function setDb(DBHelperi_huanpeng $db=null){
		if(!static::$db)
			if($db)
				static::$db = $db;
			else
				static::$db = new DBHelperi_huanpeng();
	}

	public static function setRedis(RedisHelp $redis){
		if(!static::$redis)
			if($redis)
				static::$redis = $redis;
			else
				static::$redis = new RedisHelp();
	}

	public static function canSendMsg($type,$email,$db,$limit=0){
		self::setDb($db);
		if(!$limit)
			$limit = self::SEND_TIME_LIMIT;

		$stime = date("Y-m-d")." 00:00:00";
		$etime = date("Y-m-d")." 23:59:59";
		$sql = "select count(*)  as count from ".self::TABLE_NAME." where type='$type' and email='$email' and ctime between '$stime' and '$etime' and errorNo=".self::SEND_SUCC_STATUs;
		$res = static::$db->query($sql);
		if(!$res){
			//logs
			return false;
		}

		$row = $res->fetch_assoc();
		$nums = (int)$row['count'];
		if($nums > $limit){
			return false;
		}

		return true;
	}

	public static function sendMsg($content,  DBHelperi_huanpeng $db=null, RedisHelp $redis=null){
		static::setDb($db);
		static::setRedis($redis);

		$data = array(
			'appid' => self::APPID,
			'type'=>static::$sendType,
			'email'=>static::$email,
			'content'=>$content
		);
		$data['sign'] = static::createSign($data);

		$str = http_build_query($data);

		$env = strtoupper($GLOBALS['env']);
		if($env == "DEV"){
			$send_url = self::DEV_SEND_URL.$str;
		}else{
			$send_url = self::PRO_SEND_URL.$str;
		}

		$ret = file_get_contents($send_url);
		if($ret){
			$ret = json_decode($ret, true);
			static::setRecordError(static::$codeid, $ret['resuNo'], self::STATUS_SEND);

			if($ret['resuNo'] == 1){
				static::$redis->set(static::return_redis_key(static::$sendType,static::$email), static::$codeid);
				return true;
			}else{
				return false;
			}
		}

		static::setRecordError(static::$codeid, self::URL_GET_FAILED, static::STATUS_SEND);
		return false;
	}

	public static function sendMsgCallBack($codeid, DBHelperi_huanpeng $db = null, RedisHelp $redis = null){
		static::setDb($db);
		static::setRedis($redis);
		static::setRecordError($codeid, 1, self::STATUS_BACK);
		static::getRecordInfo($codeid, $db);

		static::$redis->del(static::return_redis_key(static::$sendType, static::$email));
	}


	public static function getRecordInfo($codeid, DBHelperi_huanpeng $db = null){
		static::setDb($db);
		$sql = "select id,email,type,ctime from ".self::TABLE_NAME. " where id=$codeid";
		$res = static::$db->query($sql);
		if(!$res){
			//logs
			return false;
		}
		$row = $res->fetch_assoc();
		if($row['id']){
			static::$codeid = $codeid;
			static::$email = $row['email'];
			static::$sendType = $row['type'];
			return $row;
		}

		return false;
	}

	public static function createCodeId($type, $email, $businessid, DBHelperi_huanpeng $db = null){
		static::setDb($db);
		$port = '';
		$ip = ip2long(fetch_real_ip($port));

		$data = array(
			'type'=>$type,
			'email' =>$email,
			'businessid' => $businessid,
			'ip'=>$ip,
			'port'=>$port
		);
		if(static::$db->insert(self::TABLE_NAME, $data)){
			static::$email = $email;
			static::$sendType = $type;
			static::$codeid = static::$db->insertID;

			return static::$codeid;
		}else{
			return false;
		}
	}

	public static function getEmailContent($nick,$url){
		$data = array('username'=>$nick,'url'=>$url,'expire'=>'24');
		ksort($data);
		return json_encode($data);
	}

	public static function getEmailContentUrl($uid, $email){
		$data = array(
			'appkey' => static::getAppKey($uid, $email),
			'email' => $email,
			'uid'=>$uid
		);

		$url = http_build_query($data);

		return WEB_PERSONAL_URL.self::CALL_BACK_URL.$url;
	}

	public static function getAppKey($uid, $email){
		static::$cert_time = time() + EMAIL_CERT_OUTTIME;
		return md5($uid.CERT_EMAIL_KEY.$email.static::$cert_time).'-'.static::$cert_time;
	}

	public static function createSign($data){
		ksort($data);
		return md5(json_encode($data).self::key);
	}

	public static function setRecordError($codeid, $errno, $status, $db=null){
		self::setDb($db);
		$data = array(
			'status' => $status,
			'errorNo' => $errno
		);
		static::$db->where("id=$codeid")->update(self::TABLE_NAME, $data);
	}

	public static function return_redis_key($type,$email){
		return "sendMailRedis:$type:$email";
	}
}