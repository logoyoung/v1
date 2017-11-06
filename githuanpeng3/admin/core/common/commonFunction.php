<?php

/*
 * 封装一些常用的公用的方法
 * date 2015-12-18 10:52 Am
 * author yandong@6rooms.com
 * copyright@6.cn
 */

/**
 * 过滤接受的参数或者数组,如$_GET,$_POST
 * date 2015-12-08
 * author yandong@6rooms.com
 * @param array|string $arr 接受的参数或者数组
 * @return array|string
 */
function filterData($arr)
{
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k] = filterWords($v);
        }
    } else {
        $arr = filterWords($arr);
    }
    return $arr;
}

/**
 * 参数过滤
 * date 2015-12-10
 * author yandong@6rooms.com
 * @param string $str 接受的参数
 * @return string
 */
function filterWords($str)
{
    $farr = array(
        "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
        "/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
    );
    $str = htmlspecialchars(trim($str));
    $str = preg_replace($farr, '', $str);
    return $str;
}


/**
 * 获取用户信息
 * @param int $uid
 * @param object $db
 * @return array
 */
function getUserInfo($uid, $db)
{
    if (is_array($uid)) {
        $uid = implode(',', $uid);
        $res = $db->field('uid,nick,pic,phone')->where("uid in($uid)")->select('userstatic');
        if ($res) {
            foreach ($res as $v) {
                $row[$v['uid']] = $v;
            }
        }
    } else {
        $row = $db->field('uid,nick,pic,phone')->where('uid=' . $uid . '')->select('userstatic');
    }
    return $row ? $row : array();
}

/**
 * 批量获取用户昵称
 * date  2015-12-14
 * @param array $uids
 * @param object $db
 * @return array
 */
function getUserNicks($uids, $db)
{
    $s = implode(',', $uids);
    $ret = array();
    $res = $db->field('uid,nick')->where('uid in (' . $s . ')')->select('userstatic');
    foreach ($res as $key => $val) {
        $ret[$val['uid']] = $val['nick'];
    }
    return $ret;
}

/**
 * 获取游戏类型名称
 * @param int $gametid
 * @param object $db
 * @return string
 */
function getGameTypeName($gametid, $db)
{
    $gtname = '';
    $name = $db->field('name')->where('gametid=' . $gametid . '')->limit('1')->select('gametype');
    if ($name) {
        $gtname = $name[0]['name'];
    }
    return $gtname;
}

/**
 * 批量获取游戏名称
 * @param type $gameids
 * @param type $db
 * @return type
 */
function getMoreGameName($gameids, $db)
{
    $gamename = $db->field('gameid,name')->where("gameid in ($gameids)")->select('game');
    foreach ($gamename as $v) {
        $gname[$v['gameid']] = $v['name'];
    }
    return $gname ? $gname : '';
}

/**
 * 计算时间差
 * @param type $begin_time
 * @param type $end_time
 * @return type
 */
function timediff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array("days" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}

/**
 * 把秒数格式化
 * @param 秒数 $second
 * @return boolean|string
 */
function SecondFormat($second)
{
    if (!is_numeric($second)) {
        return false;
    }
    $str = '';
    $d = floor($second / 3600 / 24);
    $h = floor(($second % (3600 * 24)) / 3600);  //%取余
    $m = floor(($second % (3600 * 24)) % 3600 / 60);
    $s = floor(($second % (3600 * 24)) % 60);
    if (!empty($d)) {
        $str .= $d . '天';
    }
    if ($str != '' || !empty($h)) {
        $str .= $h . '时';
    }
    if ($str != '' || !empty($m)) {
        $str .= $m . '分';
    }
    $str .= $s . '秒';
    return $str;
}

/**
 * 字符串定长截取
 * @param string $str
 * @param int $len
 * @param bool $suffix
 * @return string
 */
function strCut($str, $len, $suffix = true)
{
    if (mb_strlen($str, 'utf8') > $len) {
        if ($suffix) {
            return mb_substr($str, 0, $len, 'utf8') . '....';
        } else {
            return mb_substr($str, 0, $len, 'utf8');
        }
    }
    return $str;
}

/**
 * 过滤$_GET参数
 * @param string $str
 * @param array $arr
 * @return string
 */
function filterArg($arg, $arr = array())
{
	$str = '';
	$get = empty($arr) ? $_GET : $arr;
	if($get) {
		foreach($get as $k=>$v){
			if ($k == $arg){
				continue;
			}
			$str .=	$k . "=" .$v . "&";
		}
	}
	return $str ? $str : '&';
}

/**检查审核模式
 * @param $param  头像、昵称、直播标题、评论、弹幕
 * @param $db
 * @return bool
 */
function checkMode($param, $db)
{
    $res = $db->field('status')->where("id=$param")->select('admin_check_mode');
    if ($res !== false) {
        return $res[0]['status'];
    } else {
        return false;
    }
}

/**
 *修改昵称同步到admin_user_nick表
 * @param int $uid
 * @param string $nick
 * @param type $db
 * @return boolean
 */
function setNickToAdmin($uid, $nick, $db, $status)
{
    if (empty($uid) || empty($nick)) {
        return false;
    }
    $res = $db->field('nick')->where("uid=$uid")->select('userstatic');
    if (false !== $res) {
        $oldnick = $res[0]['nick'];
        $sql = "INSERT INTO `admin_user_nick` (`uid`,`nick`,`oldnick`,`status`) VALUES ($uid,'$nick' , '$oldnick',$status )
                 on duplicate key update uid = $uid , nick = '$nick', oldnick='$oldnick',status=$status";
        $res = $db->query($sql);
        if (false !== $res) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**添加一条新纪录
 * @param $rid  资讯id
 * @param $typeid //推荐类型id
 * @param $db
 * @return bool
 */
function addRecommentData($rid, $typeid, $db)
{
    $utime = date('Y-m-d H:i:s', time());
    $sql = "insert into recommend_information (`id`,`list`,`utime`) value($typeid,'$rid','$utime') on duplicate key update list='$rid',utime='$utime'";
    $res = $db->query($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
    return $sql;
}


function game_poster($gameids, $db)
{
    if (empty($gameids)) {
        return false;
    }
    $res = $db->field("gameid,poster")->where("gameid in ($gameids)")->select('game_zone');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $temp[$v['gameid']] = $v['poster'];
            }
            return $temp;
        }
    } else {
        return false;
    }
}

function game_name($gameids, $db)
{
    if (empty($gameids)) {
        return false;
    }
    $res = $db->field("gameid,name")->where("gameid in ($gameids)")->select('game');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $temp[$v['gameid']] = $v['name'];
            }
            return $temp;
        }
    } else {
        return false;
    }
}


function getAdminNick($ids, $db)
{
    if (empty($ids)) {
        return false;
    }
    $res = $db->field('uid,username')->where("uid in ($ids)")->select('admin_user');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $temp[$v['uid']] = $v['username'];
            }
            return $temp;
        }
    } else {
        return false;
    }
}

function getAdminRecommendGame($db)
{
    $res = $db->field('type,gameid,number')->where('1 group by type')->select('admin_recommend_game');
    if (false !== $res) {
        if ($res) {
            $nlist = $rlist = $flist = array();
            foreach ($res as $v) {
                $game_name = game_name($v['gameid'], $db);
                $gcount = explode(',', $v['gameid']);
                if ($v['type'] == 1) {
                    for ($i = 0, $k = count($gcount); $i < $k; $i++) {
                        $tmp['gameID'] = $gcount[$i];
                        $tmp['gameName'] = isset($game_name[$gcount[$i]]) ? $game_name[$gcount[$i]] : '';
                        array_push($nlist, $tmp);
                    }
                }
                if ($v['type'] == 2) {
                    $conf = $GLOBALS['env-def'][$GLOBALS['env']];
                    $game_poster = game_poster($v['gameid'], $db);
                    for ($i = 0, $k = count($gcount); $i < $k; $i++) {
                        $temp['gameID'] = $gcount[$i];
                        $temp['gameName'] = isset($game_name[$gcount[$i]]) ? $game_name[$gcount[$i]] : '';
                        $temp['poster'] = isset($game_poster[$gcount[$i]]) ? "http://" . $conf['domain-img'] . '/' . $game_poster[$gcount[$i]] : '';
                        array_push($rlist, $temp);
                    }
                }
                if ($v['type'] == 3) {
                    $numCount = explode(',', $v['number']);
                    for ($i = 0, $k = count($gcount); $i < $k; $i++) {
                        $tem['gameID'] = $gcount[$i];
                        $tem['gameName'] = isset($game_name[$gcount[$i]]) ? $game_name[$gcount[$i]] : '';
                        $tem['number'] = isset($numCount[$i]) ? $numCount[$i] : 0;
                        array_push($flist, $tem);
                    }
                }
            }
            return array('nlist' => $nlist, 'rlist' => $rlist, 'flist' => $flist);
        } else {
            return array('nlist' => array(), 'rlist' => array(), 'flist' => array());
        }
    } else {
        return false;
    }
}


function get_GameList($db)
{
    $conf = $GLOBALS['env-def'][$GLOBALS['env']];
    $list = array();
    $res = $db->field("gameid,name")->select('game');
    if (false !== $res && !empty($res)) {
        $gameid = array_column($res, 'gameid');
        $gzone = game_poster(implode(',',$gameid), $db);
        foreach ($res as $v) {
            $temp['gameID'] = $v['gameid'];
            $temp['gameName'] = $v['name'];
            $temp['poster'] = isset($gzone[$v['gameid']]) ? "http://" . $conf['domain-img'] . '/' . $gzone[$v['gameid']] : '';
            array_push($list, $temp);
        }
        return array('list' => $list);
    } else {
        return array('list' => array());
    }
}


function dong_log($title, $content, $db)
{
    $data = array(
        'title' => $title,
        'content' => $content
    );
    $db->insert('dong_log', $data);
}
/**
 * 同步任务
 * @param type $uid 用户id
 * @param type $taskid 任务id
 * @param type $bean 获得金豆数
 * @param type $db
 * @return boolean
 */
function synchroTask($uid, $taskid, $type, $bean, $db)
{
    if (empty($uid) || empty($taskid)) {
        return false;
    }
    $data = array(
        'uid' => $uid,
        'taskid' => $taskid,
        'status' => TASK_FINISHED,
        'type' => $type,
        'getbean' => $bean
    );
    $res = $db->insert('task', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

function  getVInfoByVideoId($videoId,$db){
    if(empty($videoId)){
        return false;
    }
    $res=$db->field('uid,gamename,title,liveid')->where("videoid=$videoId")->limit(1)->select('video');
    if(false !==$res &&  !empty($res)){
       return $res;
    }else{
      return array();
    }

}

function checkisAutoPublish($liveid,$db){
    if(empty($liveid)){
        return false;
    }
    $res=$db->field('antopublish')->where("liveid=$liveid")->limit(1)->select('live');
    if(false !==$res &&  !empty($res)){
        return $res[0]['antopublish'];
    }else{
        return 0;
    }
}

/**
 * 添加一条新的站内消息
 * @param string $title
 * @param string $message
 * @param object $db
 * @return string
 */
function addMessagesText($title, $message, $type, $group = '', $sendid = 0, $db)
{
    $data = array(
        'title' => $title,
        'msg' => $message,
        'type' => $type,
        'group' => $group,
        'sendid' => $sendid
    );
    $res = $db->insert('sysmessage', $data);
    return $res;
}

/**
 * 添加一条用户消息
 * @param type $uid
 * @param type $msgid
 * @param type $db
 * @return type
 */
function addUserMessages($uid, $msgid, $db)
{
    $data = array(
        'uid' => $uid,
        'msgid' => $msgid
    );
    $res = $db->insert('usermessage', $data);
    return $res;
}

/**
 * 更改用户站内信数量
 * @param int $uid
 * @param object $db
 * @return bool
 */
function updateUserMailStatus($uid, $db)
{
    $sql = "update useractive set readsign=readsign+1 where uid=$uid";
    $res = $db->doSql($sql);
    return $res;
}

/**
 * 发送消息
 * @param type $sendId
 * @param type $title
 * @param type $message
 * @param type $type
 * @param type $db
 * @return int 成功返回1 失败返回0
 */
function sendMessages($sendId, $title, $message, $type, $db)
{
    if (empty($sendId) || empty($title) || empty($message)) {
        return false;
    }
    if (!in_array($type, array(0, 1))) {
        return false;
    }
    //一对一
    if ($type == 0) {
        $addMsgRes = addMessagesText($title, $message, $type, $group = 1, $sendid = 0, $db);
        $adduseRes = addUserMessages($sendId, $addMsgRes, $db);
        if ($adduseRes) {
            $res = updateUserMailStatus($sendId, $db);
        } else {
            $res = false;
        }
    }
    //一对多
    if ($type == 2) {
        $res = addMessagesText($title, $message, $type, $group = 2, $sendid = 0, $db);
    }
    if ($res !== false) {
        $back = 1;
    } else {
        $back = 0;
    }
    return $back;
}
function fetch_real_ip(&$port) {
    $pat_ip_port = '/((\d{1,3}\.){3}\d{1,3}):(\d+)/s';
    $pat_ip = '/(\d{1,3}\.){3}\d{1,3}/s';
    $pat_not_internal = '/^(10|172\.16|192\.168)\./';

    $ip = '';
    $port = 0;

    // X-Forwarded-Addr IP:PORT
    if (isset($_SERVER["HTTP_X_FORWARDED_ADDR"]) && preg_match_all($pat_ip_port, $_SERVER['HTTP_X_FORWARDED_ADDR'], $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            if (!preg_match($pat_not_internal, $matches[1][$i])) {
                $ip = $matches[1][$i];
                $port = $matches[3][$i];
                break;
            }
        }
    } // X-Forwarded-For (no port info)
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all($pat_ip, $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $ip) {
            if (!preg_match($pat_ip, $ip)) {
                break;
            }
        }
    } elseif (isset($_SERVER["HTTP_FROM"]) && preg_match($pat_ip, $_SERVER["HTTP_FROM"])) {
        $ip = $_SERVER["HTTP_FROM"];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match($pat_ip, $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } // directly access
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $port = $_SERVER['REMOTE_PORT'];
    }

    return $ip;
}

//创建网宿防盗链
function createSecurityChain($filename){
    $ip = fetch_real_ip();
    //$ip = '11.22.33.44';
    $now = time();
    //$eTime = dechex($now+WS_EXPIRED);
    $eTime = dechex($now);
    $cTime = dechex($now) ;
    $wsSecret = md5(WS_SECURITY_CHAIN.$ip.'/'.$filename.$cTime);
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query($data);
}
//封面图
function sposter($poster){
    if(empty($poster)){
        return '';
    }else{
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $iparam = createSecurityChain($poster);
        return $conf['domain-vposter'] . '/' . $poster . '?' . $iparam;
    }
}
//播放地址
function sfile($file){
    if(empty($file)){
        return '';
    }else{
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $iparam = createSecurityChain($file);
        return $conf['domain-video'] . '/' . $file . '?' . $iparam;
    }
}


function  getCompanyAnchorInfo($cid,$page,$size,$db){
    $count=$db->field('count(*) as  total')->where("cid=$cid")->select('anchor');
    if($count){
        $res=$db->field('uid')->where("cid=$cid")->limit($page,$size)->select('anchor');
        if(false !==$res){
            if($res){
                return array('total'=>$count[0]['total'],'res'=>$res);
            }else{
                return array('total'=>0,'res'=>$res);
            }
        }else{
            return array('total'=>0,'res'=>$res);
        }
    }else{
        return array('total'=>0,'res'=>$res);
    }

}


function  searchUserInfokeyWord($keyword,$db){
    $res=$db->field('uid')->where("nick like '%$keyword%'")->select("userstatic");
    if(false !==$res){
        return array('res'=>$res,'total'=>count($res));
    }else{
        return array('res'=>array(),'total'=>0);
    }
}

function  checkUserIsCompany($uids,$db){
    $res=$db->field('uid,cid')->where("uid in ($uids) ")->select("anchor");
    if(false !==$res){
        if($res){
            foreach ($res as  $v){
                $list[$v['uid']]=$v['cid'];
            }
            return $list;
        }else{
            return array();
        }
    }else{
        return array();
    }
}

function CurlPost($url,$data=array()){
    //对空格进行转义
    $url = str_replace(' ','+',$url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$url");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_TIMEOUT,5); //定义超时3秒钟
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $output = curl_exec($ch);
    $errorCode = curl_errno($ch);
    curl_close($ch);
    if(0 !== $errorCode) {
        return false;
    }
    return $output;
}

/**
 * 获取过去一年的月份
 * @param 秒数 $second
 * @return array
 */
function getPastMonth()
{
	$start_year = 2017;
	$start_month = 1;
	
	$end_year = date('Y');
	$end_month = (int)date('m');
	
	$date = array();
	
	while($end_month - $start_month >= 0) {
		$i = $end_month;
		$date[] = $end_year . '-' . ($i<10 ? '0' . $i : $i);
		$end_month--;
	}
	
	if($end_month - $start_month < 0) {
		for($i = $end_month; $i>0; $i--) {
			$date[] = $end_year . '-' . ($i<10 ? '0' . $i : $i);
		}
	}
	
	while($end_year - $start_year >= 1) {
		$end_year--;
		for($i = 12; $i>0; $i--) {
			
			if($end_year == $start_year && $i > $start_month) {
				$date[] = $end_year . '-' . ($i<10 ? '0' . $i : $i);
			} else if($end_year > $start_year) {
				$date[] = $end_year . '-' . ($i<10 ? '0' . $i : $i);
			}
		}
		
	}
	return $date;
}

/**
 * 添加比率变化记录表
 *
 * @param  array $data
 *     $data = array(
 *     'uid' => '0',
 *     'before_rate' => 0,
 *     'after_rate' => RATE,
 *     'adminid' => $adminid,
 *     'type' => 2,
 *     'role_change_id' => (int)$cid,
 *     'desc' => ''
 *     );
 * @param        $db
 *
 * @return bool
 */
//
//function addRateChangeRecord( $data, $db )
//{
//	if( empty( $data ) )
//	{
//		return false;
//	}
//	$res = $db->insert('rate_change_record', $data );
//	if( $res )
//	{
//		return true;
//	}
//	else
//	{
//		return false;
//	}
//}

