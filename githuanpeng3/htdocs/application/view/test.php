<?php

$result = curl_post($_POST,'http://dev.huanpeng.com/main/api/wxpay/unifiedorder.php');
$result = json_decode($result, true);
if($result['code'] == 0){
	exit (json_encode($result));
}
$result = $result['content'];

$data = $result['data'];
$data2 = array(
	'appId'=>$data['appid'],
	'timeStamp' => $data['timestamp'],
	'nonceStr'=>$data['noncestr'],
	'package' => $data['package'],
	'partnerId'=>$data['partnerid'],
	'prepayId'=>$data['prepayid']
);

ksort($data2);
$str = '';
foreach ($data as $k => $v){
	if($k != 'sign' && $v != "" && !is_array($v)){
		$str .= $k . "=" . $v . '&';
	}
}
$str = trim($str,'&');
$str = $str."&key=".'f6db803a518caf532bfe4bfde534bd5a';
$sign = strtoupper(md5($str));
$resultParam['nonce_str'] = $nonce_str;
$resultParam['sign'] = $sign;
$resultParam['data'] = $data2;
$resultParam['version'] = 'test10';

succ($result,$resultParam);

function succ($content=array()){
	if (empty($content)) {
		$succ = array(
			'status' => "1",
			'content' => (object) $content
		);
	} else {
		$succ = toString(array(
			'status' => "1",
			'content' => $content
		));
	}
	exit(json_encode($succ));
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

function curl_post($data, $url, $method = 'POST')
{
	$ch = curl_init();
	if (substr($url, 0, 5) == 'https') {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	}
	if($method == 'GET'){
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$params = http_build_query($data);
		$url = $url.'?'.$params;
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($method == 'POST') {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
?>


<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/17
 * Time: 下午1:51
 */



class testClass
{
	static $name='';
	static $age='';

	public static function getMyProperty(){
		$other = 'name';
		static::$$other = 1;
		print_r(get_class_vars('testClass'));
	}
}
testClass::getMyProperty();
exit;


function checkParam($condition, &$data, &$params){
	$temp = array();

	$default = [
		'type'=>'string',
		'must'=>false,
		'default'=>null,
		'values'=>null
	];

	foreach($condition as $param => $constraint){
		$currConf = $default;
		if(is_string($constraint)){
			if($constraint == 'int'){
				$currConf['type'] = 'int';
			}else{
				$currConf['type'] = 'string';
			}
		}elseif(is_array($constraint)){
			$currConf = array_merge($currConf,$constraint);
		}

		if($currConf['type'] == 'int'){
			$checkParamOne = (int)$data[$param] ? (int)$data[$param] : 0;
//			$temp[$param] = (int)$data[$param] ? (int)$data[$param] : 0;//(int)urldecode($temp[$param]);
		}else{
			$checkParamOne = trim(urldecode($data[$param])) ? trim(urldecode($data[$param])) : '';
//			$temp[$param] = trim(urldecode($data[$param])) ? trim(urldecode($data[$param])) : '';//trim(urldecode($currConf['default']));
		}


		if(!$checkParamOne){
			if(is_null($currConf['default'])){
				return false;
			}else{
				$checkParamOne = $currConf['default'];
			}
		}

		if(is_array($currConf['values'])){
			if(!in_array($checkParamOne, $currConf['values']))
				return false;
		}
		$temp[$param] = $checkParamOne;
	}

	var_dump($params);

	if(is_array($params)){
		$params = $temp;
	}else{
		$data = $temp;
	}
	return true;
}






$conf = array(
	'uid'=>array(
		'type'=>'int',
		'must'=>true,
	),
	'encpass'=>array(
		'type'=>'string',
		'must'=>true
	),

);

$data = array(
	'uid'=>'11111',
	'encpass' => 'xxxxxxxxxx',
	'type' => '1'
);

$res = array();

var_dump($ret = checkParam($conf,$data,$res=array()));
if($ret)
	print_r($data);


print_r($res);
exit;






$a = 135;

$b = 'a';

var_dump($$b);

var_dump(!$$b);

$b = [];

if(!$b){
	var_dump($b);
}


$a='1';
$b=2;
$c=135;

$post = ['a'=>1,'b'=>2,'c'=>3];

$params = ['a'=>'int','b'=>'string','c'=>'string'];
$check = ['a','b','c'];
foreach ($params as $key => $val){
	$$key = isset($post[$key]) ? $post[$key] : '';
	$$key = $val == 'int' ? (int) $$key : trim($$key);

	if(in_array($key, $check) && !$$key)
		echo 'this is not current';
}

var_dump(is_object(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5]));
var_dump((object)['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5]);

var_dump(88888888888-87777777777);

var_dump(md5('client_set_version_key@KSZ14%003'));

var_dump(md5(sha1('{"tm":"510576","versions":"20161121110946"}client_set_version_key@KSZ14%003')));
var_dump(time());

var_dump(http_build_query(['url'=>'http://dev.huanpeng.com/main/a.php']));

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../stylesheets/report.css">
	<script type="text/javascript" src="http://dev.huanpeng.com/main/static/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="http://dev.huanpeng.com/main/static/js/jquery.form.js"></script>
</head>
<body>
<div id="report-modal">
	<div class="report-mask"></div>
	<div class="report-screen">
		<div class="report-contain">
			<form id="report-form" enctype="multipart/form-data">
				<div class="report-head"><div class="head-title">举报</div></div>
				<div class="report-body">
					<div class="report-body-left">
						<div class="report-title-list">
							<div class="report-title-one"><p>主播昵称</p></div>
							<div class="report-title-one"><p>房间号</p></div>
							<div class="report-title-one"><p>举报原因</p></div>
							<div class="report-title-one"><p>详细说明</p></div>
							<div class="report-title-one report-pic"><p>上传截图</p></div>
						</div>
					</div>
					<div class="report-body-right">
						<div class="report-content-list">
							<div class="report-content-one">
								<p>诺言</p>
							</div>
							<div class="report-content-one">
								<p>12345</p>
							</div>
							<div class="report-content-one">
								<select id="report-select">
									<option>请选择一个理由</option>
									<option>传播色情</option>
									<option>盗版视频</option>
									<option>垃圾广告</option>
									<option>其它</option>
								</select>
							</div>
							<div class="report-content-one ">
								<textarea class="report-text" style="color: #999;" default="请输入问题描述" name="text" onfocus="if(this.value.trim()==this.getAttribute('default')){this.value='';this.style.color='#444'}" onblur="if(this.value=='') {this.value=this.getAttribute('default'); this.style.color='#999'}">请输入问题描述</textarea>
							</div>
							<div class="report-content-one">
								<input id="report-pic" type="file" name="file" accept="image/jpeg, image/jpg, image/png, image/gif">
							</div>
							<div class="report-content-one">
								<div class="btn-report">确认</div>
							</div>
						</div>
					</div>
				</div>
		</div>
		</form>
	</div>
</div>
<script>
	var reportRoom = function(zdata){
		var ajaxData = {
			id:'#report-form',
			url:'http://dev.huanpeng.com/main/api/other/report.php',
			type:'post',
			dataType:'json',
			textLimit:100
		};
		//使用模版数据 也可以不用，直接在外面传入模版数据
		var tplData = {
			title:['主播昵称','房间号','举报原因','详细说明','上传截图'],
			reason:['请选择一个理由','传播色情','盗版视频','垃圾广告','其它'],
			zdata:zdata
		};
		var msg = {
			filedErr:'请完整填写信息',
			textErr:'描述字数超过限制',
			success:'成功'
		};
		//提交表单
		var ajaxform = function(callBack,extraData){
			$(ajaxData.id).ajaxSubmit({url:ajaxData.url,data:extraData,type:ajaxData.type,dataType:ajaxData.dataType,success:callBack});
		};
		//检查用户填写合法性
		var checkFiled = function(){
			var reason = document.getElementById('report-select').value;
			if(tplData.indexOf(reason)<1) return msg.filedErr;
			var reportText = document.getElementsByClassName('report-text').item(0).
			if(reportText.value == reportText.getAttribute('default')); return msg.filedErr;
			var reportpic = document.getElementById('report-pic');
			if(!reportpic.value) return msg.filedErr;
			return msg.success;

		};
		//用户提示
		var dialog = function(msg){
			alert(msg);
			//todo
		};
		//获取模版数据
		var getTplData = function(){
			return tplData;
		};
		//外用接口
		return {
			ajaxform:ajaxform,
			checkFiled:checkFiled,
			dialog:dialog,
			getTplData:getTplData
		}

	}
	/*********test**********/
	var report = reportRoom();
	$('.btn-report').bind('click',function(){
		report.ajaxform(report.dialog,{uid:123,encpass:'xxxxxxx'});
	});

</script>
</body>
</html>

