<?php

namespace Admin\Controller;

use HP\Op\Anchor;
use HP\Op\Company;
use HP\Op\Live;
use HP\Op\Statis;
use Org\Util\Date;
use HP\Op\Wages;
use HP\Service\OpMail;
use HP\Op\publicRequist;

ini_set( 'memory_limit', '512M' );
set_time_limit( 300 );

class TestController extends BaseController
{

	protected function _access()
	{
		return self::ACCESS_NOLOGIN;
	}
	
	public function repairwagesblack(){
	    //黑名单
	    $anchorBlackDao = D("AnchorBlacklist");
	    $where['ctime'] = ['lt','2017-09-01 00:00:00'];
	    $anchorBlackinfos = $anchorBlackDao->where($where)->getField("luid,ctime");
	    unset($where);
	    $dao = M("admin_wages");
	    $where['month'] = '2017-08';
	    $datas = $dao->where($where)->select();
	    foreach ($datas as $data){
	        $uid = $data['uid'];
	        $black = $data['is_black'];
	        $id = $data['id'];
	        $wagesbase = $data['wages_base'];
	        if($anchorBlackinfos[$uid]){
	            $anchorBlackinfo = 1;
	        }else{
	            $anchorBlackinfo = 0;
	        }
	        if($black != $anchorBlackinfo){
	            echo "$uid,$anchorBlackinfo,$id,$wagesbase,$anchorBlackinfo";echo "<br>";
	            //echo $dao->where(['id'=>$id])->save(['is_black'=>1]);echo "<br>";
	        }
	    }
	}
	
	public function repaircompanylog(){
	    $dao = M('company_anchor');
	    $log = D('anchor_change_record');
	    //$where['ctime'] = ['lt','2017-05-22 23:59:59'];
	    $where['cid'] = ['gt',0];
	    $where['uid'] = ['gt',0];
	    if($_GET['uid']) $where['uid'] = $_GET['uid'];
	    $res = $dao->where( $where )->group('uid')->getField( 'uid,min(ctime) as ctime ,cid' );
	    unset($where);
	    $where['before_cid'] = 0;
	    $where['after_cid'] = ['gt',0];
	    if($_GET['uid']) $where['uid'] = $_GET['uid'];
	    $logres = $log->where($where)->group('uid')->getField( 'uid,min(ctime) as ctime ,after_cid as cid' );
	    
	    
	    foreach ($res as $data){
	        $uid = $data['uid'];
	        $logctime = $logres[$uid]['ctime']?date('Y-m-d',strtotime($logres[$uid]['ctime'])):0;
	        $companyctime = $data['ctime']?date('Y-m-d',strtotime($data['ctime'])):0;
	        
	        if(!$logctime){
    	        $insertdata['uid'] = $data['uid'];
    	        $insertdata['before_cid'] = 0;
    	        $insertdata['after_cid'] = $data['cid'];
    	        $insertdata['adminid'] = 1;
    	        $insertdata['desc'] = '2017年9月4日历史数据补充';
    	        $insertdata['ctime'] = $data['ctime'];
    	        if($_GET['do']=='do'){
            	    echo $log->add($insertdata);
    	        }else{
    	           echo $log->fetchSql(true)->add($insertdata);echo"<br>";
    	        }
	        }
	        
	    }
	}
	
	public function userchannel(){
	    $stime = $_GET['stime']?$_GET['stime']:date("Y-m-d",strtotime(date("Y-m-d"))-86400);
	    $etime = $_GET['etime']?$_GET['etime']:date("Y-m-d",strtotime(date("Y-m-d"))-86400);
	    $_GET['uid']?$where['uid'] = $_GET['uid']:'';
	    $where['ctime'] = [['egt',$stime.' 00:00:00'],['elt',$etime.' 23:59:59']];
	    $channel_users = M('channel_user')->where($where)->getField("uid,channel");
	    $where['channel'] = ['gt',0];
	    $channel_viewrecords = M('admin_userviewrecord')->where($where)->order('ctime desc')->getField("uid,channel");
	    
	    foreach ($channel_users as $uid=>$channel_user){
	        $channel_viewrecord = $channel_viewrecords[$uid];
	        if($channel_user+0 != $channel_viewrecord+0){
	            $diff[$uid]['channl_user'] = $channel_user;
	            $diff[$uid]['channl_viewrecord'] = $channel_viewrecord;
	        }
	    }
	    
	    if($diff){
	        $str .= 'uid,user_channel,admin_userviewrecord';$str .= "<br>";
	    }
	    foreach ($diff as $uid=>$diff){
	        $channel_user = $diff['channl_user'];
	        $channel_viewrecord = $diff['channl_viewrecord'];
	        $str .= "$uid,$channel_user,$channel_viewrecord";$str .= "<br>";
	    }
	    
	    echo $str;
	    if($str) self::sendmail("channel检查-".$stime,$str);
	    
	}
	
	public  function sendmail($subject,$body){
	    $address=['weiqiang@6.cn'];
	    $rate = OpMail::send($address, $subject, $body,1);
	}


	public function repairrate()
	{
		$rates = D( 'anchor' )->getField( "uid,rate" );
		foreach ( $rates as $uid => $rate )
		{
			$datas[$uid][0]['before'] = $rate;
			$datas[$uid][0]['after'] = $rate;
		}
		$dao = D( 'rate_change_record' );
		//$where['ctime'] = [ [ 'egt', '2017-05-01 00:00:00' ], [ 'elt', '2017-07-31 23:59:59' ] ];
		//$where['type'] = 1;
		$where['before_rate'] = [ 'neq', 'after_rate' ];
		$res = $dao->where( $where )->order( 'ctime' )->select();
		foreach ( $res as $re )
		{
			$t = strtotime( $re['ctime'] );
			$datas[$re['uid']][$t]['before'] = $re['before_rate'];
			$datas[$re['uid']][$t]['after'] = $re['after_rate'];
		}
		return $datas;
	}

	public function testrate()
	{
		$uid = $_GET['uid'];
		$t = strtotime( $_GET['t'] );
		$rates = self::repairrate();
		$rate = $rates[$uid];
		dump( $rate );
		$retrun_rate = $rate[0]['after'];
		foreach ( $rate as $ctime => $r )
		{
			if( $ctime > $t )
			{
    			$retrun_rate = $r['before'];
				break;
			}
			$retrun_rate = $r['after'];
		}
		dump( $retrun_rate );
		return $retrun_rate;
	}

	public function giftrecord()
	{
	    $month = $_GET['month']?$_GET['month']:201707;
		if( $_GET['uid'] )
		{
		    $uid = $_GET['uid'];
			$res = M()->query( "select id,ruid,hb,gb,ctime from hpf_sendGiftRecord_$month  where ruid = $uid " );
		}
		else
		{
			$res = M()->query( "select id,ruid,hb,gb,ctime from hpf_sendGiftRecord_$month   " );
		}

		echo 'id,uid,金额,送礼物rate,正确rate,ctime';
		echo "<br>";
		$rates = self::repairrate();
		foreach ( $res as $re )
		{
			$rate = $rates[$re['ruid']];
			$t = strtotime( $re['ctime'] );
			$re_ctime = $re['ctime'];
			$retrun_rate = $rate[0]['after'];
			foreach ( $rate as $ctime => $r )
			{
				if( $ctime > $t )
				{
				    $retrun_rate = $r['before']?$r['before']:60;
					break;
				}
				$retrun_rate = $r['after'];
			}
			$send_rate = $re['gb'] * 10 / $re['hb'] * -1;
			$retrun_rate = $retrun_rate / 100;
			$uid = $re['ruid'];
			$id = $re['id'];
			$hb = $re['hb'] * -1;
			if( $retrun_rate <> $send_rate )
			{
				echo "$id,$uid,$hb,$send_rate,$retrun_rate,$re_ctime";
				echo '<br>';
				$datas[$uid] += ($send_rate - $retrun_rate)*$hb;
			}
		}
		
		echo "uid,差额";echo '<br>';
		foreach ($datas as $uid=>$b){
		    echo "$uid,$b";echo "<br>";
		}

	}


	public function repairhpfrate()
	{
	    $datas = \HP\Util\Repair::getRate();
	    if($datas['diff1']){
	        $str = "uid,anchor_rate,hpf_rate";$str .= "<br>";
	        $senmail = 1;
    	    foreach ($datas['diff1'] as $data){
    	        $uid = $data['uid'];
    	        $anchor_rate = $data['anchor_rate'];
    	        $hpf_rate = $data['hpf_rate'];
    	        $str .="$uid,$anchor_rate,$hpf_rate";$str .= "<br>";
    	    }
	    }
	    if($datas['diff2']){
	        $str .= "===============";$str .= "<br>";
	        $senmail = 1;
    	    foreach ($datas['diff2'] as $data){
    	        $uid = $data['uid'];
    	        $anchor_rate = $data['anchor_rate'];
    	        $hpf_rate = $data['hpf_rate'];
    	        $str .="$uid,$anchor_rate,$hpf_rate";$str .= "<br>";
    	    }
	    }
	    if($str){
	        echo $str;
	    }else{
	        echo 'ok';
	    }
	    if($senmail) self::sendmail("收益比率检查",$str);
	}

	//2017年7月27日 刷新银行卡信息
	//     0 => string '﻿姓名' (length=9)
	//     1 => string '证照号码' (length=12)
	//     2 => string 'UID' (length=3)
	//     3 => string '银行开户账号' (length=18)
	//     4 => string ' 开户银行（明确到支行） ' (length=35)
	//     5 => string '开户银行所在地（省）' (length=30)
	//     6 => string '开户银行所在地（市）' (length=30)
	public function repairbankcard()
	{
		if( $_FILES )
		{
			$do = $_GET['do'];
			$banks = D( 'bank' )->getField( "id,name" );
			$dao = D( "bank_card" );
			$file = fopen( $_FILES["file"]["tmp_name"], "r" );
			while ( $csvdata = fgetcsv( $file ) )
			{ //每次读取CSV里面的一行内容
				$infos[] = $csvdata;
			}
			fclose( $file );
			foreach ( $infos as $info )
			{
				if( $info[2] == 'UID' )
				{
					continue;
				}
				$bankuser = $dao->fetchSql( false )->where( [ 'uid' => $info[2] ] )->find();
				if( $bankuser )
				{
					if( $do == 'do' )
					{
						echo $dao->fetchSql( false )->data( [ 'accountbank' => $info[4] ] )->where( [ 'uid' => $info[2] ] )->save();
						echo "<br>";
					}
					else
					{
						echo $dao->fetchSql( true )->data( [ 'accountbank' => $info[4] ] )->where( [ 'uid' => $info[2] ] )->save();
						echo "<br>";
					}
				}
				else
				{
					$data['bankid'] = $this->getbankid( $banks, $info[4] );
					if( !$data['bankid'] )
					{
						echo $info[2] . "," . $info[3] . "," . $info[4] . "," . $info[5] . "," . $info[6];
						echo "<br>";
						continue;
					}
					$data['uid'] = $info[2];
					$data['name'] = $info[0];
					$data['cardid'] = $info[3];
					$data['accountbank'] = $info[4];
					$data['address'] = $info[5] . ' ' . $info[6];
					$data['ctime'] = get_date();
					if( $do == 'do' )
					{
						echo $dao->fetchSql( false )->data( $data )->add();
						echo "<br>";
					}
					else
					{
						echo $dao->fetchSql( true )->data( $data )->add();
						echo "<br>";
					}
				}
			}
		}
		$this->display( 'test' );
	}

	public function getbankid( $banks, $accoutbank )
	{
		foreach ( $banks as $id => $name )
		{
			if( strstr( $accoutbank, $name ) )
			{
				return $id;
			}
		}
	}

	//db strict 严格检查 测试
	public function teststrict()
	{
		$dao = D( 'company' );
		$dao->strict( true );
		$data['name'] = '测试测试3';
		$data['id'] = '232';
		$dao->data( $data )->add();

	}

	public function testweek()
	{
		$date = $_GET['date'];
		$weekday = $this->getWeekDate( $date );
		dump( $weekday );
	}

	function getWeekDate( $str )
	{
		list( $year, $weeknum ) = str_split( $str, 4 );
		$firstdayofyear = mktime( 0, 0, 0, 1, 1, $year );
		$firstweekday = date( 'N', $firstdayofyear );
		$firstweenum = date( 'W', $firstdayofyear );
		if( $firstweenum == 1 )
		{
			$day = ( 1 - ( $firstweekday - 1 ) ) + 7 * ( $weeknum - 1 );//-1
			$startdate = date( 'Ymd', mktime( 0, 0, 0, 1, $day, $year ) );
			$enddate = date( 'Ymd', mktime( 0, 0, 0, 1, $day + 6, $year ) );
		}
		else
		{
			$day = ( 9 - $firstweekday ) + 7 * ( $weeknum - 1 );//-1
			$startdate = date( 'Ymd', mktime( 0, 0, 0, 1, $day, $year ) );
			$enddate = date( 'Ymd', mktime( 0, 0, 0, 1, $day + 6, $year ) );
		}
		return array( $startdate, $enddate );
	}

	public function statement()
	{
		$dao = D( 'hpf_statement_201707' );
		$field = "uid,hb,gb,hd,gd";
		$statements = $dao->field( $field )->order( "id desc" )->select();
		foreach ( $statements as $statement )
		{
			if( $users[$statement['uid']] )
			{
				continue;
			}
			$users[$statement['uid']] = $statement;
		}

		$dao = D( 'hpf_statement_201706' );
		$field = "uid,hb,gb,hd,gd";
		$statements = $dao->field( $field )->order( "id desc" )->select();
		foreach ( $statements as $statement )
		{
			if( $users[$statement['uid']] )
			{
				continue;
			}
			$users[$statement['uid']] = $statement;
		}

		$dao = D( 'hpf_statement_201705' );
		$field = "uid,hb,gb,hd,gd";
		$statements = $dao->field( $field )->order( "id desc" )->select();
		foreach ( $statements as $statement )
		{
			if( $users[$statement['uid']] )
			{
				continue;
			}
			$users[$statement['uid']] = $statement;
		}
		$dao = D( 'hpf_statement_201704' );
		$field = "uid,hb,gb,hd,gd";
		$statements = $dao->field( $field )->order( "id desc" )->select();
		foreach ( $statements as $statement )
		{
			if( $users[$statement['uid']] )
			{
				continue;
			}
			$users[$statement['uid']] = $statement;
		}
		$dao = D( 'hpf_statement_201703' );
		$field = "uid,hb,gb,hd,gd";
		$statements = $dao->field( $field )->order( "id desc" )->select();
		foreach ( $statements as $statement )
		{
			if( $users[$statement['uid']] )
			{
				continue;
			}
			$users[$statement['uid']] = $statement;
		}

		foreach ( $users as $user )
		{
			$datas['hb'] += $user['hb'];
			$datas['gb'] += $user['gb'];
			$datas['hd'] += $user['hd'];
			$datas['gd'] += $user['gd'];
		}
		echo "hb,gb,hd,gd";
		echo "<br>";
		echo "欢朋币：" . ( $datas['hb'] / 1000 ) . " 金币" . ( $datas['gb'] / 1000 ) . " 欢朋豆" . ( $datas['hd'] / 1000 ) . " 金豆" . ( $datas['gd'] / 1000 );
		echo "<br>";

		dump( count( $users ) );
	}

	public function withdrawHandle()
	{
		$tid = $_GET['tid'];
		$otid = $_GET['otid'];
		$type = $_GET['type'];
		$res = publicRequist::withdrawHandle( $tid, $type );//事件推送
		dump( $res );
	}


	public function withdrawHandelsuccess()
	{
		$dao = D( 'admin_wages' );
		$where['month'] = $_GET['month'];
		$where['status_finance'] = $_GET['status_finance'];
		$where['wages_gift_edit'] = [ 'gt', 0 ];
		$do = $_GET['do'];
		$datas = $dao->where( $where )->select();
		foreach ( $datas as $data )
		{
			$tid = $data['tid'];
			echo $tid;
			echo "<br>";
			if( $do == 'do' )
			{
				$res = publicRequist::withdrawHandle( $tid, 1, '汇款成功' );//汇款成功
				echo $res;
				echo "<br>";
			}
		}
	}

	function livelength()
	{//检查直播时长
		$date = $_GET['date'] ? $_GET['date'] : date( "Y-m-d", strtotime( date( "Y-m-d" ) ) - 86400 );
		$str =  "====================== date:  " . $date . " ==================<br> ";
		$length1 = Statis::getAnchorLength( $date, $date );
		$where['date'] = $date;
		$length2 = D( "liveLength" )->where( $where )->getField( "uid,length" );
		$str .= "date,uid,length1,length2,diff <br>";
		foreach ( $length1['lengths'] as $uid => $length )
		{
			$diff = $length - $length2[$uid];
			$countdiff += $diff;
			if( $diff != 0 )
			{
			    $senmail = 1;
				$str .= "$date,$uid,$length,$length2[$uid],$diff";
				$str .= "<br>";
			}
		}

		$str .= "============================= $date : $countdiff ==== <br>";

		foreach ( $length2 as $uid => $length )
		{
			$diff = $length - $length1['lengths'][$uid];
			if( $diff != 0 )
			{
			    $senmail = 1;
				$str .= "$date,$uid,$length,$length1[$uid],$diff";
				$str .= "<br>";
			}
		}
		echo $str;
		if($senmail) self::sendmail("直播时长检查-$date",$str);
	}

	function wages()
	{
		$dao = D( "admin_wages" );
		$stime = $_GET['stime'];
		$stime ? $stime = $stime : $stime = date( "Y-m-01", strtotime( date( "Y-m-01" ) ) - 86400 );
		$etime ? $etime = $etime : $etime = date( "Y-m-t", strtotime( $stime ) );
		$month = date( 'Y-m', strtotime( $stime ) );
		if( $_GET['reset'] == 1 )
		{
			$dao->where( [ 'month' => $month ] )->delete();
		}
		$userinfos = Wages::getWages( $stime );
		foreach ( $userinfos as $userinfo )
		{
			$dao->data( $userinfo )->add();
		}
	}

	function testupload()
	{
		$type = \HP\File\File::PIC_OTHER;
		$obj = \HP\File\File::UploadPic( [ 'typeDir' => C( 'GAME_I_DIR' ), 'type' => $type, 'uuid' => \HP\Op\Admin::getUid() ] );
		dump( $obj );
	}

	function retained()
	{
		$days = [ 0 => 't_0', 1 => 't_1', 3 => 't_3', 7 => 't_7', 15 => 't_15', 30 => 't_30' ];
		$dao = D( "userviewrecord" );//访问记录表
		$userdevicedao = D( "userdevice" );//新增记录表
		$tdate = $_GET['time'] ? $_GET['time'] : date( "Y-m-d", strtotime( date( "Y-m-d" ) ) - 86400 );
		$stime = $tdate . " 00:00:00";
		$etime = $tdate . " 23:59:59";
		$where["action"] = 1;//打开app
		$where["ctime"] = array( array( 'egt', $stime ), array( 'elt', $etime ) );
		$res = $dao->where( $where )->distinct( true )->getField( "udid", true );


		foreach ( $days as $day => $col )
		{
			//不同时间新增的设备udid
			$date = date( "Ymd", strtotime( $tdate ) - 86400 * $day );
			$devicedatas = $userdevicedao->where( [ "cdate" => [ "eq", $date ] ] )->getField( "udid,channel" );
			foreach ( $res as $udid )
			{
				$channel = $devicedatas[$udid];
				if( $channel !== null )
				{//留存了
					$data[$date][$channel][$col]++;
				}

			}
		}
		dump( $data );
		$dao = D( 'statisretained' );
		foreach ( $data as $date => $channels )
		{
			foreach ( $channels as $channel => $col )
			{
				$rdata = $col;
				$rdata['date'] = $date;
				$rdata['channel'] = $channel;
				$dao->add( $rdata, [], true );
			}
		}
	}

	public function index()
	{
		$path = "/data/logs/huanpeng.access.log";
		$num = $_GET['num'] ? $_GET['num'] : 10;
		$log = self::FileLastLines( $path, $num );
		echo( $log );
	}


	/**
	 * 取文件最后$n行
	 *
	 * @param string $filename 文件路径
	 * @param int    $n        最后几行
	 *
	 * @return mixed false表示有错误，成功则返回字符串
	 */
	function FileLastLines( $filename, $n )
	{
		if( !$fp = fopen( $filename, 'r' ) )
		{
			echo "打开文件失败，请检查文件路径是否正确，路径和文件名不要包含中文";
			return false;
		}
		$pos = -2;
		$eof = "";
		$str = "";
		while ( $n > 0 )
		{
			while ( $eof != "\n" )
			{
				if( !fseek( $fp, $pos, SEEK_END ) )
				{
					$eof = fgetc( $fp );
					$pos--;
				}
				else
				{
					break;
				}
			}
			$str .= fgets( $fp ) . "<br>";
			$eof = "";
			$n--;
		}
		return $str;
	}

	function test()
	{
	   $card = '50010419860414241X';
	   $card2 = get_secure_cert($card);
	   dump($card2);
	}

	function getUserStatisByday($stime,$etime){
	    $datas = [];
	    $stime = $stime." 00:00:00";
	    $etime = date("Y-m-d",strtotime($etime)+86400)." 00:00:00";
	    $userChannel = Statis::getUserChannels($stime, $etime);//渠道
	    Statis::getUserByday($stime, $etime,$userChannel,$datas);//注册
	    Statis::getRealUserByday($stime, $etime,$userChannel,$datas);//实名认证
	    Statis::getUserViewByday($stime, $etime,$userChannel,$datas);//设备
	    Statis::getUserDeviceByday($stime, $etime,$userChannel,$datas);//新增设备
	    return $datas;
	}

//2017-08-07 Dylan
	public function getInfoByUids()
	{
		$dao = new \Common\Model\HPFMonthModel( "sendGiftRecord", '2017-07-01' );
		$ruid = $dao->select();
		if( $ruid )
		{
			return $ruid;
		}
		else
		{
			return array();
		}
	}

	public function putcsv()
	{
		$list = $this->getInfoByUids();//获取uid所有记录数
		if( $list )
		{//导出数据
			$excel[] = array( '主播id', '当时计算比率', '实际比率1', '实际比率2', '收礼时间' );
			foreach ( $list as $v )
			{
				$rate = $this->grate( $v['ruid'], $v['ctime'] );//获取所得礼物时间的比率
				$nrate = $this->nrate( $v['ruid'], $v['ctime'] );//获取所得礼物时间的比率
				$excel[] = array( "\t" . $v['ruid'], ( $v['gb'] * 10 ) / abs( $v['hb'] ), $rate / 100, $nrate / 100, $v['ctime'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '主播收礼比率' );
		}
	}

	public function grate( $uid, $t )
	{
		$rates = $this->repairrate();
		$rate = $rates[$uid];
		$retrun_rate = $rate[0];
		foreach ( $rate as $ctime => $r )
		{
			if( $ctime > $t )
			{
				break;
			}
			$retrun_rate = $r;
		}
		$rate=$retrun_rate ? $retrun_rate : 60;
		if($rate<60){
			$rate=60;
		}
		return $rate;
	}



	public function nrate( $uid, $time )
	{
		$dao = D( 'rate_change_record' );
		$rate=0;
		$sql = "select  *  from  rate_change_record  where uid=$uid  and ctime <='$time' order  by ctime desc limit 1;";
		$res = $dao->query( $sql );
		if( $res )
		{
			$rate=$res[0]['after_rate'];
		}
		if($rate<60){
			$rate=60;
		}
		return $rate;
	}
}
