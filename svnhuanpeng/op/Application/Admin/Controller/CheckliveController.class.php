<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/8/15
 * Time: 13:34
 */

namespace Admin\Controller;

use HP\Op\Anchor;

class CheckliveController extends BaseController
{

	protected $pageSize = 10;

	protected function _access()
	{
		return [
			'record'=>['checklivebyip'],
			'record'=>['checklivebydevice'],
		];
	}

	public function checklivebyip(){
		$liveDao = D('live');
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		$stime = I('get.timestart') . " 00:00:00";
		$etime = I('get.timeend') . " 23:59:59";
		$results = $liveDao->query("select count( distinct uid ) as usercount,ip from live where ctime between '{$stime}' and '{$etime}' group by ip order by usercount desc");
		$status = I('get.status');
		$status = ($status)?$status:1;
		/*foreach ($results as &$result){
			if($result['usercount']>$status)
				$result['ip'] = long2ip($result['ip']);
			else
				unset($result);
		}*/
		$data = array_map(function($result)use($status){
			if($result['usercount']>$status)
			{
				$result['ip'] = long2ip($result['ip']);
				return $result;
			}
		},$results);
		$data = array_filter($data);
		$this->data = $data;
		$this->status = ['1'=>'用户大于1','5'=>'用户大于5','10'=>'用户大于10'];
		$this->display();
	}
	public function checklivebydevice(){
		$liveDao = D('live');
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		$stime = I('get.timestart') . " 00:00:00";
		$etime = I('get.timeend') . " 23:59:59";
		$results = $liveDao->query("select count( distinct uid ) as usercount,deviceid from live where ctime between '{$stime}' and '{$etime}' group by deviceid order by usercount desc");

		$status = I('get.status');
		$status = ($status)?$status:1;
		/*foreach ($results as &$result){
			if($result['usercount']>$status)
				$result['ip'] = long2ip($result['ip']);
			else
				unset($result);
		}*/
		$data = array_map(function($result)use($status){
			if($result['usercount']>$status)
			{
				//$result['ip'] = long2ip($result['ip']);
				return $result;
			}
		},$results);
		$data = array_filter($data);
		$this->data = $data;
		$this->status = ['1'=>'用户大于1','5'=>'用户大于5','10'=>'用户大于10'];
		$this->display();
	}

	public function record(){
		$liveDao = D('live');
		$anchorDao = D('anchor');
		$companys = D('company')->field('id,name')->getField('id,name');
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		$stime = I('get.timestart') . " 00:00:00";
		$etime = I('get.timeend') . " 23:59:59";

		$where['a.ctime'] = ['between',[$stime,$etime]];
		if($deviceid = I('get.deviceid')){
			$where['a.deviceid'] = $deviceid;
		}
		if($ip = I('get.ip')){
			$ip = ip2long($ip);
			$where['a.ip'] = $ip;
		}
		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}

		if($company = I('get.company')){
			list($name,$cid) = explode('|',$company);
			$where['b.cid'] = $cid;
		}
		//
		if($export = I('get.export')){//导出数据

			$datas = $liveDao
				->alias(' a ')
				->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
				->field('a.liveid,a.uid,a.deviceid,a.ip,a.stime,a.etime,b.cid')
				->where($where)
				->select();
		}else{

			$count = $datas = $liveDao
				->alias(' a ')
				->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$datas = $liveDao
				->alias(' a ')
				->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
				->field('a.liveid,a.uid,a.deviceid,a.ip,a.stime,a.etime,b.cid')
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->select();
		}

		$dir       = array(
			'DEV' => array( 'v' => 'dev/v/' ),
			'PRE' => array( 'v' => 'pre/v/' ),
			'PRO' => array( 'v' => 'pro/v/' )
		);
		$dataInfos = $liveDao
			->alias(' a ')
			->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
			->field('a.liveid,a.uid,a.deviceid,a.ip,a.stime,a.etime,b.cid')
			->where($where)
			->select();
		$userIds=array_unique(array_column($dataInfos,"uid"));
		if($userIds){
			$userInfos=Anchor::anchorInfo($userIds);
		}else{
			$userInfos=array();
		}
		foreach ($datas as &$data){
			$data['ip'] = long2ip($data['ip']);
			$data['length'] = secondFormatH( (strtotime($data['etime'])-strtotime($data['stime'])) );
			$data['url'] =  sfile($dir[$GLOBALS['env']]['v'] . $data['liveid'] . '.mp4');
			$data['nick'] =  isset($userInfos[$data['uid']]) ? $userInfos[$data['uid']]['nick'] : '';
		}
		$Info=array();
		$forlist=array_values($userIds);
		if($forlist){
			foreach ($dataInfos as $key => $info) {
				$ctime=strtotime($info['stime']);
				$etime=strtotime($info['etime']);
				if(($ctime!="-62170012800")  &&  ($etime !="-62170012800")){
					$length[$info['uid']][] = $etime-$ctime;
				}
			}
			for($i=0,$k=count($forlist);$i<$k;$i++){
				$tmp['uid'] = $forlist[$i];
				$tmp['nick'] = isset( $userInfos[$forlist[$i]] ) ? $userInfos[$forlist[$i]]['nick'] : '';
				$tmp['length'] = secondFormatH(array_sum($length[$forlist[$i]]));
				array_push( $Info, $tmp );
			}
		}
		if($export = I('get.export')){//导出数据
			$excel[] = array('IP','设备','用户UID','昵称','公司','开始时间','结束时间','直播时长');
			foreach ($datas as $data) {
				$excel[] = array("\t".$data['ip'],"\t".$data['deviceid'],$data['uid'],"\t".$data['nick'],$companys[$data['cid']],"\t".$data['stime'],"\t".$data['etime'],$data['length']);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'直播检测明细列表');
		}

		$this->data = $datas;
		$this->dataInfo = $Info;
		$this->companys = $companys;
		$this->page = $Page->show();


		$this->display();
	}
}