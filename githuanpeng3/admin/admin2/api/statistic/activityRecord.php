<?php
/**
 * 活动&内部发放记录
 * Created by PhpStorm.
 * User: dong
 * Date: 17/4/21
 * Time: 上午9:58
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/publicRequist.class.php';
$db = new DBHelperi_admin();




$adminid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : 0;//管理员id
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$type = isset( $_POST['type'] ) ? (int)$_POST['type'] : 1;
$month = isset( $_POST['month'] ) ? trim($_POST['month']) : 0;//月份

if( empty( $adminid ) || empty( $encpass ) || empty( $type ) )
{
	error( -1007 );
}
$adminHelp = new AdminHelp( $adminid, $type );
$err = $adminHelp->loginError( $encpass );
if( $err )
{
	error( $err );
}
succ(array(array('date'=>'2017-05','hpcoin'=>20000,'hpbean'=>5555555)));