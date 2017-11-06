<?php
/**
 * 
 *   */
//header("Content-Type:text/html;charset:UTF-8");
define('debug', 'false');
//
define('url_access_token', 'https://api.weixin.qq.com/cgi-bin/token');
//
define('url_jsapi_ticket', 'https://api.weixin.qq.com/cgi-bin/ticket/getticket');

$appid = 'wx79c0b818ca367bc6';
$secret = 'b0d516d0a423589dd638a1a9d1d2e772';//'e91d36f15e8914378225ee18404d84f6';
$timestamp = time();
$nonceStr = substr(md5($timestamp), 0,16);
$signature = '';

//JS接口列表
$jsApiList = array(
    'onMenuShareTimeline',
    'onMenuShareAppMessage',
    'onMenuShareQQ',
    'onMenuShareWeibo',
    'onMenuShareQZone' 
);

/**
 * 获取access_token
 * @param string $appid
 * @param string $secret
 * @return boolean|mixed  */
function get_access_token($appid='', $secret=''){
    if(!$appid || !$secret)
        return false;
    //todo
    $param = array(
        'grant_type'=>'client_credential',
        'appid'=>$appid,
        'secret'=>$secret
    );
    $param = http_build_query($param);
    $url = url_access_token.'?'.$param;
    $back = http_get($url);
    $back = json_decode($back,true);
    if(!$back || isset($back['errcode']))
        return false;
    //to do
    return $back['access_token'];   
}
function get_jsapi_ticket($access_token = ''){
    if(!$access_token)
        return false;
    $param = array(
        'access_token'=>$access_token,
        'type'=>'jsapi'
    );
    $param = http_build_query($param);
    $url = url_jsapi_ticket.'?'.$param;
    $back = http_get($url);
    $back = json_decode($back,true);//var_dump($back);
    if(!$back || $back['errcode']!=0 || $back['errmsg']!='ok')
        return false;
    return $back['ticket'];
}
/**
 * 
 * @param string $data
 * @return boolean|string  */
function get_signature($data = ''){
    if(!$data || !is_array($data))
        return false;
    ksort($data);
    $data = http_build_query($data);
    $data = urldecode($data);
    return sha1($data);
}

/**
 * http get
 * @param unknown $url
 * @return mixed|boolean  */
function http_get($url){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}




/*===================start=============================*/
//获取access_token
$acess_token = get_access_token($appid,$secret);
//var_dump($acess_token);
//获取jsapi_ticket
$jsapi_ticket = get_jsapi_ticket($acess_token);
//var_dump($jsapi_ticket);


//获取 signature
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$data = array(
    'noncestr'=>$nonceStr,
    'jsapi_ticket'=>$jsapi_ticket,
    'timestamp'=>$timestamp,
    'url'=>$url
);
$signature = get_signature($data);  
//var_dump($signature); 
 
//拼接权限验证限制
$config = array(
    'debug'=>debug,
    'appId'=>$appid,
    'timestamp'=>$timestamp,
    'nonceStr'=>$nonceStr,
    'signature'=>$signature
); 
//$config = json_encode($config);
//获取直播分享相关信息

//var_dump($signature);
?>


