<?php

function error($code) {
    $err = array(
        'stat' => 0,
        'err' => array(
            'code' => $code,
            'desc' => errDesc($code),
        )
    );

    exit(json_encode($err, JSON_UNESCAPED_UNICODE));
}

function errDesc($code) {
    switch ($code) {
        case -1001: return '用户不存在';
        case -1002: return '密码错误';
        case -1003: return '用户不存在';
        case -1004: return '登录失败';
        case -1005: return '登录异常，请重新登录';
        case -1006: return '登录异常，请重新登录';
        case -1007: return '缺少参数，或者参数类型错误';
        case -1008: return '邮箱格式错误';
        case -1009: return '没有下一个数据了';
        case -1010: return '认证失败，请稍后再试';
        case -1011: return "消息发送失败";
        case -1012: return "直播已停止或非法";
        case -1013: return "暂无数据";
        case -1014: return "系统繁忙,请稍候重试!";
        case -1015: return "游戏ID不能为空!";
        case -1016: return "推荐的类型不能为空!";
        case -1017: return "系统检测到有重复的用户存在，请核对主播昵称!";
        case -1018: return "没有找到相应数据，请确认输入是否正确!";
        case -1019: return "输入内容必须为数字";
        case -1020: return "推荐位已满～";
        case -1021: return "要推荐的主播不在待推荐列表里!";
        case -1022: return "添加条数已超出推荐最大上限!";
        case -1023: return "请求参数非法";
        case -1024: return "标题不能为空";
        case -1025: return "内容不能为空";
        case -1026: return "资讯类型不能为空";
        case -1027: return "广告类型非法";
        case -1028: return "广告位置非法";
        case -1029: return "广告封面图不能为空";
        case -1030: return "封面图不能为空";
        case -1031: return "没有封面图,不能添加到轮播推荐";
        case -1032: return "排序不能为空";
        case -1033: return "楼层数不能为空";
        case -1034: return "楼层数超过系统设置";
        case -1035: return "推荐主播数大于系统上限";
        case -1036: return "外部链接不能为空";
        case -1037: return "搜索关键字不能为空";
        case -1038: return "经纪公司名称不能为空";
        case -1039: return "未找到对应的经纪公司!";
		case -1040: return "用户组不存在";
		case -1041: return "没有访问该项目的权限";
		case -1042: return "没有访问该页面的权限";

        default: return '未知错误';
    }
}

function succ($content = array()) {
    $succ = array(
        'stat' => 1,
        'resuData' => $content
    );

    exit(json_encode($succ));
}

function checkEmailFormat($email) {
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
function verifySign($data, $secretKey) {
    //验证参数中是否有签名
    if (!isset($data['sign']) || !$data['sign']) {
        return false;
    }
    //验证请求有无时间戳
    if (!isset($data['tm']) || !$data['tm']) {
        return false;
    }
    //验证请求，3分钟实效
    if (time() - $data['tm'] > 180 || time() - $data['tm'] < 0) {
        return false;
    }
    $sign = $data['sign'];
    unset($data['sign']);
	$sign2 = buildSign($data,$secretKey,false);
//    ksort($data);
//    foreach ($data as $key => $val) {
//        $data[$key] = urldecode($val);
//    }
//    $tmpdata = json_encode($data, JSON_UNESCAPED_UNICODE);
////    $tmpdata = json_encode($data);
//    $sign2 = md5(sha1($tmpdata . $secretKey));

    if ($sign === $sign2) {
        return true;
    } else {
        return false;
    }
}

function buildSign($data, $secretKey,$urlEncode=true) {
    foreach ($data as $key => $val) {
        $data[$key] = $urlEncode ? urlencode($val) : $val;
    }
    ksort($data);
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    $sign = md5(sha1($data . $secretKey));

    return $sign;
}

function toString($mix) {
    if (is_string($mix)) {
        return $mix;
    }
    if (is_int($mix) || is_bool($mix) || is_float($mix) || is_double($mix)) {
        return "$mix";
    }
    if (is_array($mix)) {
        foreach ($mix as $key => $v) {
            $mix[$key] = toString($v);
        }
        return $mix;
    }

    return "";
}

function mylog($msg, $logfile = LOGFN_GENERAL) {
    $msg = '[' . getmypid() . '] [' . get_datetime() . '] ' . $msg . "\n";
    $r = file_put_contents($logfile, $msg, FILE_APPEND);
    return $r;
}

function get_datetime($tm = null) {
    if (!$tm)
        $tm = time();
    return date("Y-m-d H:i:s", $tm);
}

function Page($count, $size, $page) {
    $total = (int) ceil($count / $size);
    if ($total == 0) {
        $page = 1;
    } else {
        if ($page > $total) {
            $page = $total;
        }
    }
    return $page;
}

/**
 * 二维数组排序
 * @param type $array
 * @param type $sort_key
 * @param type $sort
 * @return boolean
 */
function dyadicArray($array, $sort_key, $sort = SORT_DESC) {
    if (is_array($array)) {
        foreach ($array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_array, $sort, $array);
    return $array;
}


function checkInt($id, $negative = FALSE) {
    if (is_numeric($id)) {
        $id = (int) $id;

        if (!$negative) {
            if (($id > 0) && ($id < 4294967295))
                return $id;
            else
                error("-2003"); //参数值或长度越界  			      			       
        } else {
            if (($id > -2147483648) && ($id < 2147483648))
                return $id;
            else
                error("-2003"); //参数值或长度越界
        }
    } else {
        error("-2004"); //缺少参数或格式错误
    }
}
?>