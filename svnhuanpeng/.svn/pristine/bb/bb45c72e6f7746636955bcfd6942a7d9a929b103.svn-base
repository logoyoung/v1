<?php
namespace Cli\Controller;
use Org\Util\Date;
class repairController extends \Think\Controller
{
	/**
	 * 同步anchor 表里的cert_status字段
	 */
	public function run()
	{
		$Adao = D( 'anchor' );
		$Rdao = D( 'anchor' );
		$list=$Rdao->where("status=101")->select();
		if($list){
			foreach ($list as $v){
				$Adao->where("uid=".$v['uid'])->save(array('cert_status'=>1));
			}
		}
	}
	
	public function userchannel(){
	    $stime = $_GET['stime']?$_GET['stime']:date("Y-m-d",strtotime(date("Y-m-d"))-86400);
	    $etime = $_GET['etime']?$_GET['etime']:date("Y-m-d",strtotime(date("Y-m-d"))-86400);
	    $where['ctime'] = [['egt',$stime.'00:00:00'],['elt',$etime.'23:59:59']];
	    $channel_users = M('channel_user')->where($where)->getField("uid,channel");
	    $channel_viewrecords = M('admin_userviewrecord');
	    
	}
}