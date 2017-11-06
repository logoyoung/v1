<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/publicRequist.class.php';
$db = new DBHelperi_admin();


function anchorIsExists($uid, $db)
{
    $res = $db->field('uid, cid')->where("uid=$uid")->select('anchor');
    if(!$res) {
        return 1;
    } elseif ($res[0]['cid'] != 0) {
        return 2;
    }
    
    return 200;
}

function singAnchor($uid, $cid, $companyRate,$db)
{
    $res = $db->where("uid=$uid")->update('anchor', array('cid'=>$cid,'rate'=>$companyRate));
   if(false !== $res){
       addToCompanyAnchor($uid, $cid, $db);
       return true;
   }else{
       return false;
   }
}

function addToCompanyAnchor($uid,$cid,$db){
	$status=1;
    $sql = "INSERT INTO `company_anchor` (`cid`,`uid`,`status`) VALUES ($cid,$uid,$status) on duplicate key update uid = $uid, cid = $cid,status=$status";
    $res = $db->doSql($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }

}

function checkCompanyIsExist($cid, $db)
{
    if (empty($cid)) {
        return false;
    }
    $res = $db->where("id=$cid and status=0")->limit(1)->select('company');
    if (false !== $res && !empty($res)) {
        return true;
    } else {
        return false;
    }

}

/**添加修改比率记录
 *
 * @param int $adminid 管理员id
 * @param int $cid     经纪公司id
 * @param int $rate    比率
 * @param     $db
 *
 * @return bool
 */
function addRateRecord( $adminid, $uid, $role_change_id, $beforeInfo,$afterRate, $companytype,$db )
{
	$data = array(
		'uid' => $uid,
		'before_rate' => (int)$beforeInfo['rate'],
		'after_rate' => $afterRate,
		'adminid' => $adminid,
		'type' => 1,
		'role_change_id' => (int)$role_change_id
	);
	if($companytype){
		$data['desc']='和官方签约引起的比率变化';
	}else{
		$data['desc']='和经纪公司签约引起的比率变化';
	}
	$res = addRateChangeRecord( $data, $db );
	if( $res )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function  rolechangeRecord($adminid,$uid,$cid,$addtype,$beforeInfo,$db){
	$data=array(
		'uid'=>$uid,
		'before_cid'=>$beforeInfo['cid'],
		'after_cid'=>$cid,
		'adminid'=>$adminid
	);
	if($addtype){
		$data['desc'] = '完成和官方签约';
	}else{
		$data['desc'] = '完成和经纪签约';
	}

	$res=addRoleChangeRecord( $data, $db );
	if(false !==$res){
		return $res;
	}else{
		return false;
	}
}

/**
 * 获取经纪公司当前的比率值
 * @param int $cid  公司id
 * @param $db
 */
function  getCompanyNowRate($cid,$db){
	if(empty($cid)){
		return false;
	}
	$res=$db->field('rate')->where("id=$cid")->limit(1)->select('company');
	if($res){
		return $res[0]['rate'];
	}else{
		return false;
	}
}

function  checkIsAlreadyCompanyAnchorByuid($uid,$db){
	if(empty($uid)){
		return false;
	}
	$res=$db->where("uid=$uid")->select('company_anchor');
	if($res){
		return true;
	}else{
		return false;
	}
}


/**
 * start
 */
$adminid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$addtype = isset($_POST['addtype']) ? (int)$_POST['addtype'] : 0;
$uid = isset($_POST['uuid']) ? (int)$_POST['uuid'] : 0;
$cid = isset($_POST['cid']) ? (int)$_POST['cid'] : 0;

if (empty($adminid) || empty($encpass) || empty($type)) {
    error(-1007);
}

$adminHelp = new AdminHelp($adminid , $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
if(empty($uid )){
    error(-1007);
}
if(empty($addtype) && empty($cid)){
    error(-1007);
}
if(!in_array($addtype, array(0,1))){
    error(-1007);
}
if($addtype){
    $cid=15;
}else{
    $res = checkCompanyIsExist($cid, $db);
    if(false === $res){
        error(-1039);
    }
}
$checkRes = anchorIsExists($uid, $db);
if($checkRes == 1){
	error(-1001);
} elseif ($checkRes == 2){
	error(-1040);
}
$beforeInfo = getBeforeRateByUid($uid, $db );
$companyRate = getCompanyNowRate($cid, $db);
if(!$companyRate){
	$companyRate=OTHER_RATE;
}
$result = singAnchor($uid, $cid, $companyRate, $db);
if($result){
	$role_change_id = rolechangeRecord($adminid,$uid,$cid,$addtype,$beforeInfo,$db);//添加角色变更纪录
	$list[$uid] = addRateRecord($adminid, $uid, $role_change_id, $beforeInfo,$companyRate, $addtype,$db);//添加比率兑换纪录
	$res = publicRequist::outside_setRate($list,$companyRate,'完成签约');//通知财务系统
	if($res){
		updateNoticStatus( $list[$uid], $db );//是否通知到财务系统
	}else{
		unsuccessLogForFinanceBack('完成签约比率改变 财务系统返回失败',array('financeBack'=>$res,'adminid'=>$adminId,'roleChangeid'=>$role_change_id,'rate'=>$companyRate,'before'=>$beforeInfo,'list'=>$list),$db);
	}
	succ($result);
}else{
   error();
}

