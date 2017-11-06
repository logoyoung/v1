<?php


function redirect($url = '')
{
	if(strtolower(substr($url, 0, 7)) != 'http://')	{
		$url = get_instance()->config->config['adminuser_url'] . $url;
	}
    header("Location: " . $url); exit;
}

function redirectJs($url = '', $info)
{
    if(strtolower(substr($url, 0, 7)) != 'http://')	{
		$url = get_instance()->config->config['adminuser_url'] . $url;
	}

    echo "<script>alert('" . $info . "'); window.location.href='" . $url ."';</script>"; exit;
}

/**
 * 把秒数格式化
 * @param 秒数 $second
 * @return boolean|string
 */
function secondFormat($second)
{
    if (empty($second)) {
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
    if ($str != '' || !empty($s)) {
        $str .= $s . '秒';
    }
    return $str;
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

function error($code) {
    $err = array(
        'status' => 0,
        'data' => array(
            'code' => $code,
            'desc' => errDesc($code),
        )
    );

    exit(json_encode($err));
}

function errDesc($code) {
    switch ($code) {
        case -1001: return '用户不存在';
        case -1002: return '密码错误';
        case -1003: return '用户不存在';
        case -1004: return '登录失败';
        default: return '未知错误';
    }
}

function succ($content = array()) {
    $succ = array(
        'stats' => 1,
        'data' => $content
    );
    exit(json_encode($succ));
}