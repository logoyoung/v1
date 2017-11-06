<?php

namespace Admin\Controller;
use HP\Log\Log;
use HP\Op\Check;
use HP\Op\Admin;
class LivestatisController extends BaseController
{
	protected $pageSize = 10;

	static $pat = [
		'd'=>'%Y-%m-%d',
		'w'=>'%Y-%m/%u',
		'm'=>'%Y-%m',
	];

	protected function _access(){
		//return self::ACCESS_NOLOGIN;
		return [
			'livestatis'=>['livestatis'],
			'statisgame'=>['statisgame'],
			'livestatisweek'=>['livestatisweek'],
			'livestatismonth'=>['livestatismonth'],
		];
	}
	public function livestatis(){

		$liveDao = D('livestatis');
		//$streamDao = D('livestream');
		I("get.timestart")?$stime = I("get.timestart"):$_GET["timestart"] = $stime = date("Y-m-01",strtotime(get_date())-86400);
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-d', strtotime('-1 day'));
		$start = $_GET['timestart'];
		$end = $_GET['timeend'];
		/*if(I('get.timestart')) {
			
		}*/
		/*if(!($start = I('get.timestart'))){
			$start = $_GET['timestart']= ((int)date('d') == 1) ? date('Y-m-01', strtotime('-1 month')) : date('Y-m-01');
		}
		if(!($end = I('get.timeend'))){
			$end = $_GET['timeend']= ((int)date('d') == 1) ? date('Y-m-t', strtotime('-1 month')) : date('Y-m-d', strtotime('-1 day'));
		}*/

		if($start && $end){
			$start = str_replace('-', '', $start);
			$end = str_replace('-', '', $end);
			$where['date'] = ['between',"$start,$end"];
		}
		$where['type'] = $liveDao->getStatus('day');
		if($export = I('get.export')||$chart = I('get.chart')){//导出数据
			$results = $liveDao
				->where($where)
				->field("*")
				->select();
		}else{
			$count = $liveDao
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);

			$results = $liveDao
				->where($where)
				->field("*")
				->limit($Page->firstRow.','.$Page->listRows)
				->order('`date` desc')
				->select();
		}
		foreach ($results as $k=>&$v)
		{
			$v['date'] = date("Y-m-d",strtotime($v['date']));
			$v['troughtime'] = date("H:i:s",strtotime($v['troughtime']."0000")) . '--' . date("H:i:s",strtotime($v['troughtime']."0000") + 7199);
			$v['peaktime'] = date("H:i:s",strtotime($v['peaktime']."0000")) . '--' . date("H:i:s",strtotime($v['peaktime']."0000") + 7199);
			$v['sttroughtime'] = date("H:i:s", $v['sttroughtime']);
			$v['stpeaktime'] = date("H:i:s", $v['stpeaktime']);
            $v['length'] = round($v['length']/3600, 2) . '小时';
		}
		$total = $this->total($start,$end,1);
		if($export = I('get.export')){//导出数据
            $excel[] = array('日期','开播数量','开播人数','直播总时长','开播峰值','峰值时段','开播低谷','低谷时段','同时直播峰值','峰值时间','同时直播低谷','低谷时间');
            foreach ($results as $data) {
                $excel[] = array($data['date'],$data['livecount'],$data['liveusercount'],$data['length'],$data['peakcount'],$data['peaktime'],$data['troughcount'],$data['troughtime'],$data['stpeakcount'],$data['stpeaktime'],$data['sttroughcount'],$data['sttroughtime']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'直播日统计');
        }
		if($chart = I('get.chart')){
			//dump($results);
			$this->jsdata = json_encode($results);
		}else
		{
			$this->datas     = $results;
			$this->page      = $Page->show();
		}
		$this->total     = $total['livecount'];
		$this->usertotal = $total['liveusercount'];
		$this->display();
	}
	
	
	public function statisgame(){
		$liveDao = D('livestatis');
		$gameDao = D('game');

		//isset($_GET['timestart'])?$_GET['timestart']=$_GET['timestart']:$_GET['timestart']= date('Y-m-01', time());
		I("get.timestart")?$stime = I("get.timestart"):$_GET["timestart"] = $stime = date("Y-m-01",strtotime(get_date())-86400);
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-d', strtotime('-1 day'));

		$game = $gameDao
			->field('gameid,name')
			->select();
		$games = [];
		foreach ($game as $gameid => $gamename){
			$games[$gamename['gameid']] = $gamename['name'];
		}
		unset($game);

		/*isset($_GET['timestart'])?$_GET['timestart']=$_GET['timestart']:$_GET['timestart']= date('Y-m-01', strtotime('-1 month'));
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-t', strtotime('-1 month'));*/
		isset($_GET['timestart'])?$_GET['timestart']=$_GET['timestart']:$_GET['timestart']= date('Y-m-01', time());
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-d', strtotime('-1 day'));
		if($start = I('get.timestart')){
			$where['ctime'] = ['gt',$start];
		}
		if($end = I('get.timeend')){
			$where['ctime'] = ['lt',$end];
		}

		if($start && $end){
			$start = str_replace('-','',$start);
			$end = str_replace('-','',$end);
			$where['date'] = ['between',"$start,$end"];
		}
		$where['type'] = $liveDao->getStatus('game');
		if($gameid = I('get.gid')){
			$where['gameid'] = $gameid;
		}
		if($export = I('get.export')||$chart = I('get.chart')){//导出数据
			$order ="livecount desc";
			//if($chart) $order ="date desc";
			$results = $liveDao
				->where($where)
				->field("*")
				->order($order)
				->select();
		}else{
			$count = $liveDao
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);

			$results = $liveDao
				->where($where)
				->field("*")
				->order('livecount desc')
				->limit($Page->firstRow.','.$Page->listRows)
				->select();
		}

		foreach ($results as $k=>&$v)
		{
			$v['date'] = date("Y-m-d",strtotime($v['date']));
			$v['troughtime'] = date("Y-m-d H",strtotime($v['troughtime']."0000")) ;
			$v['peaktime'] = date("Y-m-d H",strtotime($v['peaktime']."0000"));
            $v['length'] = round($v['length']/3600, 2) . '小时';
		}
		$total = $this->total($start,$end,2,$gameid);
		if($export = I('get.export')){//导出数据
			$excel[] = array('日期','开播数量','开播人数','直播总时长','开播峰值','峰值时刻','开播低谷','低谷时刻');
			foreach ($results as $data) {
				$excel[] = array($data['date'],$data['livecount'],$data['liveusercount'],$data['length'],$data['peakcount'],$data['peaktime'],$data['troughcount'],$data['troughtime'],$games[$data['gameid']]);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'直播游戏统计');
		}
		if($chart = I('get.chart')){
			$jsdata = [];
			//dump($results);
			foreach ($results as $data ){
				//dump($games[$data['gameid']]);
				$jsdatalivecount[$games[$data['gameid']]][$data['date']] = $data['livecount'];
				$jsdataliveusercount[$games[$data['gameid']]][$data['date']] = $data['liveusercount'];
				$jsdatapeakcount[$games[$data['gameid']]][$data['date']] = $data['peakcount'];
				$jsdatatroughcount[$games[$data['gameid']]][$data['date']] = $data['troughcount'];
			}

			$jsdatalivecount = array_slice($jsdatalivecount,0,5);
			$jsdataliveusercount = array_slice($jsdataliveusercount,0,5);
			$jsdatapeakcount = array_slice($jsdatapeakcount,0,5);
			$jsdatatroughcount = array_slice($jsdatatroughcount,0,5);

			$jsdatalivecountkeys = array_keys($jsdatalivecount);
			$jsdataliveusercountkeys = array_keys($jsdataliveusercount);
			$jsdatapeakcountkeys = array_keys($jsdatapeakcount);
			$jsdatatroughcountkeys = array_keys($jsdatatroughcount);
			for ($date=date('Y-m-d',strtotime($start));strtotime($date)<=strtotime($end);
				$date=
					date('Y-m-d',strtotime('1 day',strtotime($date)))){
				foreach ($jsdatalivecountkeys as $v){
					$jsdatalivecount[$v][$date] = isset($jsdatalivecount[$v][$date])?$jsdatalivecount[$v][$date]:'0';
				}
				foreach ($jsdataliveusercountkeys as $v){
					$jsdataliveusercount[$v][$date] = isset($jsdataliveusercount[$v][$date])?$jsdataliveusercount[$v][$date]:'0';
				}
				foreach ($jsdatapeakcountkeys as $v){
					$jsdatapeakcount[$v][$date] = isset($jsdatapeakcount[$v][$date])?$jsdatapeakcount[$v][$date]:'0';
				}
				foreach ($jsdatatroughcountkeys as $v){
					$jsdatatroughcount[$v][$date] = isset($jsdatatroughcount[$v][$date])?$jsdatatroughcount[$v][$date]:'0';
				}
			}
			$jsdatalivecount = array_map(function($v){
				ksort($v);
				return $v;
			},$jsdatalivecount);
			$jsdataliveusercount = array_map(function($v){
				ksort($v);
				return $v;
			},$jsdataliveusercount);
			$jsdatapeakcount = array_map(function($v){
				ksort($v);
				return $v;
			},$jsdatapeakcount);
			$jsdatatroughcount = array_map(function($v){
				ksort($v);
				return $v;
			},$jsdatatroughcount);


			$jsdata = [
				'jsdatalivecount' => $jsdatalivecount,
				'jsdataliveusercount' => $jsdataliveusercount,
				'jsdatapeakcount' => $jsdatapeakcount,
				'jsdatatroughcount' => $jsdatatroughcount,
			];

			$this->jsdata = json_encode($jsdata);
		}else
		{
			$this->datas     = $results;
			$this->page      = $Page->show();
		}
		//$this->datas = $results;
		//$this->page = $Page->show();
		$this->games = $games;
		$this->total = $total['livecount'];
		$this->usertotal = $total['liveusercount'];
		$this->display();
	}
	private function getdata($type='d',&$total){
		$pat = self::$pat[$type];
		$liveDao = D('livestatis');
		//$streamDao = D('livestream');
		/*isset($_GET['timestart'])?$_GET['timestart']=$_GET['timestart']:$_GET['timestart']= date('Y-m-01', strtotime('-1 month'));
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-t', strtotime('-1 month'));*/
		//isset($_GET['timestart'])?$_GET['timestart']=$_GET['timestart']:$_GET['timestart']= date('Y-m-01', time());
		I("get.timestart")?$stime = I("get.timestart"):$_GET["timestart"] = $stime = date("Y-m-01",strtotime(get_date())-86400);
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-d', strtotime('-1 day'));
		if($start = I('get.timestart')){
			$where['ctime'] = ['gt',$start];
		}
		if($end = I('get.timeend')){
			$where['ctime'] = ['lt',$end];
		}
		if($start && $end){
			$start = str_replace('-','',$start);
			$end = str_replace('-','',$end);
			$where['date'] = ['between',"$start,$end"];
		}
		$where['type'] = $liveDao->getStatus('day');
		if($export = I('get.export')||$chart = I('get.chart')){//导出数据
			$results = $liveDao
				->where($where)
				->field("sum(livecount) livecount,min(troughcount) troughcount,max(peakcount) peakcount ,date_format(date,'{$pat}') date")
				->group("date_format(date,'{$pat}')")
				->select();
		}else{
			$count = $liveDao
				->where($where)
				->field('count(*)')
				->group("date_format(date,'{$pat}')")
				->count();
			$count = count($count);
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);

			$results = $liveDao
				->where($where)
				->field("sum(livecount) livecount,min(troughcount) troughcount,max(peakcount) peakcount ,date_format(date,'{$pat}') date")
				->group("date_format(date,'{$pat}')")
				->limit($Page->firstRow.','.$Page->listRows)
				->select();
		}
		/*foreach ($results as $result){
			$condition['peakcount'] = $result['peakcount'];
			$t = date('t',strtotime($result['date']));
			$newdate = date('Ym',strtotime($result['date']));
			$t = $t-1;
			$condition['date'] = ['between',""];
			$liveDao
				->
		}*/
		$total = $this->total($start,$end,1);
		if($export = I('get.export')){//导出数据
			if($type == 'w'){
				$tname = '周';
			}
			elseif($type == 'm'){
				$tname = '月';
			}
			$excel[] = array('月份/周','开播数量','开播峰值','开播低谷');
			foreach ($results as $data) {
				$excel[] = array($data['date'],$data['livecount'],$data['peakcount'],$data['troughcount']);
			}

			\HP\Util\Export::outputCsv($excel,date('Y-m-d')."直播{$tname}统计");
		}

		return $results;
	}
	public function livestatisweek(){
		$datas = $this->getdata('w',$total);
		if($chart = I('get.chart')){
			$this->jsdata = json_encode($datas);
		}else{
			$this->datas = $datas;
		}
		//$this->datas = $datas;
		$this->total = $total['livecount'];
		$this->usertotal = $total['liveusercount'];
		$this->display();

	}
	public function livestatismonth(){
		$datas = $this->getdata('m',$total);
		//$this->datas = $datas;
		if($chart = I('get.chart')){
			$this->jsdata = json_encode($datas);
		}else{
			$this->datas = $datas;
		}
		$this->total = $total['livecount'];
		$this->usertotal = $total['liveusercount'];
		$this->display();
	}
	public function total($start,$end,$type,$gameid=null){
		$where = [];
		$liveDao = D('livestatis');
		$where['date'] = ['between',"$start,$end"];
		$where['type'] = $type;
		if($gameid) $where['gameid'] = $gameid;
		$count = $liveDao
			->where($where)
			->field('sum(livecount) as livecount,sum(liveusercount) as liveusercount')
			->select();//dump($count);
		return $count[0];
	}
}
