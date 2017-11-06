<?php

function isMobile()
{
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

    function CheckSubstrs($substrs, $text)
    {
        foreach ($substrs as $substr)
            if (false !== strpos($text, $substr))
            {
                return true;
            }
        return false;
    }

    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

    $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
            CheckSubstrs($mobile_token_list, $useragent);

    if ($found_mobile)
    {
        return true;
    } else
    {
        return false;
    }
}

function liveStatusMsgToAdmin($liveid, $liveStatus)
{
    $url = "http://dev.huanpeng.com/admin2/api/live/setTaskStatus.php";
    $data = array(
        'liveid' => $liveid,
        'livestatus' => $liveStatus,
        'tm' => time()
    );
    $url = $url . "?" . http_build_query(toString($data)) . "&sign=" . buildSign($data, MSG_ADMIN);
    $r = file_get_contents($url);
    mylog("--setTaskStatus--" . $url . $r, LOGFN_SEND_MSG_ERR);
    //buildSign()
    return $r;
}

function buildSign($data, $secretKey, $urlEncode = true)
{
    foreach ($data as $key => $val)
    {
        $data[$key] = $urlEncode ? urlencode($val) : $val;
    }
    ksort($data);
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    $sign = md5(sha1($data . $secretKey));
    return $sign;
}

/**
 * 获取最近直播的五场游戏名称
 * @param type $uid  主播id
 * @param type $db
 * @return type
 */
function gameNameHistory($uid, $db)
{
    $glist = array();
    if (empty($uid))
    {
        return false;
    }
    $res = $db->field('gamename')->where('uid=' . $uid . '  group by gamename')->order('ctime DESC')->limit(5)->select('live');
    if ($res !== false)
    {
        foreach ($res as $v)
        {
            array_push($glist, $v['gamename']);
        }
    }
    return $glist;
}

function toString($mix)
{
    if (is_string($mix))
    {
        return $mix;
    }
    if (is_int($mix) || is_bool($mix) || is_float($mix) || is_double($mix))
    {
        return "$mix";
    }
//	if(is_object($mix)){
//		foreach ($mix as $key => $v){
//			$mix[$key] = $v;
//		}
//		return (object)$mix;
//	}
    if (is_array($mix))
    {
        foreach ($mix as $key => $v)
        {
            $mix[$key] = toString($v);
        }
        return $mix;
    }


    return "";
}

//校验邮箱
function checkEmailFormat($email)
{
    if (!$email || !preg_match('/^\w+@(\w)+((\.\w+)+)$/', $email))
        return false;

    return true;
}

/**
 * 密钥校验
 *
 * @param type $data
 *
 * @return boolean
 */
function verifySign($data, $secretKey)
{
    //验证参数中是否有签名
    if (!isset($data['sign']) || !$data['sign'])
    {
        return false;
    }
    //验证请求有无时间戳
    if (!isset($data['tm']) || !$data['tm'])
    {
        return false;
    }
    //验证请求，3分钟实效
//    if (time() - $data['tm'] > 180 || time() - $data['tm'] < 0) {
//        return false;
//    }
    $sign = $data['sign'];
    unset($data['sign']);
    $sign2 = buildSign($data, $secretKey, false);
//    ksort($data);
//    foreach ($data as $key => $val) {
//        $data[$key] = urldecode($val);
//    }
////    $tmpdata = json_encode($data,JSON_UNESCAPED_UNICODE);
//    $tmpdata = json_encode($data);
//    $sign2 = md5(sha1($tmpdata . $secretKey));
//    return array('sign' => $sign2, 'data' => $data);
    if ($sign === $sign2)
    {
        return true;
    } else
    {
        return false;
    }
}

// 递归建立目录
function mkdirs($dir, $mode = 0755)
{
    if (is_null($dir) || $dir === "")
        return FALSE;
    if (is_dir($dir) || $dir === "/")
        return TRUE;
    if (mkdirs(dirname($dir), $mode))
    {
        $r = mkdir($dir, $mode);
        if (!$r)
            exit(-1902);
        return $r;
    }
    return FALSE;
}

function msgexit($msg)
{
    echo $msg;
    exit;
}

function msglogexit($msg, $log = LOGFN_GENERAL)
{
    $r = mylog($msg, $log);
    echo $msg;
    exit;
}

function mylog($msg, $logfile = LOGFN_GENERAL)
{
    $msg = '[' . getmypid() . '] [' . get_datetime() . '] ' . $msg . "\n";
    $r = file_put_contents($logfile, $msg, FILE_APPEND);
    return $r;
}

function get_datetime($tm = null)
{
    if (!$tm)
        $tm = time();
    return date("Y-m-d H:i:s", $tm);
}

function get_date($tm = null)
{
    if (!$tm)
        $tm = time();
    return date("Y-m-d", $tm);
}

function get_next_date($tm = null)
{
    if (!$tm)
        $tm = time() + 86400;
    return date("Y-m-d", $tm);
}

function fetch_real_ip(&$port)
{
    $pat_ip_port = '/((\d{1,3}\.){3}\d{1,3}):(\d+)/s';
    $pat_ip = '/(\d{1,3}\.){3}\d{1,3}/s';
    $pat_not_internal = '/^(10|172\.16|192\.168)\./';

    $ip = '';
    $port = 0;

    // X-Forwarded-Addr IP:PORT
    if (isset($_SERVER["HTTP_X_FORWARDED_ADDR"]) && preg_match_all($pat_ip_port, $_SERVER['HTTP_X_FORWARDED_ADDR'], $matches))
    {
        for ($i = 0; $i < count($matches[1]); $i++)
        {
            if (!preg_match($pat_not_internal, $matches[1][$i]))
            {
                $ip = $matches[1][$i];
                $port = $matches[3][$i];
                break;
            }
        }
    } // X-Forwarded-For (no port info)
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all($pat_ip, $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
    {
        foreach ($matches[0] as $ip)
        {
            if (!preg_match($pat_ip, $ip))
            {
                break;
            }
        }
    } elseif (isset($_SERVER["HTTP_FROM"]) && preg_match($pat_ip, $_SERVER["HTTP_FROM"]))
    {
        $ip = $_SERVER["HTTP_FROM"];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match($pat_ip, $_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } // directly access
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $port = $_SERVER['REMOTE_PORT'];
    }

    return $ip;
}

/* * 检测是否含有enmoji表情
 * @param $str
 * @return int
 */

function checkEmoji($str)
{
    $text = json_encode($str); //暴露出unicode
    return preg_match("/(\\\u[ed][0-9a-f]{3})/i", $text);
}

/**
 * 检查用户登陆状态
 *
 * @param string $uid
 * @param string $encpass
 *
 * @return string|boolean
 */
function checkUserState($uid, $encpass, $mydb)
{

    //$uid = $db->realEscapeString( $uid );
    $row = $mydb->field('encpass')->where('uid=' . $uid)->select('userstatic');

//        $sql = "SELECT `encpass` FROM `userstatic` WHERE `uid` = $uid";
//        $res = $db->query($sql);
//
    if (!$row[0])
        return '-1014';

    if ($row[0]['encpass'] != $encpass)
        return '-1013';

    return true;
}

/**
 * 错误退出
 *
 * @param $string $code
 * @param $string $desc
 */
function errorexit($code, $desc)
{

    $err = array('code' => "$code", 'desc' => "$desc");
    exit(json_encode(toString($err)));
}

function error2($code, $type = 1, $debug = false, $custom = null)
{
    if (!headers_sent())
    {
        header('Content-type: application/json; charset=utf-8', true);
    }
    $err = array(
        'status' => "0",
        'content' => array(
            'code' => "$code",
            'desc' => errorDesc($code),
            'type' => "$type"
        )
    );
    if ($debug)
        $err['custom'] = $custom;
    exit(json_encode(toString($err)));
}

function succ($content = array(), $header = false)
{
    if (empty($content))
    {
        $succ = array(
            'status' => "1",
            'content' => (object) $content
        );
    } else
    {
        $succ = toString(array(
            'status' => "1",
            'content' => $content
        ));
    }

    $content = json_encode($succ);
    if ($header)
    {
        
    }
    exit($content);
}

/**
 * 错误信息
 *
 * @param string $code
 */
function error($code)
{
    $desc = errorDesc($code);
    errorexit($code, $desc);
}

function errorDesc($code)
{
    switch ($code)
    {
        case -982: return "您已经评论过喽!";
        case -983: return "渠道非法!";
        case -984: return "服务器繁忙,请稍后再试!";
        case -985: return "用户名未设置";
        case -986: return "没有需要删除的消息!";
        case -987: return "搜索关键词不能为空哦!";
        case -988: return "证件号码有误";
        case -989: return "新密码不能为空";
        case -990: return "昵称已被占用";
        case -991: return "无效的视频ID";
        case -992: return "评论内容不能为空";
        case -993: return "参数错误";
        case -994: return "暂无数据";
        case -995: return "获取数据时发生意外";
        case -996: return "用户名或密码错误";
        case -997: return "用户名或密码不能为空";
        case -998: return "获取用户信息时发生意外";
        case -999: return "密码不一致";
        case'-1001': return "注册用户名已被占用";
        case'-1002': return "用户名为空";
        case"-1003": return "密码长度范围6-12";
        case"-1004": return "昵称长度范围2-10个字符";
        case"-1005": return "注册用户名包含非法字符";
        case"-1006": return "密码包含中文";
        case"-1007": return "数据录入错误";
        case"-1008": return "登陆密码错误";
        case"-1009": return "登陆用户名不存在";
        case"-1010": return "用户名密码不完整";
        case"-1011": return "数据更新错误";
        case"-1012": return "昵称长度范围2-10个字符";
        case"-1013": return "Ecnpass验证错误";
        case"-1014": return "无效的uid";
        case"-1015": return "空的uid 或者ecnpass";
        case"-1016": return "传入文件超过服务器限制";
        case"-1017": return "传入文件超过html限制";
        case"-1018": return "传入文件不完整";
        case"-1019": return "没有文件传入";
        case"-1020": return "文件传入失败";
        case"-1021": return "非图像格式";
        case"-1022": return "文件非法传入";
        case"-1023": return "文件录入失败";
        case"-1024": return "文件保存出错";
        case"-1025": return "无效的目标uid";
        case"-1026": return "空的targetUserID";
        case'-1027': return "无效的关注对象";
        case'-1028': return "无效的size";
        case'-1029': return "无效的目标游戏ID";
        case'-1030': return "数据删除失败";
        case'-1031': return "无效的录像ID";
        case'-1032': return "无此录像";
        case'-1033': return "无效的录像ID列表";
        case'-1034': return "无效的文件名";
        case'-1035': return "空的主播ID";
        case'-1036': return "无此直播";
        case'-1037': return "非法的All标记";
        case'-1038': return "无此用户评论信息";
        case'-2001': return "无法获取游戏类型";
        case'-2002': return "发起直播失败";
        case'-2003': return "参数值或长度越界";
        case'-2004': return "缺少参数或参数类型错误";
        case'-2005': return "无数据";
        case'-2006': return "无此游戏";
        case'-2007': return "旋转角度应为 0，1，2，3";
        case'-2008': return "直播截图异常";
        case'-2009': return "直播截图格式错误";
        case'-2010': return "未获取直播流名称";
        case'-2011': return "评论失败";
        case'-2012': return "评分不正确";
        case'-2013': return "删除录像失败,无此录像或已被删除";
        case'-2014': return "意见反馈不能为空";
        case'-2015': return "反馈失败";
        case'-2016': return "直播创建，未能正常直播";
        case'-2017': return "无此录像";
        case'-2018': return "请求渠道非法!";
//聊天室相关错误
        case -31: return "用户欢朋币余额不足";
        case -32: return "用户欢朋豆余额不足";
        case -33: return "尚未认证手机";
        case -3001: return "聊天室初始化失败";
        case -3002: return "聊天室进入失败";
        case -3003: return "聊天室退出失败";
        case -3004: return "聊天室心跳失败";
        case -3005: return "聊天室消息类型非法";
        case -3006: return "聊天室消息发送失败";
        case -3007: return "聊天室点赞失败";
        case -3008: return "聊天室游客不能说话";
        case -3009: return "用户被禁言，不能发言";
        case -3501: return "消息表写入失败";
        case -3502: return "用户发言失败";
        case -3503: return "获取发言用户信息失败";
        case -3504: return "获取直播信息失败";
        case -3505: return "更新直播赞失败";
        case -3506: return "点赞消息发送失败";
        case -3507: return "获取主播信息失败";
        case -3508: return "礼物全局通知信息发送失败";
        case -3509: return "礼物房间信息发送失败";
        case -3510: return "服务器繁忙";
        case-3511: return "送礼请求参数错误";
        case -3512: return "输入字符不能为空";
        case -3513: return "输入字符不能超过50个字";
//参数错误系列
        case -4001: return "请求用户名为空";
        case -4002: return "请求密码为空";
        case -4003: return "密码长度错误:6-12";
        case -4004: return "用户名长度错误:3-10";
        case -4005: return "注册用户名包含非法字符";
        case -4006: return "注册用户名被占用";
        case -4007: return "登录用户名不存在";
        case -4008: return "登录密码错误";
        case -4009: return "用户登录验证错误";
        case -4010: return "昵称长度范围3-12个字符";
        case -4011: return "用户ID为空";
        case -4012: return "无效的目标用户";
        case -4013: return "缺少参数或者参数类型错误";
        case -4014: return "两次设置密码不一致";
        case -4015: return "传入文件超过服务器限制";
        case -4016: return "传入文件不完整";
        case -4017: return "文件没有传入";
        case -4018: return "文件传入失败";
        case -4019: return "非图像格式";
        case -4020: return "文件非法传入";
        case -4021: return "该直播不存在";
        case -4022: return "该主播不存在";
        case -4023: return "主播ID为空";
        case -4024: return "接口认证失败";
        case -4025: return "三方登录渠道非法";
        case -4026: return "类型错误";
        case -4027: return "金豆数必须大于100";
        case -4028: return "提现金额必须大于800";
        case -4029: return "您的金币数不足";
        case -4030: return "您的金豆数不足";
        case -4031: return "验证码错误或已过期!";
        case -4032: return "当前密码不正确!";
        case -4033: return "文件大小超出浏览器限制";
        case -4034: return "图片太小,请重新裁剪";
        case -4035: return "该昵称已存在";
        case -4036: return "姓名不能为空";
        case -4037: return "身份证格式错误";
        case -4038: return "身份证到期时间类型错误";
        case -4039: return "身份证过期";
        case -4040: return "请上传身份证证件照";
        case -4041: return "认证失败，请重新认证";
        case -4042: return "邮箱格式错误";
        case -4043: return "操作次数超过限制，请24小时后重试";
        case -4044: return "邮件发送失败，请重试";
        case -4045: return "您的实名认证正在审核中，请不要重复认证";
        case -4046: return "您已经实名认证过，请不要重复认证";
        case -4047: return "您的邮箱已经认证过，请不要重复认证";
        case -4048: return "还未到领取时间";
        case -4049: return "该宝箱不存在";
        case -4050: return "该房间不存在此宝箱";
        case -4051: return "请输入正确的联系方式";
        case -4052: return "填写的内容已超出限定字数";
        case -4053: return "内容不能为空";
        case -4054: return "宝箱已经被领取完";
        case -4055: return "很遗憾，您没有领取到";
        case -4056: return "手机号码不能为空";
        case -4057: return "您还不是主播,不能使用这项技能~";
        case -4058: return "请输入正确的手机号码!";
        case -4059: return "未找到该手机号对应的用户,赶快注册吧~";
        case -4060: return "该手机号码已注册过喽!";
        case -4061: return "连续登录失败,请输入验证码";
        case -4062: return "直播标题长度应该为3-20个字符";
        case -4063: return "游戏名称长度应该为1-20个字符";
        case -4064: return "昵称含特殊字符或者为空";
        case -4065: return "两次手机号码不一致";
        case -4066: return "您当前有未结束的直播";
        case -4067: return "请重新登录!";
        case -4068: return "该账号已经被绑定";
        case -4069: return "授权失败";
        case -4070: return "请求非法";
        case -4071: return "省市不能不选哦～";
        case -4072: return "哈尼,详细地址不能为空~";
        case -4073: return "哈尼,解绑次数太多服务器君已忽略～";
        case -4074: return "哈尼,您在其他设备上还有未结束的直播～";
        case -4075: return "哈尼,您又淘气啦!";
        case -4076: return "哈尼,您已经绑定过手机喽!";
        case -4077: return "哈尼,兑换数额不能为空!";
        case -4078: return "哈尼,兑换数额必须是整数!";
        case -4079: return "哈尼,请输入正确的兑换数额!";
        case -4080: return "哈尼,兑换数额最少100!";
        case -4081: return "哈尼,地址太短或太长喽";
        case -4082: return "哈尼,暂无该商品哦!";
        case -4083: return "哈尼,您今天操作太频繁喽!明天再来吧！";
        case -4084: return "哈尼,评论最多120个字符哦！";
        case -4085: return "哈尼,请填写完整需要的信息!";
        case -4086: return "哈尼,您还没有绑定银行卡!";
        case -4087: return "哈尼,这张银行卡您没有绑定过哦!";
        case -4088: return "哈尼,请选择银行卡!";
        case -4090: return "请确认邀请码是否正确!";
        case -4091: return "哈尼,昵称中含非法字符";
        case -4092: return "哈尼,直播标题中含非法字符";
        case -4093: return "哈尼,自定义游戏名称中含非法字符";
        case -4094: return "哈尼,请选择正确的证件类型";
        case -4095: return "哈尼,该证件已被使用";
        case -4096: return "哈尼,未找到对应的经纪公司";
        case -4097: return "哈尼,经纪公司主播不支持提现和兑换";
        case -4098: return "哈尼,系统升级中该服务暂时关闭";
        case -4099: return "哈尼,金币兑换欢朋币失败,请稍后再试!";
        case -4100: return "哈尼,金豆兑换金币失败,请稍后再试!";
        case -4101: return "哈尼,金币提现失败,请稍后再试!";
        case -4102: return "哈尼,提现时间为每月的25日以后!";
        case -4103: return "哈尼,每月只能体现一次哦!";
        case -4104: return "哈尼,提现金额范围为100到800!";
        case -4105: return "哈尼,金币兑换欢朋币范围为50到1000!";
        case -4106: return "哈尼,金豆兑换金币范围为20到500!";
        case -4107: return "兑换6月1日正式启用";
        case -4108: return "欢朋移动端提现，6月1日开启";
        case -4109: return "包含敏感内容";
        //操作错误
        case -5001: return "数据录入错误";
        case -5002: return "获取数据发生意外";
        case -5003: return "获取用户信息失败";
        case -5004: return "文件录入失败";
        case -5005: return "文件保存出错";
        case -5006: return "数据删除失败";
        case -5007: return "发起直播失败";
        case -5008: return "直播截图异常";
        case -5009: return "评论失败";
        case -5010: return "意见反馈失败";
        case -5011: return "直播创建，未能正常直播";
        case -5012: return "非提现时间";
        case -5013: return "本月已经提现过了";
        case -5014: return "系统错误，提现失败";
        case -5015: return "您当前有未结束直播,不能继续直播";
        case -5016: return "与上次发起直播的设备不同，不能继续直播";
        case -5017: return "服务器繁忙,请稍后再试!";
        case -5018: return "该任务尚未完成，不能领取";
        case -5019: return "该任务不存在";
        case -5020: return "未找到该用户";
        case -5021: return "您不能添加自己为超级管理员";
        case -5022: return "您已经添加过了!";
        case -5023: return "您的账户余额不足!";
        case -5024: return "您可发布的录像空间已用完!";
        case -5025: return "系统错误, 请重试";
        case -5026: return "手机尚未认证哦～";
        case -5027: return "邮箱未认证";
        case -5028: return "未完成实名认证";
        case -5029: return '您当前正在直播, 不能发起直播';
        case -5030: return '悲剧喽!你已被关进小黑屋~';
        case -5031: return '悲剧喽!推送失败喽~';
        case -5032: return '不能禁言自己';
        case -5033: return '不能禁言主播';
        case -5034: return '不能禁言管理员';
        case -5035: return '您还不是管理员';
        case -5036: return '评论内容不能为空，赶紧说两句吧!';
        case -5037: return '请不要重复发送哦!';
        case -5038: return '官人,短信发送失败喽!';
        case -6001: return "上传配置错误";
        case -6002: return "无目录写权限";
        case -6003: return "目录路径非法";
        case -6004: return "文件不存在";
        case -6005: return "拷贝临时文件出错";
        case -6006: return "图片缩放失败";
        case -6007: return "图片裁减失败";
        case -6008: return "图片旋转失败";
        case -6009: return "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。";
        case -6010: return "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
        case -6011: return "文件只有部分被上传";
        case -6012: return "没有文件被上传";
        case -6013: return "找不到临时文件夹";
        case -6014: return "文件写入失败";
        case -6015: return "A PHP extension stopped the file upload.";
        case -6016: return "上传错误";
        case -6017: return "非法的mime";



        case -7001: return "无权限发直播";
        case -7002: return "正在其他设备上发直播";
        case -7003: return "直播参数有误或不全";
        case -7004: return "创建直播失败";
        case -7005: return "创建流失败";
        case -7006: return "您开始的直播未创建或失效";
        //约玩错误号码段
        case -8001: return "约玩错误";
        case -8002: return "获取主播资质失败";
        case -8003: return "获取主播设置失败";
        case -8004: return "获取主播评论失败";
        case -8005: return "糟糕!数据写入失败";
        case -8006: return "这不是您的订单";
        case -8007: return "都没有技能";
        case -8008: return "订单不存在";
        case -8009: return "您已经评论过了";
        case -8010: return "内容包含敏感内容";
        case -8011: return "活动不存在";
        case -8012: return "此内容无法分享";
        case -8013: return "分享失败";
        case -8014: return "此活动您的领取数量达到最大值了哦";
        case -8015: return "优惠券发放的方式错误";
        case -8016: return "没有可选的优惠券";
        case -8017: return "没有发现优惠券";
        case -8018: return "优惠券发放时间已过期";
        case -8019: return "没有人分享此优惠券";
        case -8020: return "优惠券发放方式错误";
        case -8021: return "优惠券领取失败";
        case -8022: return "此分享链接您已经领取了";
        case -8023: return "此优惠券领取完了";
        case -8024: return "没有领取到优惠券,请稍后再试吧!";
        case -8025: return "优惠券发放规则错误!";
        case -8026: return "活动信息不全!";
        case -8027: return "优惠券已发放完!";
        case -8028: return "优惠券领取超时!";
        case -8029: return "已经分享过了哦!";
        case -8030: return "活动已经结束!";
        case -8031: return "优惠券分享活动已经结束!";
        case -8032: return "已经分享过了!";
        case -8033: return "优惠券已经发放了";//推广的优惠券领取后,登录不在允许领取优惠券
        case -8034: return "此手机号已经领取过了";//推广:未领取的手机号才可以领取
        case -8035: return "服务器繁忙请稍后尝试!";

        default: return "未知错误";
    }
}

function roomerror($code)
{
    myLog($code);
    error('-ERROR' . $code);
}

/**
 * 数字验证
 * 可选参数$negative为TRUE:允许负数 FALSE:不允许负数   默认:FALSE
 *
 * @param unknown $id
 *
 * @return number
 * */
function checkInt($id, $negative = FALSE)
{
    if (is_numeric($id))
    {
        $id = (int) $id;

        if (!$negative)
        {
            if (($id > 0) && ($id < 4294967295))
                return $id;
            else
                error("-2003"); //参数值或长度越界
        } else
        {
            if (($id > -2147483648) && ($id < 2147483648))
                return $id;
            else
                error("-2003"); //参数值或长度越界
        }
    } else
    {
        error("-2004"); //缺少参数或格式错误
    }
}

function checkInt2($i, $min = null, $max = null, $errfunc = 'error')
{
    if (!isset($min))
        $min = 0;
    if (!isset($max))
        $max = 4294967295;

    if (!is_int($i))
        $errfunc("-2004");
    if ($i < $min)
        $errfunc("-2003");
    if ($i > $max)
        $errfunc("-2003");

    return $i;
}

/**
 * 字符串验证
 *
 * @param unknown $str
 *
 * @return string
 */
function checkStr($str, $length = 100)
{
    if (is_string($str))
    {
        $strLen = strlen($str);
        if (($strLen > 0) && ($strLen < $length))
            return $str;
        else
            error("-2003"); //参数值或长度越界
    } else
    {
        error("-2004"); //缺少参数或格式错误
    }
}

function isFollow($uid1, $uid2, $mydb)
{
    $res = $mydb->field('uid1')->where("uid1=$uid1 and uid2=$uid2")->select('userfollow');
    if (isset($res[0]['uid1']))
        return true;
    else
        return false;
}

function isCollect($videoid, $uid, $mydb)
{
    $res = $mydb->field('videoid')->where("videoid=$videoid and uid=$uid")->select('videofollow');
    if (isset($res[0]['videoid']))
        return true;
    else
        return false;
}

function getUserBaseInfo($uid, $mydb)
{
    $res = getUserInfo($uid, $mydb);
    if (!$res || !is_array($res[0]))
        return false;

    $userinfo = array();

    foreach ($res[0] as $key => $val)
        $userinfo[$key] = $val;

    $res = $mydb->field('level, integral, readsign,hpbean,hpcoin')->where("uid=$uid")->select('useractive');
    if (!$res || !is_array($res[0]))
        return false;
    foreach ($res[0] as $key => $val)
        $userinfo[$key] = $val;

    return $userinfo;
}

function getFollowCount($uid, $mydb)
{
    $res = $mydb->doSql("select count(*) as followcount from userfollow where uid1=$uid");
    return (int) $res[0]['followcount'];
}

function userLiveInfo($luid, $mydb)
{
    $res = $mydb->field('*')->where("uid=$luid")->order('liveid desc')->limit('1')->select('live');
    if (!$res || !is_array($res[0]))
        return false;

    return $res[0];
}

function getLevelIntegral($level, $mydb, $isAnchor = false)
{
    if ($isAnchor)
        $res = $mydb->doSql("select integral from anchorlevel where level = $level");
    else
        $res = $mydb->doSql("select integral from userlevel where level = $level");

    if (!$res || !$res[0])
        return false;

    $integral = (int) $res[0]['integral'];
    return $integral;
}

function getVideoCollectCount($videoid, $db)
{
    $res = $db->doSql("select count(*) as collectcount from videofollow where videoid = $videoid");
    return (int) $res[0]['collectcount'];
}

function getVideoCommentCount($videoid, $db)
{
    $res = $db->doSql("select count(*) as commentCount from videocomment where videoid = $videoid");
    return (int) $res[0]['commentCount'];
}

function getVideoInfo($videoid, $db)
{
    $sql = "select * from video where videoid = $videoid and status=" . VIDEO;
    $res = $db->doSql($sql);
    if (!$res || !is_array($res[0]))
        return false;

    return $res[0];
}

function getVideoBasesInfo($videoid, $db)
{
    $row = getVideoInfo($videoid, $db);
    if (!$row)
        return -2017;

    $videoinfo = array();
    $videoinfo = $row;

    $publisherid = $row['uid'];

    $res = getUserInfo($publisherid, $db);
    $row = $res[0];

    foreach ($row as $key => $val)
        $videoinfo[$key] = $val;

    $videoinfo['collectCount'] = getVideoCollectCount($videoid, $db);
    $videoinfo['commentCount'] = getVideoCommentCount($videoid, $db);
    $videoinfo['viewerRate'] = getVideoRate($videoid, $db);

    return $videoinfo;
}

/**
 * 获取随机数
 *
 * @param int $length
 * @param int $numeric
 *
 * @return string
 */
function random($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
    if ($numeric)
    {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else
    {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++)
        {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

function checkMobile($phonenumber)
{
    if (11 != strlen($phonenumber) or ! $phonenumber)
        return -111; //手机号长度不正确






        
//格式
    if (!preg_match('/(13|14|15|16|18|17|19)[0-9]\d{8}/', $phonenumber))
        return -112; //手机号错误

    return true;
}

function get_userEmailCertifyStatus($uid, $db)
{
    $row = $db->field('mail, mailstatus')->where('uid = ' . $uid)->select('userstatic');
    return $row[0];
}

/**
 * 获取用户手机认证状态
 *
 * @param $uid
 * @param $db
 *
 * @return mixed
 */
function get_userPhoneCertifyStatus($uid, $db)
{
    $row = $db->field('phone')->where('uid = ' . $uid)->select('userstatic');
    $phone = $row[0]['phone'];

    if ($phone == '')
    {
        $r['phone'] = '';
        $r['phonestatus'] = 0;
    } else
    {
        $r['phone'] = $phone;
        $r['phonestatus'] = 1;
    }

    return $r;
}

/**
 * 获取用户身份认证状态
 *
 * @param $uid
 * @param $db
 *
 * @return mixed
 */
function get_userIdentCertifyStatus($uid, $db)
{
    $row = $db->field('id,name ,papersid, status')->where('uid=' . $uid)->select('userrealname');
    if (!isset($row[0]['id']))
    {
        $r['ident'] = '';
        $r['identname'] = '';
        $r['identstatus'] = 0;
    } else
    {
        $r['ident'] = $row[0]['papersid'];
        $r['identname'] = $row[0]['name'];
        $r['identstatus'] = (int) $row[0]['status'];
    }

    return $r;
}

/**
 * 获取用户银行卡认证状态
 *
 * @param $uid
 * @param $db
 *
 * @return mixed
 */
function get_userBankCardCertifyStatus($uid, $db)
{
    $row = $db->field('id,bankid,cardid, status')->where('uid=' . $uid)->limit(1)->select('bank_card');
    if (!isset($row[0]['id']))
    {
        $r['bank'] = '';
        $r['bankstatus'] = 0;
        $r['bankid'] = '';
    } else
    {
        $r['bank'] = $row[0]['cardid'];
        $r['bankstatus'] = (int) $row[0]['status'];
        $r['bankid'] = (int) $row[0]['bankid'];
    }

    return $r;
}

function get_threePartyBindStatus($uid, $db)
{
    if (empty($uid))
    {
        return false;
    }
    $res = $db->field('channel,nick')->where("status=1 and uid=$uid")->select('three_side_user');
    if (false !== $res)
    {
        $weibo = array('status' => 0, 'nick' => '');
        $wechat = array('status' => 0, 'nick' => '');
        $qq = array('status' => 0, 'nick' => '');
        if ($res)
        {
            foreach ($res as $v)
            {
                if (isset($v['channel']) && $v['channel'] == 'weibo')
                {
                    $weibo['status'] = 1;
                    $weibo['nick'] = $v['nick'];
                }
                if (isset($v['channel']) && $v['channel'] == 'wechat')
                {
                    $wechat['status'] = 1;
                    $wechat['nick'] = $v['nick'];
                }
                if (isset($v['channel']) && $v['channel'] == 'qq')
                {
                    $qq['status'] = 1;
                    $qq['nick'] = $v['nick'];
                }
            }
            return array('weibo' => $weibo, 'wechat' => $wechat, 'qq' => $qq);
        } else
        {
            return array('weibo' => $weibo, 'wechat' => $wechat, 'qq' => $qq);
        }
    } else
    {
        return false;
    }
}

/**
 * 获取用户认证状态
 *
 * @param $uid
 * @param $db
 *
 * @return mixed
 */
function get_userCertifyStatus($uid, $db)
{
    $tmp = get_userEmailCertifyStatus($uid, $db);
    $r['email'] = $tmp['mail'];
    $r['emailstatus'] = (int) $tmp['mailstatus'];

    $tmp = get_userPhoneCertifyStatus($uid, $db);
    $r['phone'] = $tmp['phone'];
    $r['phonestatus'] = $tmp['phonestatus'];

    $tmp = get_userIdentCertifyStatus($uid, $db);
    $r['ident'] = $tmp['ident'];
    $r['identstatus'] = $tmp['identstatus'];
    $r['identname'] = $tmp['identname'];

    $tmp = get_userBankCardCertifyStatus($uid, $db);
    $r['bank'] = $tmp['bank'];
    if ($tmp['bankid'] > 0)
    {
        $row = $db->field('name')->where(['id' => $tmp['bankid']])->select('bank');
    }
    $r['bankname'] = isset($row[0]['name']) ? $row[0]['name'] : '';
    $r['bankstatus'] = $tmp['bankstatus'];

    $tmp = get_threePartyBindStatus($uid, $db);
    $r['weibostatus'] = $tmp['weibo']['status'];
    $r['weibonick'] = $tmp['weibo']['nick'];
    $r['wechatstatus'] = $tmp['wechat']['status'];
    $r['wechatnick'] = $tmp['wechat']['nick'];
    $r['qqstatus'] = $tmp['qq']['status'];
    $r['qqnick'] = $tmp['qq']['nick'];
    return $r;
}

function notLoginErrorPage($uid, $enc, $url, $db)
{
    $flag = true;
    $uid = (int) $uid;
    $enc = trim($enc);
    $url = "/main/personal/login.php?ref_url=" . urlencode($url);
    if ($uid && $enc)
    {
        $code = checkUserState($uid, $enc, $db);
        if ($code === true)
        {
            return true;
        }
    }
    echo '<div class="error_page"><div class="pic"><img src="http://dev.huanpeng.com/main/static/img/src/bg_nodata.png" alt=""/></div><span>请先登录</span><a href="' . $url . '" class="sub">前往登录</a></div>';
}

function updateAnchorLevels($luid, $exp, $db)
{
    $res = $db->query("select level, integral from anchor where uid = $luid");
    $lv = $res->fetch_assoc();

    $res = $db->query('select max(level) as `level` from anchorlevel');
    $row = $res->fetch_assoc();

    $maxLevel = $row['level'];
    $exp = $exp + $lv['integral'];

    if ($lv['level'] == $maxLevel)
    {
        return $db->query("update anchor set inegral=$exp where uid = $luid");
//		$res = $db->query("select integral from anchorlevel where `level` =  $lv");
//		$row = $res->fetch_assoc();
//		$level = $lv;
    } else
    {
        $res = $db->query("select * from anchorlevel where integral >= $exp order by level limit 1");
        $row = $res->fetch_assoc();
        $level = $row['level'];
        if ($level)
        {
            return $db->query("update anchor set integral=$exp, level=$level where uid = $luid");
        } else
        {
            return $db->query("update anchor set integral=$exp, level=$maxLevel where uid = $luid");
        }
    }

//	$anchorlevel['level'] = $level;
//	$anchorlevel['percent'] = $exp / $row['integral'];
//
//	return $anchorlevel;
}

function getApkVersion($redis)
{
    $version['version'] = $redis->get('apk:version');
    $version['name'] = $redis->get('apk:versionName');
    $version['desc'] = $redis->get('apk:versionDesc');
    $version['file'] = $redis->get('apk:fileName');
    return $version;
}

function setApkVersion($data, $redis)
{
    $redis->set('apk:version', $data['version']);
    $redis->set('apk:versionName', $data['versionName']);
    $redis->set('apk:versionDesc', $data['versionDesc']);
//    $redis->set('apk:fileName', $data['fileName']);
}

/**
 * 页数校验
 * @param type $count 总条数
 * @param type $size  数量
 * @param type $page  页数
 * @return int
 */
function returnPage($count, $size, &$page)
{
    $pageCount = (int) ($count / $size);
    if ($count % $size != 0)
    {
        $pageCount += 1;
    }

    if ($page > $pageCount)
    {
        $page = $pageCount;
    }
    return $page;
}

//身份证号校验
function identCodeValid($identCode)
{
    $pass = true;
    $city = array(
        11, 12, 13, 14, 15,
        21, 22, 23,
        31, 32, 33, 34, 35, 36, 37,
        41, 42, 43, 44, 45, 46,
        50, 51, 52, 53, 54,
        61, 62, 63, 64, 65,
        71, 81, 82, 91
    );
    $identCodeReg = "/^[1-9]\d{5}((18|19|20)\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dx]$/i";
    if (!$identCode || !preg_match($identCodeReg, $identCode))
    {
        $pass = -4;
    } elseif (!in_array((int) substr($identCode, 0, 2), $city))
    {
        $pass = -5;
    } else
    {
//		$identCode = explode('',$identCode);
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $parity = array(1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2);

        $sum = $ai = $wi = 0;

        for ($i = 0; $i < 17; $i++)
        {
            $ai = $identCode[$i];
            $wi = $factor[$i];
            $sum += $ai * $wi;
        }
        if ($parity[$sum % 11] != strtoupper($identCode[17]))
        {
            $pass = -6;
        }
    }

    return $pass;
}

//检测证件是否使用
function checkIdentID($identID, $type, $db)
{
    if (empty($identID))
    {
        return false;
    }
    $res = $db->where("papersid='$identID' and  paperstype=$type and  status  in (" . RN_PASS . ',' . RN_WAIT . ")")->select('userrealname');
    if (false !== $res)
    {
        if (!empty($res))
        {
            return 1;
        } else
        {
            return 0;
        }
    } else
    {
        return false;
    }
}

//创建网宿录像防盗链
function createSecurityChain($filename)
{
    //$ip = fetch_real_ip($port);
    //$ip = '11.22.33.44';
    $now = time();
    //$eTime = dechex($now+WS_EXPIRED);
    $eTime = dechex($now);
    $cTime = dechex($now);
    $wsSecret = md5(WS_SECURITY_CHAIN . '/' . $filename . $cTime);
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query($data);
}

/**
 * 创建网宿直播防盗链
 * @param $filename
 * @return string
 */
function createLiveSecurityChain($filename)
{
    $now = time();
    $eTime = dechex($now);
    $cTime = dechex($now);
    $wsSecret = md5(WS_SECURITY_CHAIN . '/' . $filename . $cTime);
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query($data);
}

/**
 * 创建网宿hls防盗链
 * @param $filename
 * @return string
 */
function createHlsSecret($filename)
{
    if (!is_string($filename))
    {
        return '';
    }
    $filename .= '/playlist.m3u8';
    $now = time();
    $eTime = dechex($now);
    $cTime = dechex($now);
    $wsSecret = md5(WS_SECURITY_CHAIN . '/' . $filename . $cTime);
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query($data);
}

//封面图
function sposter($poster)
{
    if (empty($poster))
    {
        return '';
    } else
    {
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
//        $iparam = createSecurityChain($poster);
        return $conf['domain-vposter'] . '/' . $poster;
    }
}

//播放地址
function sfile($file)
{
    if (empty($file))
    {
        return '';
    } else
    {
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $iparam = createSecurityChain($file);
        return $conf['domain-video'] . '/' . $file . '?' . $iparam;
    }
}

//观看流地址
function sstream($stream)
{
    if (empty($stream))
    {
        return '';
    } else
    {
        $st = "liverecord/" . $stream;
        $iparam = createLiveSecurityChain($st);
        return $stream . '?' . $iparam;
    }
}

function v6RegChannel($uid, $db)
{
    $channel = $_COOKIE['datamain'];
    if ($channel == '6cn')
    {
        addRegChannel(7001, $uid, $db); //添加到注册渠道
    }
}

/* * 注册渠道统计
 * @param string $channel 渠道
 * @param int  $uid  用户id
 * @param $db
 * @return bool
 */

function addRegChannel($channel, $uid, $db)
{
    if (empty($uid))
    {
        return false;
    }
    $res = $db->insert('reg_channel_record', array('channel' => $channel, 'uid' => $uid));
    if (false !== $res)
    {
        hpdelCookie('datamain');
        return true;
    } else
    {
        return false;
    }
}

if (!function_exists('write_log'))
{

    /**
     * 日志方法
     * @param  string|array $content  日志内容
     * @param  string $logName 自定义日志名，默认为huanpeng_common
     * @return void
     */
    function write_log($content = '', $logName = 'huanpeng_common')
    {

        $logName = $logName ?: 'huanpeng_common';
        $content = is_string($content) ? $content : json_encode($content);
        $port = 0;
        $mode = 'fpm-fcgi';
        if (IS_CLI)
        {
            $clientIp = $serverIp = getHostName();
            $mode = 'cli';
        } else
        {
            $clientIp = fetch_real_ip($port);
            $serverIp = $_SERVER['SERVER_ADDR'];
        }
        $date = date('Y-m-d H:i:s');
        $content = "{$date} | {$content} | client_ip:{$clientIp} | server_ip:{$serverIp}| mode:{$mode}\n";
        $logFile = LOG_DIR . $logName . '.log.' . date('Ymd');
        if (!file_exists($logFile))
        {
            touch($logFile);
            @chmod($logFile, 0777);
            clearstatcache();
        }
        file_put_contents($logFile, $content, FILE_APPEND);
        return;
    }

}

if (!function_exists('check_phone_valid'))
{

    /**
     * 校验用机号是否合法
     * @param  int $phone 手机号
     * @return boolean
     */
    function check_phone_valid($phone)
    {
        return preg_match('#^1[34578]\d{9}$#', $phone) ? true : false;
    }

}

if (!function_exists('xss_clean'))
{

    function xss_clean($var)
    {
        if (empty($var))
        {
            return $var;
        }

        if (is_array($var))
        {
            foreach (array_keys($var) as $key)
            {
                $var[$key] = xss_clean($var[$key]);
            }

            return $var;
        }

        return filter_var($var, FILTER_SANITIZE_STRING);
    }

}

if (!function_exists('strip_tags_deep'))
{

    function strip_tags_deep($value)
    {
        return is_array($value) ? array_map('strip_tags_deep', $value) : strip_tags($value);
    }

}

if (!function_exists('filter_emoji'))
{

    /**
     * 过滤 emoji 字符支持 字符、数组 递归过滤
     * @param  string | array $var
     * @return string | array | ''
     */
    function filter_emoji($var)
    {
        if (empty($var))
        {
            return $var;
        }

        if (is_array($var))
        {

            foreach (array_keys($var) as $key)
            {
                $var[$key] = filter_emoji($var[$key]);
            }

            return $var;
        }

        $var = preg_replace_callback('#.#u', function (array $match)
        {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $var);

        return $var;
    }

}


if (!function_exists('get_hp_env'))
{

    /**
     * 获取系统配置环境
     * @return string
     */
    function get_hp_env()
    {
        return strtolower((string) $GLOBALS['env']);
    }

}


if (!function_exists('get_hp_config'))
{

    /**
     * 获取配置数据方法
     * @param  string $f    配置文件名 如 log.php
     * @param  string $name 需要获取的配置项 file
     * @return array | false
     */
    function get_hp_config($f = '', $name = '')
    {
        $f = trim($f);
        $name = trim($name);
        if (!$f)
        {
            return false;
        }
        static $_config = [];
        if (!isset($_config[$f]))
        {
            $file = CONFIG_DIR . $f . '.php';
            if (file_exists($file))
            {
                $_config[$f] = require $file;
            }
        }
        return $name ? (isset($_config[$f][$name]) ? $_config[$f][$name] : false) : (isset($_config[$f]) ? $_config[$f] : false);
    }

}

if (!function_exists('get_hp_mysql_conf'))
{

    /**
     * 获取mysql配置
     * @return array |false
     */
    function get_hp_mysql_conf()
    {
        return get_hp_config('mysql/mysql.' . get_hp_env());
    }

}

if (!function_exists('get_hp_redis_conf'))
{

    /**
     * 获取redis配置
     * @return array |false
     */
    function get_hp_redis_conf()
    {
        return get_hp_config('redis/redis.' . get_hp_env());
    }

}

if (!function_exists('array_values_to_string'))
{

    /**
     * 数组值统一转换成 string
     * @param  array $var
     * @return array
     */
    function array_values_to_string($var)
    {

        if (is_array($var))
        {

            foreach (array_keys($var) as $key)
            {
                $var[$key] = array_values_to_string($var[$key]);
            }

            return $var;
        }
        return (string) $var;
    }

}

if (!function_exists('render_json'))
{

    /**
     * json 数据 渲染 输出
     * @param  array | string $content  需要渲染数据
     * @param  string $errorCode 错误码 错误渲染请使用 render_error_json 方法）
     * @param  string $type   1前端不展示给用户，2前端展示给用户 （错误渲染请使用 render_error_json 方法）
     * @return json
     */
    function render_json($content = '', $errorCode = '0', $type = '1')
    {

        if (!headers_sent())
        {
            header('Content-type: application/json; charset=utf-8', true);
        }

        if ($content === null)
        {
            $content = new stdClass;
        }

        if ($errorCode != '0')
        {

            $data['status'] = '0';
            $data['content']['code'] = $errorCode;

            if (is_array($content))
            {
                $data['content'] = array_merge($data['content'], $content);
            } else
            {
                $data['content']['desc'] = $content;
            }

            $data['content']['type'] = $type;
        } else
        {
            $data['status'] = '1';
            $data['content'] = $content;
        }
        $data = xss_clean($data);
        exit(hp_json_encode(array_values_to_string($data)));
    }

}

if (!function_exists('render_error_json'))
{

    /**
     * json 错误渲染输出
     * @param  string $content    错误提示信息
     * @param  string $errorCode  错误码
     * @param  string $type       1前端不展示给用户，2前端展示给用户
     * @return json
     */
    function render_error_json($content = '', $errorCode = '-1', $type = '1')
    {
        render_json($content, $errorCode, $type);
    }

}

function getOtid()
{
    return intval(microtime(true) * 10000 . rand(1000, 9999));
}

/**
 * 递归的 array_map();
 * @param type $filter  回调函数
 * @param type $data    数据
 * @return array
 */
function array_map_recursive($filter, $data)
{
    $result = array();
    foreach ($data as $key => $val)
    {
        $result[$key] = is_array($val) ? array_map_recursive($filter, $val) : call_user_func($filter, $val);
    }
    return $result;
}

if (!function_exists('multiArraySort'))
{

    /**
     * 二维数组排序
     *
     * @param array  $multi_array 待排序的数组
     * @param string $sort_key    要排序的字段
     * @param string $sort        排序的规则
     *
     * @return array
     */
    function multiArraySort($multi_array, $sort_key, $tow_sort_key, $sort = SORT_DESC)
    {
        if (is_array($multi_array))
        {
            foreach ($multi_array as $row_array)
            {
                if (is_array($row_array))
                {
                    $key_array[] = $row_array[$sort_key];
                    $tow_key_array[] = $row_array[$tow_sort_key];
                } else
                {
                    return false;
                }
            }
        } else
        {
            return false;
        }
        array_multisort($key_array, $sort, $tow_key_array, $sort, $multi_array);
        return $multi_array;
    }

}

if (!function_exists('ArraySort'))
{

    /**
     * 二维数组排序（多字段 多排序）
     *
     * @param array  $multi_array   待排序的数组
     * @param string $sort_key      要排序的字段1
     * @param string $tow_sort_key  要排序的字段2
     * @param string $sort1         排序的规则1
     * @param string $sort2         排序的规则2
     *
     * @return array
     */
    function ArraySort($multi_array, $sort_key, $tow_sort_key, $sort1 = SORT_DESC, $sort2 = SORT_DESC)
    {
        if (is_array($multi_array))
        {
            foreach ($multi_array as $row_array)
            {
                if (is_array($row_array))
                {
                    $key_array[] = $row_array[$sort_key];
                    $tow_key_array[] = $row_array[$tow_sort_key];
                } else
                {
                    return false;
                }
            }
        } else
        {
            return false;
        }
        array_multisort($key_array, $sort1, $tow_key_array, $sort2, $multi_array);
        return $multi_array;
    }

}

if (!function_exists('signCheck'))
{

    /**
     * 比率｜内部发放 密钥校验
     *
     * @param type $data
     *
     * @return boolean
     */
    function signCheck($data, $secretKey)
    {
        //验证参数中是否有签名
        if (!isset($data['sign']) || !$data['sign'])
        {
            return false;
        }
        //验证请求有无时间戳
        if (!isset($data['tm']) || !$data['tm'])
        {
            return false;
        }
//	验证请求，3分钟实效
        if (time() - $data['tm'] > 180 || time() - $data['tm'] < 0)
        {
            return false;
        }
        $sign = $data['sign'];
        unset($data['sign']);
        $data['tm'] = (int) $data['tm'];
        $sign2 = buildSign($data, $secretKey, false);
        if ($sign === $sign2)
        {
            return true;
        } else
        {
            return false;
        }
    }

}

if (!function_exists('get_user_integral_by_level'))
{

    function get_user_integral_by_level($level = null)
    {
        $levelData = get_hp_config('level/user');
        if ($level === null)
        {
            return $levelData;
        }

        return isset($levelData[$level]) ? (int) $levelData[$level] : 0;
    }

}


if (!function_exists('get_anchor_integral_by_level'))
{

    function get_anchor_integral_by_level($level = null)
    {
        $levelData = get_hp_config('level/anchor');
        if ($level === null)
        {
            return $levelData;
        }

        return isset($levelData[$level]) ? (int) $levelData[$level] : 0;
    }

}

function lockRequest($key, $redis = null, $limit = 1, $expire = 10)
{
    if (is_null($redis))
    {
        $redis = new RedisHelp();
    }

    $key = "LOCK_" . $key;

    $redisObj = $redis->getMyRedis();
    $count = $redisObj->incr($key);

    if ($count > $limit)
    {
        return true;
    } else
    {
        $redisObj->expire($key, $expire);

        return false;
    }
}

function unLockRequest($key, $redis = null)
{
    if (is_null($redis))
    {
        $redis = new RedisHelp();
    }

    $key = "LOCK_" . $key;

    $redisObj = $redis->getMyRedis();
    $redisObj->del($key);
}

if (!function_exists('array_convert_encoding'))
{

    /**
     * 数组或字符串 字符集转换
     * @param  array $var
     * @return array
     */
    function array_convert_encoding($var, $charset = 'UTF-8')
    {

        if (is_array($var))
        {

            foreach (array_keys($var) as $key)
            {
                $var[$key] = array_convert_encoding($var[$key], $charset);
            }

            return $var;
        }

        return mb_convert_encoding($var, $charset);
    }

}

if (!function_exists('hp_json_encode'))
{

    /**
     * json_encode
     * @param  string |array $var
     * @return string
     */
    function hp_json_encode($var)
    {

        $jsonStr = json_encode($var, JSON_UNESCAPED_UNICODE);
        if ($jsonStr !== false)
        {
            return $jsonStr;
        }

        return json_encode(array_convert_encoding($var, 'UTF-8'), JSON_UNESCAPED_UNICODE);
    }

}


if (!function_exists('token_check'))
{

    /**
     * 比率｜内部发放 密钥校验
     *
     * @param type $data
     *
     * @return boolean
     */
    function token_check($data, $secretKey)
    {
        //验证参数中是否有签名
        if (!isset($data['token']) || !$data['token'])
        {
            return false;
        }

        $token = $data['token'];
        unset($data['token']);
        $sign = token_create($data, $secretKey, false);
        return $token === $sign ? true : false;
    }

}

if (!function_exists('token_create'))
{

    function token_create($data, $secretKey, $urlEncode = true)
    {
        $data = array_values_to_string($data);
        foreach ($data as $key => $val)
        {
            $data[$key] = $urlEncode ? urlencode($val) : $val;
        }
        ksort($data);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sign = md5(sha1($data . $secretKey));
        return $sign;
    }

}

if (!function_exists('get_address_province_by_pid'))
{

    /**
     * 获取省名
     * @param  [type] $pid [description]
     * @return [type]      [description]
     */
    function get_address_province_by_pid($pid = null)
    {
        $provinceData = get_hp_config('address/province');

        if ($pid === null)
        {
            return $provinceData;
        }

        return isset($provinceData[$pid]) ? $provinceData[$pid]['name'] : false;
    }

}

if (!function_exists('get_address_city_by_cid_pid'))
{

    /**
     * 获取城市名
     * @param  [type] $pid [description]
     * @return [type]      [description]
     */
    function get_address_city_by_cid_pid($cid = null, $pid = null)
    {

        $cityData = get_hp_config('address/city');

        if ($pid === null && $cid === null)
        {
            return $cityData;
        }
        $uk = "{$cid}_{$pid}";
        return isset($cityData[$uk]) ? $cityData[$uk]['name'] : false;
    }

}

if (!function_exists('getLastMonthDays'))
{

    /**
     * 获取给出日期的上个月的第一天和最后一天
     * @param int $date
     * @return array
     */
    function getLastMonthDays($date = 0)
    {
        if (!$date)
        {
            $date = time();
        } else
        {
            $date = strtotime($date);
        }
        $timestamp = strtotime(date('Y-m-01',$date)) - 86400;

        $firstday = date('Y-m-01', $timestamp);
        $lastday = date('Y-m-t',$timestamp);
        
        return array($firstday, $lastday);
    }

}


if (!function_exists('getNextMonthDays'))
{

    /**
     * 获取给出日期的下个月的第一天和最后一天
     * @param int $date
     * @return array
     */
    function getNextMonthDays($date = 0)
    {
        if (!$date)
        {
            $date = time();
        } else
        {
            $date = strtotime($date);
        }
        
        $timestamp = strtotime(date('Y-m-t',$date)) + 86400;
        
        $firstday = date('Y-m-01', $timestamp);
        $lastday = date('Y-m-t',$timestamp);
        
        return array($firstday, $lastday);
    }

}

if(!function_exists('indexShuffle'))
{
    /**
     * 带索引打乱数组顺序
     * @param array $arr
     * @return array
     */
    function indexShuffle(array $arr)
    {
        $copy = [];
        while(count($arr))
        {
            $element = array_rand($arr);
            $copy[$element] = $arr[$element];
            unset($arr[$element]);
        }
        return $copy;
    }
}
