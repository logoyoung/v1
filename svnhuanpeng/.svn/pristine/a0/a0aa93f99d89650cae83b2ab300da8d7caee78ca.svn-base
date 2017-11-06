<?php
use Org\Util\Date;
function get_uid($app=null){
    is_null($app) and $app=BIND_MODULE;
    switch ($app){
        case 'Www':
        case 'Wap':
        case 'Api':
            return \HP\User\Www::getUid();
        case 'Admin':
            return \HP\Op\Admin::getUid();
    }
}

function get_user($app=null){
    return \HP\Op\Admin::getUser();
}
function get_date($str=null){
    return is_null($str)?date('Y-m-d H:i:s'):date('Y-m-d H:i:s', is_numeric($str)?$str:strtotime($str));
}
/**
 * coka 2016-04-29 
 * @param type $days
 * 向前取几天的日期
 * beforedays(-7)
 */
function beforedays($days=0){
    for($i=$days;$i<=0;$i++){
        $day = date('Y-m-d',strtotime($i.' day'));
        $reslist[]=$day;
    }
    return $reslist;
}
/*
 * coka,2016-04-29
 * 向后几天的日期
 * afterdays(5)
 */
function afterdays($days=0){
       
       for($i=0;$i<$days;$i++){
           $day = date('Y-m-d',strtotime($i.' day'));
           $reslist[]=$day;
       }
       return $reslist;
        
}

/**
 * coka 2016-5-03
 * @param type $month
 * 向前取几月的日期
 * beforeMonths(-7)
 */
function beforeMonths($months=0){
    for($i=$months;$i<=0;$i++){
        $month = date('Y-m',strtotime(date('Y-m-01')."$i month"));
        $reslist[]=$month;
    }
    return $reslist;
}
/**
 * coka 2016-5-04
 * @param type $month
 * 向后取几月的日期
 * beforeMonths(7)
 */
function afterMonths($months=0){
    for($i=0;$i<=$months;$i++){
        $month = date('Y-m',strtotime(date('Y-m-01')."$i month"));
        $reslist[]=$month;
    }
    return $reslist;
}

function get_useragent(){
    return $_SERVER['HTTP_USER_AGENT'];
}
function strcut($str,$length){
    return mb_substr($str,0,$length,'utf-8');
}
function secure_gettoken($key){
    return \HP\Secure\Token::get($key);
}
function composer_autoload(){
    return require_once THINK_PATH.'Library/Composer/vendor/autoload.php';
}
function showmoney($number,$type=1){
    $zero=$type==2?'0.00':'0';
    if(!is_numeric($number))return $zero;
    return $number>0?number_format($number,2):$zero;
}
function microtime_float()
{
    return microtime(1);
}
function microtime_int(){
    return intval(microtime(1)*10000);
}
/**
 * 返回一个13位的微秒数
 * @return type
 */
function get_number(){
    return intval((microtime(1)-1300000000)*10000);
}
function isPc(){
    return !isWap() and !isApp();
}
function isWap(){
    return defined('HPHPPE') and HP_TYPE!='pc';
}
function isIOS(){
    return defined('HP_TYPE') and HP_TYPE=='ios';
}
function isAndroid(){
    return defined('HP_TYPE') and strpos(HP_TYPE,'android')!==false;
}
function isWeixin(){
    return stripos($_SERVER['HTTP_USER_AGENT'],'micromessenger')!==false;
}
function isApp(){
    return isIOS()||isAndroid();
}
function get_fromtype(){
    if(isPc())return 1;
    if(isIOS())return 3;
    if(isAndroid())return 4;
    if(isWeixin())return 5;
    if(isWap())return 2;
    return 0;
}
/**
 * 是否是前台程序
 */
function isFront(){
    return defined('BIND_MODULE') and in_array(BIND_MODULE,['Www','Wap']);
}
/*
 * 是否是后台程序
 */
function isAdmin(){
    return defined('BIND_MODULE') and BIND_MODULE=='Admin';
}
function switchWap(){
    return cookie('layout','w',['expire'=>8640000]);
}
function switchPc(){
    return cookie('layout','p',['expire'=>8640000]);
}

/*
 * 是否是生产环境
 */
function isRelease(){
    return HP_DOMAIN=='op.huanpeng.com';
}
/*
 * 是否是测试环境
 */
function isBeta(){
    return HP_DOMAIN=='oppre.huanpeng.com';
}
/*
 * 是否是开发环境
 */
function isDev(){
    return HP_DOMAIN=='opdev.huanpeng.com';
}


/*
 * 设置当前session过期时间
 * 设置的是memcache过期时间
 */
function session_set_expire($time,$sessID=null) {
    $sessID or $sessID=session_id();
    if(is_numeric($time)){
        S('hpsidexpire_'.$sessID,$time,$time);
    }elseif(is_null($time)){
        S('hpsidexpire_'.$sessID,null);
    }
}

/*
 * 根据fid获取连接
 */
function getpic_fromfid($fid,$sign=null){
    return \HP\File\Read::getPublicUrlByFid($fid, $sign);
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
    $res = M('task')->add($data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
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
 * 把秒数格式化
 * @param 秒数 $second
 * @return boolean|string
 */
function secondFormatH($second)
{
    if (empty($second)) {
        return false;
    }
    $str = '';
    $h = floor($second / 3600);
    $m = floor(($second % (3600 * 24)) % 3600 / 60);
    $s = floor(($second % (3600 * 24)) % 60);
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

//头像地址
function avator($file = ''){
	if(empty($file)){
		return DEFAULT_PIC;
	}else{
		return DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $file;
	}
}

//获取图片
function getPic($file = ''){
    if(empty($file)){
        return '';
    }else{
        return DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $file;
    }
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

//创建网宿防盗链
function createSecurityChain($filename){
    $now = time();
    $eTime = dechex($now);
    $cTime = dechex($now) ;
    $wsSecret = md5(WS_SECURITY_CHAIN.'/'.$filename.$cTime);
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query($data);
}

//天数集合
function get_days($type,$stime,$etime){
    if(!$stime || !$etime)return [];
    switch ($type) {
        case 'day':
            $key ="Y-m-d";
            break;
        case 'week':
            $key = "Y-W";
            break;
        case 'month':
            $key="Y-m";
            break;
        default:
            $key="Y-m-d";
    }
    $days = (strtotime($etime) - strtotime($stime))/86400;
    if($days>=0){
        for ($i=0;$i<=$days;$i++){
            $dates[date($key,strtotime($stime)+86400*$i)]['date'] = date($key,strtotime($stime)+86400*$i);
        }
    }
    return $dates;
}

function joinInsertSql($data) 
{
    if(is_array($data)) {
        $key = $value = '';
        foreach($data as $k=>$v) {
            if(!is_array($v)) {
                $key .= '`' . $k . '`,';
                $value .= '"' . $v . '",';
            } else {
                return '';
            }
        }
        $sql = ' (' . rtrim($key, ',') . ') VALUES (' . rtrim($value, ',') . ') ';
        return $sql;
    }
    return '';
}

//2017年6月7日，zwq add 与前台一致
function hp_commonMsgDecode($msgGz)
{
    $msg = hp_base64Decode($msgGz);
    $msg = gzinflate( $msg );

    return $msg;
}

function hp_commonMsgEncode($msg)
{
    $msgGz = gzdeflate( $msg, 6 );
    $msgGz = hp_base64Encode( $msgGz );

    return $msgGz;
}

function hp_base64Decode($str)
{
    $base64Str = str_replace( array( '(', ')', '@' ), array( '+', '/', '=' ), $str );
    $base64Str = base64_decode( $base64Str );

    return $base64Str;
}

function hp_base64Encode($str)
{
    $base64Str = base64_encode( $str );
    $base64Str = str_replace( array( '+', '/', '=' ), array( '(', ')', '@' ), $base64Str );

    return $base64Str;
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
function addRateChangeRecord( $data )
{
	if( empty( $data ) )
	{
		return false;
	}
	$Dao=D('rate_change_record');
	$res = $Dao->add($data);
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

/**
 * 添加主播角色变更纪录
 *
 *@param $data
 * $data＝array(){
 *    uid
 *    cid
 *    adminid
 *    desc
 * }
 * @param $db
 *
 * @return bool
 */
function addRoleChangeRecord( $data )
{
	if( empty( $data ) )
	{
		return false;
	}
	$Dao=D('anchor_change_record');
	$res = $Dao->add($data);
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function getBeforeRateByUid( $uid )
{
	if( empty( $uid ) )
	{
		return false;
	}
	$Dao=D('anchor');
	$res = $Dao->field( 'cid,rate' )->where( "uid=$uid" )->limit( 1 )->select();
	if( false !== $res )
	{
		return $res[0];
	}
	else
	{
		return false;
	}
}


/**
 * 更新通知状态
 *
 * @param int $rate_change_id
 * @param object $db
 *
 * @return bool
 */
function updateNoticStatus( $rate_change_id)
{
	if( empty( $rate_change_id ) )
	{
		return false;
	}
	$Dao=D('rate_change_record');
	$res = $Dao->where( "id in ($rate_change_id)" )->save(array( 'status' => 1 ) );
	if( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 财务系统成功返回,后续操作失败日志
 * @param $type
 * @param $desc
 * @param $db
 */
function  unsuccessLogForFinanceBack($title,$desc){
	$data = array(
		'title' => $title,
		'desc' => json_encode($desc),
	);
	$Dao=D('unsuccess_log_for_financeBack');
	$Dao->add($data);

}

/**
 * 秘钥生成
 * @param      $data
 * @param      $secretKey
 * @param bool $urlEncode
 *
 * @return string
 */
function buildSign($data, $secretKey,$urlEncode=true) {
	foreach ($data as $key => $val) {
		$data[$key] = $urlEncode ? urlencode($val) : $val;
	}
	ksort($data);
	$data = json_encode($data, JSON_UNESCAPED_UNICODE);
	$sign = md5(sha1($data . $secretKey));
	return $sign;
}


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

function get_secure_phone($phone){
	if(!is_numeric($phone))
		return '';
	$pat = '/(\d{3})(\d{4})(\d{4})/';
	$phone = preg_replace($pat,'$1****$3',$phone);
	return $phone;
}
function get_secure_cert($cert){
	if(!$cert)
		return '';
	$pat = '/(\d{4})(\d+)(\w{4})/';
	$cert = preg_replace($pat,'$1****$3',$cert);
	return $cert;
}
function get_secure_bankcard($bankcard){
	if(!is_numeric($bankcard))
		return '';
	$pat = '/(\d{4})(\d+)(\d{4})/';
	$bankcard = preg_replace($pat,'$1****$3',$bankcard);
	return $bankcard;
}

/**
 * 根据ip获取所属地区
 * @return json string
 */
function get_ip_address($ip)
{
    if(!strstr($ip, '.')) {
        $ip = long2ip($ip);
    }
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $ip;
    $str = \HP\Util\Curl::get($url);
    //$str = file_get_contents($url);
    return $str;
}


function numToRmb($num)
{
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    //精确到分后面就不要了，所以只留两个小数位
    $num = round($num, 2);
    //将数字转化为整数
    $num = $num * 100;
    if (mb_strlen($num) > 10) {
        return "金额太大，请检查";
    }
    $i = 0;
    $c = "";
    while (true) {

        $n = $num % 10;
        //每次将最后一位数字转化为中文
        $p1 = mb_substr($c1, $n, 1);
        $p2 = mb_substr($c2, $i, 1);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        //去掉数字最后一位了
        $num = (int)($num / 10);
        if ($num == 0) {  //结束循环
            break;
        }
        $i++;
    }
    $j = 0;
    $slen = mb_strlen($c);
    while ($j < $slen) {
        //utf8一个汉字相当3个字符
        $m = mb_substr($c, $j, 2);
        //处理数字中很多0的情况,每次循环去掉一个汉字“零”
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $c = mb_substr($c, 0, $j) . mb_substr($c, $j + 1);
            $j--;
            $slen--;
        }
        $j++;
    }
    //这个是为了去掉类似23.0中最后一个“零”字
    if (mb_substr($c, mb_strlen($c) - 1, 1) == '零') {
        $c = mb_substr($c, 0, mb_strlen($c) - 1);
    }
    //将处理的汉字加上“整”
    if (empty($c)) {
        return "零元整";
    }else{
        return $c . "整";
    }
}

function ymd2md(&$value, $key)
{
    $value = date('m-d', strtotime($value . ' 00:00:00'));
}