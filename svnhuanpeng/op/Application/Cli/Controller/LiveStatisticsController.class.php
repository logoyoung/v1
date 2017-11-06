<?php
/*
 * 读取数据库直播数据进行统计
 */
namespace Cli\Controller;

class LiveStatisticsController extends \Think\Controller
{

	public function insertLiveData()
	{	   
		$date = '2017-03-01'; 
		do {
		   $this->timingLiveData($date);
		   $date = date('Y-m-d', strtotime($date) + 86400);  //增加一天
		}while($date < date('Y-m-d'));
	}
	
   /**
    * 统计每天的直播数据，开播量 开播人数 游戏开播量 游戏开播人数
    */ 
   public function timingLiveData($date = false)
   {
		$dao_live = D("Live");
		$dao_s_live = D("Statisticslive");
		if(!$date) {
			$date = date('Y-m-d', strtotime('-1 day'));
		}
	   
		$live = array();
		$live['date'] = date('Ymd', strtotime($date));
		$where['stime'] = ['like',"$date%"];
		$live_info = $dao_live->field('liveid,gameid,stime,uid')->where($where)->order('liveid')->select();   //开播数据,基于此数据计算

		if(!$live_info) { //没有数据插入0，后台列表显示不能断
			$live['livecount'] = 0;
			$live['liveusercount'] = 0;
			$live['troughtime'] = $live['date'] . '00';
			$live['peaktime'] = $live['date'] . '00';
			$live['type'] = 1;
			$dao_s_live->add($live, array(), true);	
			exit('没有数据');
		}
	   
		$live['livecount'] = count($live_info);  //开播次数
		$uids = array_unique(array_column($live_info, 'uid'));
		$live['liveusercount'] = count($uids); //开播人次	
		
		$channelUser = D('channelUser')->where(['uid'=>['in', $uids]])->getField('uid,channel');

		$timeInterval = 2;  //2小时为一个时间段
		$game = $interval = $gameInterval = $channel = [];
	   
		foreach($live_info as $k=>$v) {
			//计算渠道开播量
			if(isset($channel[$channelUser[$v['uid']]])) {
				$channel[$channelUser[$v['uid']]]['count'] += 1;
			} else {
				$channel[$channelUser[$v['uid']]]['count'] = 1;
			}
			$channel[$channelUser[$v['uid']]]['uid'][] = $v['uid'];

			//按时间段计算所有游戏开播数量
			$i = floor(date('H', strtotime($v['stime']))/$timeInterval);
			$index = date('YmdH', (strtotime($date) + $timeInterval * $i * 3600));
			if(isset($interval[$index])) {
				$interval[$index] += 1;
			} else {
				$interval[$index] = 1;
			}
			
			//计算所有游戏开播数量
			$game[$v['gameid']][] = $v['uid'];
			
			//按时间段计算单个游戏开播数量
			if(isset($gameInterval[$v['gameid']][$index])) {
				$gameInterval[$v['gameid']][$index] += 1;
			} else {
				$gameInterval[$v['gameid']][$index] = 1;
			}
		}
       
		$live['troughcount'] = min($interval);
		$live['troughtime'] = array_search($live['troughcount'], $interval);
		$live['peakcount'] = max($interval);
		$live['peaktime'] = array_search($live['peakcount'], $interval);
		$live['type'] = 1;
		$dao_s_live->add($live, array(), true);	  
	    
		
		//按渠道统计
		foreach($channel as $k=>$v) {
			$data = [];
			$data['date'] = $live['date'];
			$data['channel'] = $k;
			$data['livecount'] = $v['count'];
			$data['type'] = 5;
			$data['liveusercount'] = count(array_unique($v['uid']));
			$dao_s_live->add($data, array(), true);
			
			echo $dao_s_live->getLastSql() . chr(10);
		}

		//按时间段统计  
		foreach($interval as $k=>$v) {
			$data = [];
			$data['date'] = $k;
			$data['livecount'] = $v;
			$data['type'] = 3;
			$dao_s_live->add($data, array(), true);	
		}	
		
		//按游戏统计
		foreach($game as $k=>$v) {
			$data = [];
			$data['gameid'] = $k;
			$data['livecount'] = count($v);
			$data['liveusercount'] = count(array_unique($v)); //开播人次
			$data['date'] = $live['date'];
			$data['type'] = 2;
			$data['troughcount'] = min($gameInterval[$k]);
			$data['troughtime'] = array_search($data['troughcount'], $gameInterval[$k]);
			$data['peakcount'] = max($gameInterval[$k]);
			$data['peaktime'] = array_search($data['peakcount'], $gameInterval[$k]);
			$dao_s_live->add($data, array(), true);
			//echo $dao_s_live->getLastSql() . chr(10);	
		}
              
		$this->timingInsertStLiveData($date);
		$this->timingEveryDayLengthData($date);
   }
    
    /**
     * 计算某天某时刻同时直播数量，时间差距是五分钟
     */
    function timingInsertStLiveData($date = false)
    {
        
        $dao_live = D("Live");
        $dao_s_live = D("Statisticslive");
    	if(!$date) {
    		$date = date('Y-m-d', strtotime('-1 day'));
    	}
    	$stime = $date . ' 00:00:00';
    	$etime = $date . ' 23:59:59';
    	
    	$sql = "SELECT `liveid`,`gameid`,`stime`,`etime` FROM `live` WHERE
        		`stime` <> '0000-00-00 00:00:00'
				AND `stime` <= '" . $etime . "'
				AND ( `etime` >= '" . $stime . "' OR (`etime` = '0000-00-00 00:00:00' and status=100))
				ORDER BY liveid";
    	$live_info = $dao_live->query($sql);  //开播数据,基于此数据计算
        //echo $dao_live->getLastSql() . chr(10);	
    	if(!$live_info) {
    	   return;
    	} 
    	$num = [];  
    	$timeInterval = 2;  //2小时为一个时间段
    	$timeEvery = 300;   //每5分钟统计一次
    	$i = 0;
    	$dateTime = strtotime($stime);
    	while($i < 86400) {
			$tmp = date('YmdHis', $dateTime + $i); 
			$num[$tmp] = 0;
			$i += $timeEvery;
    	}
    	foreach($live_info as $k=>$v) {
    		$stime = date('YmdHis', strtotime($v['stime'])); 
    		$etime = date('YmdHis', strtotime($v['etime'])); 
    		foreach($num as $time=>$count) {
    			if($stime <= $time && $etime >= $time) {
    				$num[$time] += 1;
    			}
    		}	
    	}
    	$live['date'] = date('Ymd', strtotime($date));
    	$live['sttroughcount'] = min($num);
    	$live['stpeakcount'] = max($num);
    	$live['sttroughtime'] = strtotime(array_search($live['sttroughcount'], $num));
    	$live['stpeaktime'] = strtotime(array_search($live['stpeakcount'], $num));
    	$live['type'] = 1;
    	$sql = 'INSERT INTO ' . $dao_s_live->getTableName() 
				. joinInsertSql($live) . ' ON DUPLICATE KEY UPDATE `sttroughcount`=' . $live['sttroughcount'] 
				. ',`stpeakcount`=' . $live['stpeakcount']
				. ',`sttroughtime`=' . $live['sttroughtime']
				. ',`stpeaktime`=' . $live['stpeaktime'];
    	$dao_s_live->execute($sql);
        
        //var_dump($num);
    	$arr = array_chunk($num, 3600*$timeInterval/$timeEvery, true);
    	foreach($arr as $k=>$v) {
			$data = [];
			$data['date'] = date('YmdH', strtotime(array_search(current($v), $v)));
			$data['sttroughcount'] = min($v);
			$data['stpeakcount'] = max($v);
			$data['sttroughtime'] = strtotime(array_search($data['sttroughcount'], $v));
			$data['stpeaktime'] = strtotime(array_search($data['stpeakcount'], $v));
			$data['type'] = 4;
			$dao_s_live->add($data, array(), true);
    	}
    }
    
   	/**
	 * 计算每天的直播总时长，跨天直播按照24点分界
	 */
	function timingEveryDayLengthData($date = false)
	{
		$dao_live = D("Live");
		     
        if(!$date) {
            $date = date('Y-m-d', strtotime('-1 day')); 
        }
        $stime = $date . ' 00:00:00';
        $etime = $date . ' 23:59:59';
        
        $sql = "SELECT `liveid`,`gameid`,`stime`,`etime` FROM `live` WHERE
        		`stime` <> '0000-00-00 00:00:00' 
				AND `stime` <= '" . $etime . "' 
				AND ( `etime` >= '" . $stime . "' OR (`etime` = '0000-00-00 00:00:00' and status=100)) 
				ORDER BY liveid";
        $live_info = $dao_live->query($sql);  //开播数据,基于此数据计算
        //echo $dao_live->getLastSql() . chr(10);	
        if(!$live_info) {
            return false;
        }
        $length = 0;
        $game = [];
        foreach($live_info as $k=>$v) {
			if($v['stime'] < $stime) {  
				$start = strtotime($stime);//如果开始时间早于统计的这一天，开始时间为当天0点
			} else {
				$start = strtotime($v['stime']);  //直播开始时间
			}
			if($v['etime'] == '0000-00-00 00:00:00' || $v['etime'] > $etime) {
				$end = strtotime($etime); //如果不是当天，结束时间为24点或当前时间
			} else {
				$end = strtotime($v['etime']);  //直播结束时间
			}
			$num = $end - $start;
            if($num > 0) {
			   $length += $num;
               if(isset($game[$v['gameid']])) {
                    $game[$v['gameid']] += $num;
               } else {
                    $game[$v['gameid']] = $num;
               }
            }
        }
        $dao_s_live = D("Statisticslive"); 
        $date = date('Ymd', strtotime($date));
        $where = [
            'type' => 1,
            'date' => $date
            ];
        $dao_s_live->where($where)->save(['length' => $length]);
		if($game) {
			foreach($game as $k=>$v) {
				$where = [
					'date' => $date,
					'type' => 2,
					'gameid' => $k
				];
				$dao_s_live->where($where)->save(['length' => $v]);
			}
		}
	}

}
