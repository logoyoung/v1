<?php
/**
 * 根据推荐码注册的用户统计
 */
namespace Cli\Controller;
ini_set('memory_limit', '512M');
class PromocodestatisController extends \Think\Controller
{
	const MONEY_CODE = 1000;//充值表rmb单位
	const RECHARGE_TYPE_DAY = 1;
	const RECHARGE_TYPE_CHANNEL = 2;
	const RECHARGE_STATUS_SECCESS = 100;
	const RECHARGE_STATUS_FAILED = 0;
	
	const REGISTER_TYPE_DAY = 1;
	const REGISTER_TYPE_DAY_PROMOCODE = 2;
	const REGISTER_TYPE_HOURS = 3;
	const REGISTER_TYPE_HOURS_PROMOCODE = 4;
	
	public function reset($do=null, $stime=null, $etime=null)
	{
		$dao = D('statispromocode');
		$stime = $stime ? $stime : date("Y-m-d",strtotime('-1 day'));
		$etime = $etime ? $etime : $stime;

		if($do == 'do') {
		   $dao->where(" date>= '".date("Ymd",strtotime($stime))."' and date <= '".date("Ymd",strtotime($etime))."' ")->delete();
		} else {
		   $res = $dao->fetchSql(true)->where(" date>= '".date("Ymd",strtotime($stime))."' and date <= '".date("Ymd",strtotime($etime))."' ")->delete();
		   echo $res . chr(10);
		}
		self::init($do, $stime, $etime);
	}
   
   
	public function init($do=null, $stime=null, $etime=null)
	{
		$dao = D("statispromocode");
		$stime = $stime ? $stime : date("Y-m-d",strtotime('-1 day'));
		$etime = $etime ? $etime : $stime;

		$datas = self::getUserStatisByday($stime, $etime);
	
		foreach ($datas['data_day'] as $date=>$data){//日数据
			$data['date'] = date('Ymd', strtotime($date));
			$data['type'] = self::REGISTER_TYPE_DAY;
			$data['userview'] = count($data['userview']);
			if($do == 'do') {
				echo $dao->add($data);
			} else {
				echo $dao->fetchSql(true)->add($data) . chr(10);
			}
		}
		foreach ($datas['data_promocode'] as $date=>$promocodes){//日推广数据
			foreach ($promocodes as $promocode=>$data){
				$data['date'] = date('Ymd', strtotime($date));
				$data['promocode'] = $promocode;
				$data['type'] = self::REGISTER_TYPE_DAY_PROMOCODE;
				$data['userview'] = count($data['userview']);
				if($do == 'do') {
					echo $dao->add($data);
				} else {
					echo $dao->fetchSql(true)->add($data) . chr(10);
				}
			}
		}
		foreach ($datas['data_hours'] as $date=>$data){//小时日数据
			$date = $date . ':00:00';
			$data['date'] = date("Ymd",strtotime($date));
			$data['hours'] = date("H",strtotime($date));
			$data['type'] = self::REGISTER_TYPE_HOURS;
			$data['userview'] = count($data['userview']);
			if($do == 'do') {
				echo $dao->add($data);
			} else {
				echo $dao->fetchSql(true)->add($data) . chr(10);
			}
		}
		foreach ($datas['data_hours_promocode'] as $date=>$promocodes){//小时推广
			$date = $date.":00:00";
			foreach ($promocodes as $promocode=>$data){
				$data['date'] = date("Ymd",strtotime($date));
				$data['hours'] = date("H",strtotime($date));
				$data['promocode']=$promocode;
				$data['type']=self::REGISTER_TYPE_HOURS_PROMOCODE;
				$data['userview'] = count($data['userview']);
				if($do == 'do') {
					echo $dao->add($data);
				} else {
					echo $dao->fetchSql(true)->add($data) . chr(10);
				}
			}
		}
	}
   
   
	function getUserStatisByday($stime, $etime)
	{
		$stime = $stime." 00:00:00";
		$etime = date("Y-m-d",strtotime($etime)+86400)." 00:00:00";
		$promocodes = self::getUserPromocodes();
		if(!$promocodes) {
			exit('没有数据');
		}
		$datas = [];
		self::getUserByday($stime, $etime, $promocodes, $datas);//注册  手机认证
		self::getRealUserByday($stime, $etime, $promocodes, $datas);//实名认证
		self::getRecharges($stime, $etime, $promocodes, $datas);//充值  新增充值
		return $datas;
	}
    
    /**
     * 取所有通过推广码注册的用户
     * @return array
     */
    static function getUserPromocodes()
    {
		$dao = D("channelUser");
    	$res = $dao->where(['promocode'=>['neq', '']])->getField("uid,promocode");
    	return $res;
    }
    
    /**
     * 获取注册及手机认证用户
     * 2017年8月30日
     */
    static function getUserByday($stime, $etime, $promocodes, &$datas)
    {
    	$where = [];
    	$where['rtime'] = [['egt', $stime],['elt', $etime]];
    	$where['uid'] = ['in', array_keys($promocodes)];
    	$res = D("userstatic")->field("uid,rtime,phone")->where($where)->order("uid")->select();
    	$key_day = self::getDatakey('day');
    	$key_hours = self::getDatakey('hours');
    	foreach ($res as $data){
    		$day = date($key_day,strtotime($data['rtime']));
    		$datas['data_day'][$day]["register"]++;
    		$data['phone'] && $datas['data_day'][$day]["phoneuser"]++;
    		$promocode = $promocodes[$data["uid"]];
    		$data['phone'] && $datas['data_promocode'][$day][$promocode]["phoneuser"]++;
    		$datas['data_promocode'][$day][$promocode]["register"]++;
    		
    		$day = date($key_hours, strtotime($data['rtime']));
    		$datas['data_hours'][$day]["register"]++;
    		$data['phone'] && $datas['data_hours'][$day]["phoneuser"]++;
    		$promocode = $promocodes[$data["uid"]];
    		$data['phone'] && $datas['data_hours_promocode'][$day][$promocode]["phoneuser"]++;
    		$datas['data_hours_promocode'][$day][$promocode]["register"]++;
    	}
    	return $datas;
    }
    
    /**
     * 获取实名认证用户
     * 2017年8月30日
     */
    static function getRealUserByday($stime, $etime, $promocodes, &$datas)
    {
    	$where = [];
    	$where['status'] = RN_PASS;
    	$where['passtime'] = [['egt', $stime],['elt', $etime]];
    	$where['uid'] = ['in', array_keys($promocodes)];
    	$res = D("userrealname")->field("uid,passtime")->where($where)->order("passtime")->select();
    	$key_day = self::getDatakey('day');
    	$key_hours = self::getDatakey('hours');
    	foreach ($res as $data){
    		$day = date($key_day,strtotime($data['passtime']));
    		$datas['data_day'][$day]["realuser"]++;
    		$promocode = $promocodes[$data["uid"]];
    		$datas['data_promocode'][$day][$promocode]["realuser"]++;
    		
    		$day = date($key_hours,strtotime($data['passtime']));
    		$datas['data_hours'][$day]["realuser"]++;
    		$promocode = $promocodes[$data["uid"]];
    		$datas['data_hours_promocode'][$day][$promocode]["realuser"]++;
    	}
    	return $datas;
    }
	
    static function getRecharges($stime, $etime, $promocodes, &$datas)
	{
		$where = [];
        $where['uid'] = ['in', array_keys($promocodes)];
        $where['first_recharge_time'] = ['gt', 0];
        $firstRecharge = D("Userstatic")->where($where)->getField("uid,first_recharge_time");
        
        $dao = new \Common\Model\HPFMonthModel("rechargeRecord", $stime);
        $where = [];
        $where['uid'] = ['in', array_keys($promocodes)];
        $where['ctime'] = [['egt',$stime],['lt',$etime]];
		$where['status'] = 100;
        $res = $dao->field("ctime,rmb,uid")->where($where)->select();
//echo $dao->getLastSql(); 
		$key_day = self::getDatakey('day');
    	$key_hours = self::getDatakey('hours');
		if(!$res) {
			return $datas;
		}
        foreach ($res as $data){	
			$day = date($key_day, strtotime($data['ctime']));
			$datas['data_day'][$day]['count_rmb'] += $data["rmb"];
			$datas['data_day'][$day]['count_num']++;
			if(isset($firstRecharge[$data['uid']]) && $data['ctime'] == $firstRecharge[$data['uid']]){
				$datas['data_day'][$day]["count_rmb_new"] += $data["rmb"];
				$datas['data_day'][$day]["count_num_new"] ++;
			}
			
			$promocode = $promocodes[$data["uid"]];
			$datas['data_promocode'][$day][$promocode]['count_rmb'] += $data["rmb"];
			$datas['data_promocode'][$day][$promocode]["count_num"]++;
			if(isset($firstRecharge[$data['uid']]) && $data['ctime'] == $firstRecharge[$data['uid']]){
				$datas['data_promocode'][$day][$promocode]["count_rmb_new"] += $data["rmb"];
				$datas['data_promocode'][$day][$promocode]["count_num_new"] ++;
			}
			
			
			$day = date($key_hours, strtotime($data['ctime']));
			$datas['data_hours'][$day]['count_rmb'] += $data["rmb"];
			$datas['data_hours'][$day]['count_num']++;
			if(isset($firstRecharge[$data['uid']]) && $data['ctime'] == $firstRecharge[$data['uid']]){
				$datas['data_hours'][$day]["count_rmb_new"] += $data["rmb"];
				$datas['data_hours'][$day]["count_num_new"] ++;
			}
			
			$promocode = $promocodes[$data["uid"]];
			$datas['data_hours_promocode'][$day][$promocode]['count_rmb'] += $data["rmb"];
			$datas['data_hours_promocode'][$day][$promocode]['count_num']++;
			if(isset($firstRecharge[$data['uid']]) && $data['ctime'] == $firstRecharge[$data['uid']]){
				$datas['data_hours_promocode'][$day][$promocode]["count_rmb_new"] += $data["rmb"];
				$datas['data_hours_promocode'][$day][$promocode]["count_num_new"] ++;
			}
        }
        return $datas;
    }
    
    static function getDatakey($index = 'day')
	{
		$arr = [
			'hours' => 'Y-m-d H',
			'day' => 'Y-m-d',
			'week' => 'Y-W',
			'month' => 'Y-m'
			];
    	return $arr[$index];
    }
   
}
