<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/24
 * Time: 上午10:49
 */

?>

<html>
<title>加载中...</title>
<script type="application/javascript" src="../static/js/common.js?v=1.0.4"></script>
<script>
    function isIphoneClient(){
	    var ua = navigator.userAgent.toLowerCase();
	    return /(iphone|ipad|ipod)/.test(ua);
    }
	function getParams(uid,encpass){
		setCookie('_uid',uid);
		setCookie('_enc',encpass);
		location.href = './index.php';
	}
	if(isIphoneClient()){
	    getParams(appLoginUid,appEncpass);
    }
</script>
</html>
