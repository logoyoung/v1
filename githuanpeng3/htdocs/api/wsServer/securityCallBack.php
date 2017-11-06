<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/2/17
 * Time: 14:51
 */
include('/usr/local/huanpeng/include/init.php');



function createSecurityChain2($filename){
    //$ip = fetch_real_ip($port);
    //$ip = '11.22.33.44';
    $filename = '8780.mp4';
    $now = time();
    //$eTime = dechex($now+WS_EXPIRED);
    $eTime = dechex($now);
    $cTime = dechex($now) ;
    $wsSecret = md5(WS_SECURITY_CHAIN.'/'.$filename.$cTime);
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query($data);
}
var_dump(createSecurityChain2('8780.mp4'));exit;


$url = 'rtmp://dev-drtmp.huanpeng.com/liverecord/Y-19930-9842758';
//$filename = basename($url);
$filename = 'liverecord/Y-19930-9842758';
$param = createHlsSecret($filename);
$url .= '?'.$param;
echo $url,"\n";
echo $filename;
//echo fetch_real_ip(),"\n";
//echo $_SERVER['REMOTE_ADDR'];

//创建网宿hls防盗链

/*function createHlsSecret($filename){
    if( !is_string( $filename ) )
    {
        return '';
    }
    $filename .= '/playlist.m3u8';
    $now = time();
    $eTime = dechex( $now );
    $cTime = dechex( $now ) ;
    $wsSecret = md5( WS_SECURITY_CHAIN . '/' . $filename . $cTime );
    $data = array(
        'wsSecret' => $wsSecret,
        'eTime' => $eTime
    );
    return http_build_query( $data );
}*/
