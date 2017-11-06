<?php
/**
 * video
 * v 2.0
 * date 2017-09-11
 */
namespace lib\live;

use Exception;
use system\DbHelper;
use lib\live\Config;
use lib\WcsHelper;
use lib\live\LiveHelper;
use lib\CDNHelper;
use lib\SiteMsgBiz;
use lib\MsgPackage;

class VideoHelper{
	static $dbname = "huanpeng";
	static $video_table = "video";
	static $master_flv_table  = "live_VideoRecord";
	static $slave_flv_table   = "slaveflv";
	static $seconds = 5;
	static $merge_table = "video_merge_record";

	public static function getdb(){
		return DbHelper::getInstance(self::$dbname);
	}

	public static function getflvs($liveid, $master = true){
		if(empty($liveid)||!is_numeric($liveid))
			return false;
		$table = $master?self::$master_flv_table:self::$slave_flv_table;
		$sql = "SELECT `stream`,`keys` FROM " . $table . " WHERE `liveid`=:liveid";
		$bdparams = ['liveid'=>$liveid];
		$db = self::getdb();
		try{
			$result = $db->query($sql,$bdparams);
			$flvs = [];
			if(empty($result))
				return $flvs;
			foreach ($result as $v){
				$flvs['keys'][] = $v['keys'];
				$flvs['stream'][] = $v['stream'];
			}
			return $flvs;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function getstreams($liveid, $master = true){
		if(empty($liveid)||!is_numeric($liveid))
			return false;
		$table = $master?LiveHelper::$master_stream_table:LiveHelper::$slave_stream_table;
		$sql = "SELECT `stream`,`stime`,`etime` FROM " . $table . " WHERE `liveid`=:liveid";
		$bdparams = ['liveid'=>$liveid];
		$db = self::getdb();
		try{
			$result = $db->query($sql,$bdparams);
			$flvs = [];
			if(empty($result))
				return $flvs;
			foreach ($result as $v){
				$stime = strtotime($v['stime']);
				$etime = strtotime($v['etime']);
				if($stime>0 && $etime>0 && ($etime-$stime)>self::$seconds)
					$flvs[] = $v['stream'];
			}
			return $flvs;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function addflv($field = [],$master = true){
		if(empty($field) || !is_array($field))
			return false;
		$table = $master?self::$master_flv_table:self::$slave_flv_table;
		return self::add($field,$table);
	}
	public static function addmerge($field = []){
		if(empty($field) || !is_array($field))
			return false;
		return self::add($field,self::$merge_table);
	}

	public static function add($field,$table){
		$sqlkey = array_keys($field);


		foreach ($sqlkey as $value){
			$sqlvalue[] = ":$value";
			$sqlfield[] = "`$value`";
		}
		$sqlfield = implode(',',$sqlfield);
		$sqlvalue = implode(',',$sqlvalue);
		$db = self::getdb();
		$sql = "INSERT INTO " . $table . "({$sqlfield}) " . "VALUES({$sqlvalue})";
		try{
			$affect = $db->execute($sql,$field);
			return $affect?true:false;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function mergefiles($files,$save){
		$cdn = self::getcdn();
		return $cdn->mergeFiles($files,$save);
	}
	public static function cutpic($file, $save, $offset){
		$cdn = self::getcdn();
		return $cdn->cutOutVideoPicture($file, $save, $offset);
	}
	public static function getcdn(){
		return  new CDNHelper();
	}

	public static function getmerge($key,$field){
		if(empty($key)||empty($field))
			return [];
		$sqlfield = implode(",",$field);
		foreach ($key as $k => $v){
			$where[] = "$k=:{$k}key";
			$bdparams["{$k}key"] = $v;
		}
		$where = implode(",",$where);
		$db = self::getdb();
		$sql = "SELECT " . $sqlfield . " FROM " . self::$merge_table . " WHERE " . $where;
		try{
			$result = $db->query($sql,$bdparams);
			return empty($result)?[]:$result;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function setmerge($key,$field){
		if(empty($key) || empty($field))
			return false;
		foreach ($field as $k => $v){
			$sqlfield[] = "$k=:$k";
		}
		$sqlfield = implode(",",$sqlfield);
		foreach ($key as $k => $v){
			$where[] = "$k=:{$k}key";
			$field["{$k}key"] = $v;
		}
		$where = implode(",",$where);
		$sql = "UPDATE " . self::$merge_table . " SET " . $sqlfield . " WHERE " . $where;
		$db = self::getdb();
		try{
			$affect = $db->execute($sql, $field);
			return $affect?true:false;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function addvideo($field = []){
		if(empty($field) || !is_array($field))
			return false;
		return self::add($field,self::$video_table);
	}
	public static function setvideopic($liveid, $poster = '',$master = true){
		if(!is_numeric($liveid)||empty($poster))
			return false;
		$sql = "UPDATE `video` SET `poster`=:poster WHERE liveid=:liveid ";
		$bdparams = [
			'poster' => Config::$v_dir[$GLOBALS['env']]['i'] . $poster,
			'liveid' => $liveid,
		];
		if(!$master){
			$sql .= " AND `livetype`=:livetype";
			$bdparams['livetype'] = Config::$live_type['doubelslave'];
		}else{
			$sql .= " AND `livetype`!=:livetype";
			$bdparams['livetype'] = Config::$live_type['doubelslave'];
		}
		$db = self::getdb();
		try{
			return $db->execute($sql,$bdparams);
		}catch (Exception $e){
			throw $e;
		}
	}
	public static function sendmsg($liveid){
		if(!is_numeric($liveid))
			return false;
		$live = LiveHelper::getlivebykey(['liveid'=>$liveid],['gamename,title,uid'],LiveHelper::$master_live_table);
		if(empty($live[0])){
			return false;
		}
		$msg = [
			'uid' => $live[0]['uid'],
			'title' => '系统消息',
			'content' => "您的直播视频\"{$live[0]['gamename']}-{$live[0]['title']}\"已生成，可以到我的空间发布哦～",
		];
		$db = new \DBHelperi_huanpeng();
		$redis = new \RedisHelp();
		$package = MsgPackage::getSiteMsgPackage($msg['uid'], $msg['title'], $msg['content'], MsgPackage::SITEMSG_TYPE_TO_USER);
		$siteMsg = new SiteMsgBiz($db, $redis);
		return $siteMsg->sendMsg($package);
	}

}