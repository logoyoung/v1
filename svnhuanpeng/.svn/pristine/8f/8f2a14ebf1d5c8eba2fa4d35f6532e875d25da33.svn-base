<?php

namespace Admin\Controller;

use HP\Op\Anchor;
use HP\Op\Company;
use HP\Op\Statis;
use HP\Log\Log;
use HP\File\Dao;
use Think\Model;
use Org\Util\Date;
use Common\Model\HPFMonthModel;


class StatisController extends BaseController
{

	protected $pageSize = 10;

	public function _access()
	{
		return [
			'livedaystatis' => [ 'rechargestatis' ],
			'rechargestatisweek' => [ 'rechargestatis' ],
			'rechargestatismonth' => [ 'rechargestatis' ],
			'rechargestatishours' => [ 'rechargestatis' ],
			'userstatis' => [ 'userstatis' ],
			'channelstatis' => [ 'channelstatis' ],
			'channelstatishours' => [ 'channelstatis' ],
			'rechargedetail' => [ 'rechargedetail' ],
		];
	}

	public function userstatis()
	{
		$dao = D( "statisregister" );
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $_GET["timestart"] = $stime = date( "Y-m-01", strtotime( get_date() ) - 86400 );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $_GET["timeend"] = $etime = date( "Y-m-d", strtotime( get_date() ) - 86400 );
		$stime = date( "Ymd", strtotime( $stime ) );
		$etime = date( "Ymd", strtotime( $etime ) );
		$where['date'] = [ [ 'egt', $stime ], [ 'elt', $etime ] ];
		$counttype = I( "get.count_type" ) ? I( "get.count_type" ) : "day";

		if( $channel = I( 'get.channel' ) )
		{
			$where['channel'] = $channel;
		}

		if( !I( 'get.type' ) )
		{
			$_GET['type'] = 1;
		}
		if( $type = I( 'get.type' ) )
		{
			$where['type'] = $type;
		}
		if( $client = I( 'get.client' ) )
		{
			$where['client'] = [ "like", "%$client%" ];
		}
		if( $payclient = I( 'get.pay_client' ) )
		{
			$where['pay_client'] = [ "like", "%$payclient%" ];
		}

		$groups = [
			'day' => "DATE_FORMAT(date,'%Y%m%d')",
			'week' => "DATE_FORMAT(date,'%Y%u')",
			'month' => "DATE_FORMAT(date,'%Y%m')",
		];
		$titles = [
			'day' => "日",
			'week' => "周",
			'month' => "月",
		];
		$group = $groups[$counttype];
		$field = "max(date) as maxdate,min(date) as mindate ,$group as date,sum(userdevice) as userdevice,sum(userview) as userview,sum(register) as register,sum(phoneuser) as phoneuser,sum(realuser) as realuser";
		$countres = $dao
			->field( $field )
			->where( $where )
			->group( $group )
			->order( 'date' )
			->select();
		if( $export = I( 'get.export' ) || $chart = I( 'get.chart' ) )
		{//导出数据
			$results = $countres;
		}
		else
		{
			$count = count( $countres );
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->where( $where )
				->field( $field )
				->group( $group )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'date' )
				->select();
		}

		$rechargedatas = Statis::getRechargeByType( $counttype, $stime, $etime );

		foreach ( $results as $result )
		{
			$rechargedata = $rechargedatas[$result['date']];
			$data = $result;
			$data['recharge'] = $rechargedata['recharge_user'];
			$data['rmb'] = $rechargedata['rmb'] / Statis::MONEY_CODE;
			$data['recharge_new'] = $rechargedata['recharge_new'];
			$data['rmb_new'] = $rechargedata['rmb_new'] / Statis::MONEY_CODE;
			if( $counttype == 'week' )
			{
				$weekday = $this->getWeekDate( $data['date'] );
				$startday = ( strtotime( $stime ) > strtotime( $weekday[0] ) ) ? $stime : $weekday[0];
				$endday = ( strtotime( $etime ) < strtotime( $weekday[1] ) ) ? $etime : $weekday[1];
				$data['date'] = $data['date'] . "周(" . $startday . "-" . $endday . ")";
			}
			//$counttype == 'week' && $data['date'] = $data['date']."周(".$result['mindate']."-".$result['maxdate'].")";
			$datas[] = $data;
		}
		foreach ( $countres as $result )
		{
			$rechargedata = $rechargedatas[$result['date']];
			$sum['userdevice'] += $result['userdevice'];
			$sum['userview'] += $result['userview'];
			$sum['register'] += $result['register'];
			$sum['phoneuser'] += $result['phoneuser'];
			$sum['realuser'] += $result['realuser'];
			$sum['recharge'] += $rechargedata['recharge_user'];
			$sum['rmb'] += $rechargedata['rmb'] / Statis::MONEY_CODE;
			$sum['recharge_new'] += $rechargedata['recharge_new'];
			$sum['rmb_new'] += $rechargedata['rmb_new'] / Statis::MONEY_CODE;
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '日期', '新增设备', '活跃设备', '注册', '手机认证', '实名认证', '充值人数', '充值金额', '新增充值人数', '新增充值金额' );
			foreach ( $datas as $data )
			{
				$excel[] = array( $data['date'], $data['userdevice'], $data['userview'], $data['register'], $data['phoneuser'], $data['realuser'], $data['recharge'], $data['rmb'], $data['recharge_new'], $data['rmb_new'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '用户统计-' . $titles[$counttype] . '报' );
		}
		if( $chart = I( 'get.chart' ) )
		{
			$label = $this->gettimelabel( $stime, $etime, $counttype );
			foreach ( $datas as $data )
			{
				$tmp['userdevice'][$data['date']] = $data['userdevice'];
				$tmp['userview'][$data['date']] = $data['userview'];
				$tmp['register'][$data['date']] = $data['register'];
				$tmp['phoneuser'][$data['date']] = $data['phoneuser'];
				$tmp['realuser'][$data['date']] = $data['realuser'];
				$tmp['recharge'][$data['date']] = $data['recharge'];
				$tmp['rmb'][$data['date']] = $data['rmb'];
				$tmp['recharge_new'][$data['date']] = $data['recharge_new'];
				$tmp['rmb_new'][$data['date']] = $data['rmb_new'];
			}

			foreach ( $label as $date )
			{
				$jsdata['新增设备'][$date] = isset( $tmp['userdevice'][$date] ) ? $tmp['userdevice'][$date] : '0';
				$jsdata['活跃设备'][$date] = isset( $tmp['userview'][$date] ) ? $tmp['userview'][$date] : '0';
				$jsdata['注册'][$date] = isset( $tmp['register'][$date] ) ? $tmp['register'][$date] : '0';
				$jsdata['手机认证'][$date] = isset( $tmp['phoneuser'][$date] ) ? $tmp['phoneuser'][$date] : '0';
				$jsdata['实名认证'][$date] = isset( $tmp['realuser'][$date] ) ? $tmp['realuser'][$date] : '0';
				$jsdata['充值人数'][$date] = isset( $tmp['recharge'][$date] ) ? $tmp['recharge'][$date] : '0';
				$jsdata['充值金额'][$date] = isset( $tmp['rmb'][$date] ) ? $tmp['rmb'][$date] : '0';
				$jsdata['新增充值人数'][$date] = isset( $tmp['recharge_new'][$date] ) ? $tmp['recharge_new'][$date] : '0';
				$jsdata['新增充值金额'][$date] = isset( $tmp['rmb_new'][$date] ) ? $tmp['rmb_new'][$date] : '0';
			}
			unset( $tmp );
			//dump($jsdata);
			$this->jsdata = json_encode( $jsdata );
			$this->sum = $sum;
			$this->title = $titles[$counttype];
			$this->display( 'userstatis' );

		}
		else
		{
			$this->data = $datas;
			$this->sum = $sum;
			$this->title = $titles[$counttype];
			$this->page = $Page->show();
			$this->display( 'userstatis' );
		}
	}


	public function channelstatis()
	{
		$dao = D( "statisregister" );
		$channelnames = D( "ChannelVersion" )->getField( "channel,channelName" );
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $_GET["timestart"] = $stime = date( "Y-m-01", strtotime( get_date() ) - 86400 );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $_GET["timeend"] = $etime = date( "Y-m-d", strtotime( get_date() ) - 86400 );
		$stime = date( "Ymd", strtotime( $stime ) );
		$etime = date( "Ymd", strtotime( $etime ) );
		$where['date'] = [ [ 'egt', $stime ], [ 'elt', $etime ] ];
		$counttype = I( "get.count_type" ) ? I( "get.count_type" ) : "day";
		if( $channel = I( 'get.channel' ) )
		{
			$where['channel'] = $channel;
		}
		$where["type"] = $_GET['type'] ? $_GET['type'] : 2;
		if( $client = I( 'get.client' ) )
		{
			$where['client'] = [ "like", "%$client%" ];
		}
		if( $payclient = I( 'get.pay_client' ) )
		{
			$where['pay_client'] = [ "like", "%$payclient%" ];
		}

		$groupdates = [
			'day' => "DATE_FORMAT(date,'%Y%m%d')",
			'week' => "DATE_FORMAT(date,'%Y%u')",
			'month' => "DATE_FORMAT(date,'%Y%m')",
			'hours' => "concat(date,':',hours)",
		];
		$groups = [
			'day' => "DATE_FORMAT(date,'%Y%m%d'),channel",
			'week' => "DATE_FORMAT(date,'%Y%u'),channel",
			'month' => "DATE_FORMAT(date,'%Y%m'),channel",
			'hours' => "date,hours,channel",
		];
		$titles = [
			'day' => "日",
			'week' => "周",
			'month' => "月",
			'hours' => "小时",
		];
		$group = $groups[$counttype];
		$groupdate = $groupdates[$counttype];
		$field = "hours,max(date) as maxdate,min(date) as mindate,channel,$groupdate as date,sum(userdevice) as userdevice,sum(userview) as userview,sum(register) as register,sum(phoneuser) as phoneuser,sum(realuser) as realuser";
		$countres = $dao
			->field( $field )
			->where( $where )
			->group( $group )
			->order( 'date' )
			->select();
		if( $export = I( 'get.export' ) || $chart = I( 'get.chart' ) )
		{//导出数据
			$results = $countres;
		}
		else
		{
			$count = count( $countres );
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->where( $where )
				->field( $field )
				->group( $group )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'date,userview desc' )
				->select();
		}

		$rechargedatas = Statis::getRechargeByChannel( $counttype, $stime, $etime );
		//dump($results);
		$label = $this->gettimelabel( $stime, $etime, 'week' );

		foreach ( $results as $result )
		{
			$data = $result;
			$rechargedata = $rechargedatas[$result['date']][$result['channel']];
			$data['recharge'] = $rechargedata['recharge_user'];
			$data['rmb'] = $rechargedata['rmb'] / Statis::MONEY_CODE;
			$data['recharge_new'] = $rechargedata['recharge_new'];
			$data['rmb_new'] = $rechargedata['rmb_new'] / Statis::MONEY_CODE;
			if( $counttype == 'week' )
			{
				$weekday = $this->getWeekDate( $data['date'] );
				$startday = ( strtotime( $stime ) > strtotime( $weekday[0] ) ) ? $stime : $weekday[0];
				$endday = ( strtotime( $etime ) < strtotime( $weekday[1] ) ) ? $etime : $weekday[1];
				$data['date'] = $data['date'] . "周(" . $startday . "-" . $endday . ")";
			}
			if( $counttype == 'hours' )
			{
				list( $daystr, $hour ) = explode( ':', $data['date'] );
				$data['date'] = $hour < 10 ? "$daystr:0$hour" . "点" : "$daystr:$hour" . "点";
			}
			$datas[] = $data;
		}
		foreach ( $countres as $result )
		{
			$rechargedata = $rechargedatas[$result['date']][$result['channel']];
			$sum['userdevice'] += $result['userdevice'];
			$sum['userview'] += $result['userview'];
			$sum['register'] += $result['register'];
			$sum['phoneuser'] += $result['phoneuser'];
			$sum['realuser'] += $result['realuser'];
			$sum['recharge'] += $rechargedata['recharge_user'];
			$sum['rmb'] += $rechargedata['rmb'] / Statis::MONEY_CODE;
			$sum['recharge_new'] += $rechargedata['recharge_new'];
			$sum['rmb_new'] += $rechargedata['rmb_new'] / Statis::MONEY_CODE;
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '日期', '渠道编号', '渠道名称', '新增设备', '活跃设备', '注册', '手机认证', '实名认证', '充值人数', '充值金额', '新增充值人数', '新增充值金额' );
			foreach ( $datas as $data )
			{
				$excel[] = array( $data['date'], $data['channel'], $channelnames[$data['channel']], $data['userdevice'], $data['userview'], $data['register'], $data['phoneuser'], $data['realuser'], $data['recharge'], $data['rmb'], $data['recharge_new'], $data['rmb_new'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '充值' . $titles[$counttype] . '统计' );
		}
		if( $chart = I( 'get.chart' ) )
		{
			$userdevice = [];
			$userview = [];
			$register = [];
			$phoneuser = [];
			$realuser = [];
			$recharge = [];
			//isset($_GET['count_type'])?$_GET['count_type']
			/*if($counttype=='hours'){
			 $label = $this->gettimelabel($stime,$etime,'hour');
			 }
			 if($counttype=='day'){
			 $label = $this->gettimelabel($stime,$etime,'day');
			 }
			 if($counttype=='week'){
			 $label = $this->gettimelabel($stime,$etime,'week');
			 }
			 if($counttype=='month'){
			 $label = $this->gettimelabel($stime,$etime,'month');
			 }*/
			$label = $this->gettimelabel( $stime, $etime, $counttype );
			foreach ( $datas as $data )
			{
				//$date = $data['date'];
				if( $data['channel'] == '0' )
				{
					$channelnames[$data['channel']] = '其他';
				}
				$userdevice[$channelnames[$data['channel']]][$data['date']] = $data['userdevice'];
				$userview[$channelnames[$data['channel']]][$data['date']] = $data['userview'];
				$register[$channelnames[$data['channel']]][$data['date']] = $data['register'];
				$phoneuser[$channelnames[$data['channel']]][$data['date']] = $data['phoneuser'];
				$realuser[$channelnames[$data['channel']]][$data['date']] = $data['realuser'];
				$recharge[$channelnames[$data['channel']]][$data['date']] = $data['recharge'];
			}
			//dump($label);

			foreach ( $datas as $data )
			{
				foreach ( $label as $date )
				{
					$userdevice[$channelnames[$data['channel']]][$date] = isset( $userdevice[$channelnames[$data['channel']]][$date] ) ? $userdevice[$channelnames[$data['channel']]][$date] : '0';
					$userview[$channelnames[$data['channel']]][$date] = isset( $userview[$channelnames[$data['channel']]][$date] ) ? $userview[$channelnames[$data['channel']]][$date] : '0';
					$register[$channelnames[$data['channel']]][$date] = isset( $register[$channelnames[$data['channel']]][$date] ) ? $register[$channelnames[$data['channel']]][$date] : '0';
					$phoneuser[$channelnames[$data['channel']]][$date] = isset( $phoneuser[$channelnames[$data['channel']]][$date] ) ? $phoneuser[$channelnames[$data['channel']]][$date] : '0';
					$realuser[$channelnames[$data['channel']]][$date] = isset( $realuser[$channelnames[$data['channel']]][$date] ) ? $realuser[$channelnames[$data['channel']]][$date] : '0';
					$recharge[$channelnames[$data['channel']]][$date] = isset( $recharge[$channelnames[$data['channel']]][$date] ) ? $recharge[$channelnames[$data['channel']]][$date] : '0';
				}
			}
			$channeldatas = [
				'userdevice' => $userdevice,
				'userview' => $userview,
				'register' => $register,
				'phoneuser' => $phoneuser,
				'realuser' => $realuser,
				'recharge' => $recharge,
			];
			$tmp = [];
			//dump($channeldatas);
			foreach ( $channeldatas as $datakey => &$channellist )
			{
				foreach ( $channellist as $chnnelkey => $channel )
				{
					$tmp[$chnnelkey] = array_sum( $channel );
				}
				arsort( $tmp );
				//dump($tmp);
				$tmp = array_slice( $tmp, 0, 10, true );
				$diff = array_diff_key( $channellist, $tmp );
				$channellist = array_diff_key( $channellist, $diff );
				foreach ( $channellist as &$v )
				{
					ksort( $v );
				}
				//dump($channellist);
			}
			//dump($channeldatas);
			//$this->channels = $channelnames;
			$this->jsdata = json_encode( $channeldatas );

		}
		else
		{
			$this->page = $Page->show();
			$this->data = $datas;
		}
		$this->sum = $sum;
		//$this->page = $Page->show();
		$this->title = $titles[$counttype];
		$this->channels = $channelnames;
		$this->display( 'channelstatis' );
	}

	public function promocodestatis()
	{
		I( 'get.timestart' ) ? $stime = I( 'get.timestart' ) : $_GET['timestart'] = $stime = date( 'Y-m-01', strtotime( get_date() ) - 86400 );
		I( 'get.timeend' ) ? $etime = I( 'get.timeend' ) : $_GET['timeend'] = $etime = date( 'Y-m-d', strtotime( get_date() ) - 86400 );
		$stime = date( 'Ymd', strtotime( $stime ) );
		$etime = date( 'Ymd', strtotime( $etime ) );
		$where['date'] = [ [ 'egt', $stime ], [ 'elt', $etime ] ];

		$counttype = I( 'get.count_type' ) ? I( 'get.count_type' ) : 'day';
		if( $promocode = I( 'get.promocode' ) )
		{
			$where['promocode'] = $promocode;
		}
		$where['type'] = I( 'get.type' ) ? I( 'get.type' ) : 2;

		$counttype = I( "get.count_type" ) ? I( "get.count_type" ) : "day";
		$groupdates = [
			'day' => "DATE_FORMAT(date,'%Y-%m-%d')",
			'week' => "DATE_FORMAT(date,'%Y-%u')",
			'month' => "DATE_FORMAT(date,'%Y-%m')",
			'hours' => "concat(date,':',hours,'点')",
		];
		$groups = [
			'day' => "DATE_FORMAT(date,'%Y%m%d'),promocode",
			'week' => "DATE_FORMAT(date,'%Y%u'),promocode",
			'month' => "DATE_FORMAT(date,'%Y%m'),promocode",
			'hours' => "date,hours,promocode",
		];
		$titles = [
			'day' => "日",
			'week' => "周",
			'month' => "月",
			'hours' => "小时",
		];

		$dao = D( "statispromocode" );
		$field = "promocode, sum(register) as register,sum(phoneuser) as phoneuser,sum(realuser) as realuser,sum(count_num) as count_num,sum(count_rmb) as count_rmb,
					sum(count_num_new) as count_num_new,sum(count_rmb_new) as count_rmb_new";
		$sum = $dao
			->field( $field )
			->where( $where )
			->find();

		if( $sum )
		{
			$sum['count_rmb'] = $sum['count_rmb'] / Statis::MONEY_CODE;
			$sum['count_rmb_new'] = $sum['count_rmb_new'] / Statis::MONEY_CODE;
		}

		if( I( 'get.export' ) )
		{
			$datas = $dao
				->where( $where )
				->field( $field . ',' . $groupdates[$counttype] . ' as date' )
				->limit( '0,10000' )
				->order( 'date desc, hours desc' )
				->select();
		}
		else
		{
			$count = $dao
				->field( '*' )
				->where( $where )
				->group( $groups[$counttype] )
				->count();
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );

			$datas = $dao
				->where( $where )
				->field( $field . ',' . $groupdates[$counttype] . ' as date' )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->group( $groups[$counttype] )
				->order( 'date desc, hours desc' )
				->select();
		}
		if( $datas )
		{
			$promocode = D( 'promocode' )->getField( 'promocode,name' );
			foreach ( $datas as $key => $data )
			{
				$datas[$key]['count_rmb'] = $data['count_rmb'] / Statis::MONEY_CODE;
				$datas[$key]['count_rmb_new'] = $data['count_rmb_new'] / Statis::MONEY_CODE;
				$datas[$key]['promocodename'] = $promocode[$data['promocode']];
			}
		}
		if( I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '日期', '推广码', '推广码名称', '注册', '手机认证', '实名认证', '充值人次', '充值金额', '新增充值人数', '新增充值金额' );
			foreach ( $datas as $data )
			{
				$excel[] = array( $data['date'], $data['promocode'], $data['promocodename'], $data['register'], $data['phoneuser'], $data['realuser'], $data['count_num'], $data['count_rmb'], $data['count_num_new'], $data['count_rmb_new'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '推广码' . $titles[$counttype] . '统计' );
			exit;
		}

		$this->data = $datas;
		$this->sum = $sum;
		$this->page = $Page->show();
		$this->display();
	}


	public function gettimelabel( $stime, $etime, $type = 'day' )
	{
		$label = [];
		if( $type == 'day' )
		{
			for ( $date = date( 'Ymd', strtotime( $stime ) ); strtotime( $date ) <= strtotime( $etime );
				$date =
					date( 'Ymd', strtotime( '1 day', strtotime( $date ) ) ) )
			{
				$label[] = $date;
			}
		}
		elseif( $type == 'hour' || $type == 'hours' )
		{
			for ( $date = date( 'Ymd', strtotime( $stime ) ); strtotime( $date ) <= strtotime( $etime );
				$date =
					date( 'Ymd', strtotime( '1 day', strtotime( $date ) ) ) )
			{
				for ( $i = 0; $i < 24; $i++ )
				{
					$label[] = $i < 10 ? $date . ":0$i" . "点" : $date . ":$i" . "点";
				}
			}
		}
		elseif( $type == 'week' )
		{
			for ( $date = date( 'Ymd', strtotime( $stime ) ); strtotime( $date ) <= strtotime( $etime );
				$date =
					date( 'Ymd', strtotime( '1 day', strtotime( $date ) ) ) )
			{
				$week = date( 'YW', strtotime( $date ) );
				$weekday = $this->getWeekDate( $week );
				$startday = ( strtotime( $stime ) > strtotime( $weekday[0] ) ) ? $stime : $weekday[0];
				$endday = ( strtotime( $etime ) < strtotime( $weekday[1] ) ) ? $etime : $weekday[1];
				$week = $week . "周(" . $startday . "-" . $endday . ")";
				$label[] = $week;
			}
			$label = array_unique( $label );
		}
		elseif( $type == 'month' )
		{
			for ( $date = date( 'Ymd', strtotime( $stime ) ); strtotime( $date ) <= strtotime( $etime );
				$date =
					date( 'Ymd', strtotime( '1 day', strtotime( $date ) ) ) )
			{
				$label[] = date( 'Ym', strtotime( $date ) );
			}
			$label = array_unique( $label );
		}
		else
		{
			return false;
		}
		return $label;
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


	public function rechargestatis()
	{
		$channelnames = D( "ChannelVersion" )->getField( "channel,channelName" );
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $_GET["timestart"] = $stime = date( "Y-m-01", strtotime( get_date() ) - 86400 );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $_GET["timeend"] = $etime = date( "Y-m-d", strtotime( get_date() ) - 86400 );

		$stime = date( "Ymd", strtotime( $stime ) );
		$etime = date( "Ymd", strtotime( $etime ) );

		$where['status'] = 100;
		$where['date'] = [ [ 'egt', $stime ], [ 'elt', $etime ] ];

		$counttype = I( "get.count_type" ) ? I( "get.count_type" ) : "day";

		$dao = D( "Statisrecharge" );

		if( $channel = I( 'get.channel' ) )
		{
			$where['channel'] = $channel;
		}

		I( 'get.type' ) ? I( 'get.type' ) : $_GET['type'] = 1;
		if( $type = I( 'get.type' ) )
		{
			$where['type'] = $type;
		}
		if( $client = I( 'get.client' ) )
		{
			$where['client'] = [ "like", "%$client%" ];
		}
		if( $payclient = I( 'get.pay_client' ) )
		{
			$where['pay_channel'] = [ "like", "%$payclient%" ];
		}
		$groupdates = [
			'day' => "DATE_FORMAT(date,'%Y%m%d')",
			'week' => "DATE_FORMAT(date,'%Y%u')",
			'month' => "DATE_FORMAT(date,'%Y%m')",
			'hours' => "concat(date,':',hours)",
		];
		$groups = [
			'day' => "DATE_FORMAT(date,'%Y%m%d'),pay_channel,client,channel",
			'week' => "DATE_FORMAT(date,'%Y%u'),pay_channel,client,channel",
			'month' => "DATE_FORMAT(date,'%Y%m'),pay_channel,client,channel",
			'hours' => "date,hours",
		];
		$titles = [
			'day' => "日",
			'week' => "周",
			'month' => "月",
			'hours' => "小时",
		];
		$field = "max(date) as maxdate ,min(date) as mindate ,client,pay_channel,channel,$groupdates[$counttype] as date,sum(count_num) as count_num,sum(count_user) as count_user,sum(count_rmb) as count_rmb,sum(count_num_new) as count_num_new,sum(count_user_new) as count_user_new,sum(count_rmb_new) as count_rmb_new";
		$order = " date";
		//根据条件查出所有，计算总额
		$countres = $dao
			->where( $where )
			->field( $field )
			->group( $groups[$counttype] )
			->order( $order )
			->select();

		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $countres;
		}
		elseif( $chart = I( 'get.chart' ) )
		{
			$results = $this->rechargestatisforline( $counttype, 'channel', $where, $dao );
		}
		else
		{//查出一页用于显示。
			$count = count( $countres );
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->where( $where )
				->field( $field )
				->group( $groups[$counttype] )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( $order )
				->select();
		}

		foreach ( $results as $result )
		{
			$result['count_rmb'] = $result['count_rmb'] / Statis::MONEY_CODE;
			$result['count_rmb_new'] = $result['count_rmb_new'] / Statis::MONEY_CODE;
			/* $counttype == 'week' && $result['date'] = $result['date']."周(".$result['mindate']."-".$result['maxdate'].")";
			$counttype == 'hours' && $result['date'] = $result['date']."点"; */
			if( $counttype == 'week' )
			{
				$weekday = $this->getWeekDate( $result['date'] );
				$startday = ( strtotime( $stime ) > strtotime( $weekday[0] ) ) ? $stime : $weekday[0];
				$endday = ( strtotime( $etime ) < strtotime( $weekday[1] ) ) ? $etime : $weekday[1];
				$result['date'] = $result['date'] . "周(" . $startday . "-" . $endday . ")";
			}
			if( $counttype == 'hours' )
			{
				list( $daystr, $hour ) = explode( ':', $result['date'] );
				$result['date'] = $hour < 10 ? "$daystr:0$hour" . "点" : "$daystr:$hour" . "点";
			}
			$datas[] = $result;
		}
		foreach ( $countres as $result )
		{
			$result['count_rmb'] = $result['count_rmb'] / Statis::MONEY_CODE;
			$result['count_rmb_new'] = $result['count_rmb_new'] / Statis::MONEY_CODE;
			$sum['count_rmb'] += $result['count_rmb'];
			$sum['count_num'] += $result['count_num'];
			$sum['count_user'] += $result['count_user'];
			$sum['count_rmb_new'] += $result['count_rmb_new'];
			$sum['count_num_new'] += $result['count_num_new'];
			$sum['count_user_new'] += $result['count_user_new'];
		}


		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '日期', '平台', '渠道', '注册来源', '名称', '笔数', '用户数', '金额(元)', '新增笔数', '新增人数', '新增金额(元)' );
			foreach ( $datas as $data )
			{
				$excel[] = array( $data['date'], $data['pay_channel'], $data['client'], $data['channel'], $channelnames[$data['channel']], $data['count_num'], $data['count_user'], $data['count_rmb'], $data['count_num_new'], $data['count_user_new'], $data['count_rmb_new'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . "充值$titles[$counttype]统计" );
		}
		else if( $chart = I( 'get.chart' ) )
		{
			$label = $this->gettimelabel( $stime, $etime, $counttype );
			//$jsdata = [];
			$count_rmb = [];
			$count_rmb_new = [];
			$count_user = [];
			$count_user_new = [];
			$channelnames = D( "ChannelVersion" )->getField( "channel,channelName" );
			foreach ( $datas as $data )
			{
				if( $data['channel'] == '0' )
				{
					$channelnames[$data['channel']] = '其他';
				}
				$count_rmb[$channelnames[$data['channel']]][$data['date']] = $data['count_rmb'];
				$count_rmb_new[$channelnames[$data['channel']]][$data['date']] = $data['count_rmb_new'];
				$count_user[$channelnames[$data['channel']]][$data['date']] = $data['count_user'];
				$count_user_new[$channelnames[$data['channel']]][$data['date']] = $data['count_user_new'];
			}

			$tmpdata = [
				'count_rmb' => $count_rmb,
				'count_rmb_new' => $count_rmb_new,
				'count_user' => $count_user,
				'count_user_new' => $count_user_new,
			];
			/*dump($tmpdata);
			dump($label);*/
			$jsdata = [];
			foreach ( $label as $date )
			{
				foreach ( $tmpdata as $k => $typedata )
				{
					foreach ( $typedata as $channel => $v )
					{
						$jsdata[$k][$channel][$date] = isset( $v[$date] ) ? $v[$date] : '0';
					}
				}
			}
			//dump($jsdata);
			$this->jsdata = json_encode( $jsdata );
			$this->sum = $sum;
			$this->title = $titles[$counttype];
			$this->channels = $channelnames;
			$this->display( 'rechargestatis' );
		}
		else
		{
			$this->data = $datas;
			$this->sum = $sum;
			$this->title = $titles[$counttype];
			$this->page = $Page->show();
			$this->channels = $channelnames;
			$this->display( 'rechargestatis' );
		}
	}

	public function rechargedetail()
	{
		$companys = D( 'company' )->getField( "id,name" );
		$this->status = [ '0' => '失败', '100' => '成功' ];
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $_GET["timestart"] = $stime = date( "Y-m-01", strtotime( get_date() ) - 86400 );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $_GET["timeend"] = $etime = date( "Y-m-d", strtotime( get_date() ) - 86400 );
		date( "Y-m", strtotime( $etime ) ) == date( "Y-m", strtotime( $stime ) ) ? "" : $_GET["timeend"] = $etime = date( "Y-m-t", strtotime( $stime ) );
		$stime = date( "Y-m-d 00:00:00", strtotime( $stime ) );
		$etime = date( "Y-m-d 23:59:59", strtotime( $etime ) );
		$where['a.ctime'] = [ [ 'egt', $stime ], [ 'elt', $etime ] ];
		$dao = new \Common\Model\HPFMonthModel( "rechargeRecord", $stime );


		if( I( 'get.status' ) !== '-1' )
		{
			$status = I( 'get.status' );
			if( !isset( $_GET['status'] ) )
			{
				$where['a.status'] = $_GET['status'] = '100';
			}
			else
			{
				$where['a.status'] = $status;
			}
		}

		if( I( 'get.isanchor' )>0 )
		{
			$isanchor = I( 'get.isanchor' );
			if( $isanchor == 1 )
			{
				$where['b.uid'] = [ 'gt', 0 ];
			}
			else
			{
				$where['b.uid'] = [[ 'EXP', 'is null' ],[ 'eq', '' ],[ 'eq', 0 ],'or'];
			}
		}


		$results = $dao
			->alias( 'a' )
			->field( "a.*,b.cid,b.uid as buid" )
			->join( "left join anchor b on a.uid = b.uid  " )
			->where( $where )
			->order( 'a.ctime desc ' )
			->select();

		foreach ( $results as $result )
		{
			$data = $result;
			$data['rmb'] = $data['rmb'] / 1000;
			$data['buid'] ? $sum['anchor_rmb'] += $data['rmb'] : $sum['user_rmb'] += $data['rmb'];
			$sum['rmb'] += $data['rmb'];
		}

		$count = count( $results );
		$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );

		$results = $dao
			->alias( 'a' )
			->field( "a.*,b.cid,b.uid as buid" )
			->join( "left join anchor b on a.uid = b.uid  " )
			->where( $where )
			->limit( $Page->firstRow . ',' . $Page->listRows )
			->order( 'a.ctime desc  ' )
			->select();

		foreach ( $results as $result )
		{
			$data = $result;
			$data['isanchor'] = '否';
			$data['rmb'] = $data['rmb'] / 1000;
			$data['status'] = $this->status[$result['status']];
			$data['ip'] = long2ip( $result['ip'] );
			$data['cid'] && $data['companyname'] = $companys[$data['cid']];
			$data['buid'] && $data['isanchor'] = '是';
			$datas[] = $data;
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( 'id', '创建时间', '支付时间', 'uid', '是否主播', '所属公司', '金额', '欢朋币', '平台', '订单来路', 'ip', 'thrid_order_id', 'thrid_buyer_id', 'promotionID', 'channel', 'otid', '状态', '描述' );
			foreach ( $datas as $data )
			{
				$excel[] = array( "\t" . $data['id'], $data['ctime'], $data['paytime'], $data['uid'], $data['companyname'], $data['rmb'], $data['hb'], $data['client'], $data['refer_url'], $data['ip'], "\t" . $data['thrid_order_id'], "\t" . $data['thrid_buyer_id'], $data['promotionID'], $data['channel'], "\t" . $data['otid'], $data['status'], $data['desc'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '渠道列表' );
		}
		$this->data = $datas;
		$this->sum = $sum;
		$this->page = $Page->show();
		$this->isanchor = [ '1' => '主播', '2' => '非主播' ];
		$this->display();
	}

	public function rechargestatisforline( $timetype, $linetype, $where, $dao )
	{
		static $timegroups = [
			'day' => "DATE_FORMAT(date,'%Y%m%d')",
			'week' => "DATE_FORMAT(date,'%Y%u')",
			'month' => "DATE_FORMAT(date,'%Y%m')",
			'hours' => "date,hours",
		];
		static $groupdates = [
			'day' => "DATE_FORMAT(date,'%Y%m%d')",
			'week' => "DATE_FORMAT(date,'%Y%u')",
			'month' => "DATE_FORMAT(date,'%Y%m')",
			'hours' => "concat(date,':',hours)",
		];
		static $linegroup = [
			'channel' => 'channel',
		];
		$field = $field = "channel,$groupdates[$timetype] as date,sum(count_user) as count_user,sum(count_rmb) as count_rmb,sum(count_num_new) as count_num_new,sum(count_num_new) as count_user_new,sum(count_rmb_new) as count_rmb_new";
		$group = $timegroups[$timetype] . ', ' . $linegroup[$linetype];
		$order = 'date';
		$countres = $dao
			->where( $where )
			->field( $field )
			->group( $group )
			->order( $order )
			->select();
		return $countres;
	}

	public function rechargestatisweek()
	{
		$_GET['count_type'] = 'week';
		$this->rechargestatis();
	}

	public function rechargestatismonth()
	{
		$_GET['count_type'] = 'month';
		$this->rechargestatis();
	}

	public function rechargestatishours()
	{
		$_GET['count_type'] = 'hours';
		$_GET['type'] = '2';
		$this->rechargestatis();
	}

	public function channelstatishours()
	{
		$_GET['count_type'] = 'hours';
		$_GET['type'] = '4';
		$this->channelstatis();
	}

	public function retainedstatis()
	{
		$dao = D( "statisretained" );
		$channelnames = D( "ChannelVersion" )->getField( "channel,channelName" );
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $_GET["timestart"] = $stime = date( "Y-m-01", strtotime( get_date() ) - 86400 );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $_GET["timeend"] = $etime = date( "Y-m-d", strtotime( get_date() ) - 86400 );
		$stime = date( "Ymd", strtotime( $stime ) );
		$etime = date( "Ymd", strtotime( $etime ) );
		$where['date'] = [ [ 'egt', $stime ], [ 'elt', $etime ] ];
		if( $channel = I( 'get.channel' ) )
		{
			$where['channel'] = $channel;
		}
		if( $client = I( 'get.client' ) )
		{
			$where['client'] = [ "like", "%$client%" ];
		}
		if( $payclient = I( 'get.pay_client' ) )
		{
			$where['pay_client'] = [ "like", "%$payclient%" ];
		}


		$countres = $dao
			->where( $where )
			->order( 'date' )
			->select();
		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $countres;
		}
		else
		{
			$count = count( $countres );
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->where( $where )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'date' )
				->select();
		}

		foreach ( $countres as $result )
		{
			$sum['t_0'] += $result['t_0'];
			$sum['t_1'] += $result['t_1'];
			$sum['t_3'] += $result['t_3'];
			$sum['t_7'] += $result['t_7'];
			$sum['t_15'] += $result['t_15'];
			$sum['t_30'] += $result['t_30'];
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '日期', '渠道编号', '渠道名称', '新增设备', '次日留存', '3日留存', '7日留存', '15日留存', '30日留存' );
			foreach ( $results as $data )
			{
				$excel[] = array( $data['date'], $data['channel'], $channelnames[$data['channel']], $data['t_0'], $data['t_1'], $data['t_3'], $data['t_7'], $data['t_15'], $data['t_30'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '留存统计' );
		}
		$this->data = $results;
		$this->sum = $sum;
		$this->page = $Page->show();
		$this->channels = $channelnames;
		$this->display();
	}


	public function runoff()
	{
		$uid = I( 'get.uid' ) ? I( 'get.uid' ) : '';
		$days = I( 'get.days' ) ? I( 'get.days' ) : '30';
		if( !is_numeric( $days ) || ( $days > 90 ) )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => '请求非法' ) );
		}
		$time = date( 'Y-m-d H:i:s', time() - ( $days * 86400 ) );
		$dao = D( 'useractive' );
		if( $uid )
		{
			$where['uid'] = array( "EQ", $uid );
		}
		$where['ltime'] = array( "ELT", $time );

		if( $export = I( 'get.export' ) )
		{//导出数据
			$data = $dao->where( $where )->getField( "uid,ltime" );
		}
		else
		{
			$count=$dao->where( $where )->count();
			$Page = new \HP\Util\Page( $count, $this->pageSize );
			$data = $dao->where( $where )->limit($Page->firstRow.','.$Page->listRows)->getField( "uid,ltime" );
		}
		$uids=array_keys($data);
		if($uids){
			$userinfo=Anchor::anchorInfo($uids);
			$isAnchor=$this->getcidByUids($uids);
			$company=Company::getCompanymap();
		}
		$datas=array();
		foreach ($data as $k=>$v){
			$tmp['uid']=$k;
			$tmp['nick']=isset($userinfo[$k])?$userinfo[$k]['nick']:'';
			$tmp['isanchor']=isset($isAnchor[$k])?'是':'否';
			$tmp['ltime']=$v;
			$tmp['company']=isset($isAnchor[$k])?$company[$isAnchor[$k]['cid']]['name']:'';
			$tmp['days']=secondFormat(time()-strtotime($v));
			array_push($datas,$tmp);
		}
		if($export = I('get.export')){//导出数据
			$excel[] = array('uid,昵称,是否是主播,所属经纪公司,上次登录时间,距离现在天数');
			foreach ($datas as $d) {
				$excel[] = array("\t".$d['uid'],"\t".$d['nick'],$d['isanchor'],"\t".$d['company'],"\t".$d['ltime'],"\t".$d['days']);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'流失列表');
		}
		$this->data=$datas;
		$this->page = $Page->show();
		$this->display();
	}


	/**获取cid
	 */
	public function getcidByUids( $uids )
	{
		$db = D( 'anchor' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->field( "uid,cid,level" )
			->where( $where )
			->getField( 'uid,cid,level' );
		return $res ? $res : array();
	}

}
