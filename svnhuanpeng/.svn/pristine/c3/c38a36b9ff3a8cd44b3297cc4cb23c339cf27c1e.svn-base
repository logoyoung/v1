<?php
/*
 * 计算主播每天的统计数据
 */
namespace Cli\Controller;

class AnchorController extends \Think\Controller
{
	
	public function insertData()
	{
		$date = '2017-08-10';
		do {
			$this->timingIncomeData($date);
			$date = date('Y-m-d', strtotime($date) + 86400);  //增加一天
		}while($date < date('Y-m-d'));
	}
	
	public function timingFansData($date = false)
	{
		if(!$date) {
			$date = date('Y-m-d', strtotime('-1 day'));
		}
		
		$suffix = date('Ym', strtotime($date));
		$Dao_follow = D('userfollow');
		
		$fans = $Dao_follow->where(['tm'=>['elt', $date . ' 23:59:59']])->group('uid2')->getField('uid2,count(*) as count');
		$newfans = $Dao_follow->where(['tm'=>['like', "$date%"]])->group('uid2')->getField('uid2,count(*) as count');
		
		$Dao = D('AnchorStatis');
		
		if($fans) {
			foreach($fans as $k=>$v) {
				$insert['date'] = $date;
				$insert['uid'] = $k;
				$insert['fans'] = $v;
				$insert['newfans'] = isset($newfans[$k]) ? $newfans[$k] : 0;
				
				$sql = 'INSERT INTO ' . $Dao->getTableName()
					. joinInsertSql($insert) . ' ON DUPLICATE KEY UPDATE `fans`="' . $insert['fans'] . '",`newfans`="' . $insert['newfans'] . '"';
				$Dao->execute($sql);
			}
		}
	}
	
	public function timingIncomeData($date = false)
	{
	    if(!$date) {
		  $date = date('Y-m-d', strtotime('-1 day')); 
        }

		$suffix = date('Ym', strtotime($date));
		$Dao_coin = D('giftrecordcoin_' . $suffix);
		$Dao_bean = D('giftrecord_' . $suffix);

		$Dao = D('liveLength');
		$where = ['ctime'=>['like',"$date%"]];
		$coin_result = $Dao_coin->field('luid,sum(income) as coin')->where($where)->group('luid')->select();  //金币
		$bean_result = $Dao_bean->field('luid,sum(income) as bean')->where($where)->group('luid')->select();  //金豆
		
		//echo $Dao_bean->getLastSql() . chr(10);
		if($coin_result) {
		   foreach($coin_result as $k=>$v) {
			   $v['date'] = $date;
			   $v['uid'] = $v['luid'];
			   unset($v['luid']);
			   $sql = 'INSERT INTO ' . $Dao->getTableName() . joinInsertSql($v) .  'ON DUPLICATE KEY UPDATE `coin`="' . $v['coin'] . '"';
			   //echo $sql .chr(10);
			   $Dao->execute($sql);
		   }
		}
		if($bean_result) {
		   foreach($bean_result as $k=>$v) {
			   $v['date'] = $date;
			   $v['uid'] = $v['luid'];
			   unset($v['luid']);
			   $sql = 'INSERT INTO ' . $Dao->getTableName()	. joinInsertSql($v) . ' ON DUPLICATE KEY UPDATE `bean`="' . $v['bean'] . '"';
			   //echo $sql .chr(10);
			   $Dao->execute($sql);
		   }
		}
        $result = $Dao->field('distinct(uid) as uid')->select();
        if($result) {
            $Dao_anchor = D('anchor');
            $anchor = $Dao_anchor->where(['uid'=>['in', array_column($result, 'uid')]])->getField('uid,cid');
            foreach($result as $k=>$v) {
                $where = ['uid'=>$v['uid']];
                $data = ['cid'=>$anchor[$v['uid']]];
                $Dao->where($where)->save($data);
            }
        }
        /****************代码分界线**********上面可删除*********先测试几天*******2017-08-14*/
        if(!$date) {
        	$date = date('Y-m-d', strtotime('-1 day'));
        }
        
        $suffix = date('Ym', strtotime($date));
        $Dao_coin = D('giftrecordcoin_' . $suffix);
        $Dao_bean = D('giftrecord_' . $suffix);
        
        $Dao = D('AnchorStatis');
        $where = ['ctime'=>['like',"$date%"]];
        $coin_result = $Dao_coin->field('luid,sum(income) as coin')->where($where)->group('luid')->select();  //金币
        $bean_result = $Dao_bean->field('luid,sum(income) as bean')->where($where)->group('luid')->select();  //金豆
        
        //echo $Dao_bean->getLastSql() . chr(10);
        if($coin_result) {
        	foreach($coin_result as $k=>$v) {
        		$v['date'] = $date;
        		$v['uid'] = $v['luid'];
        		unset($v['luid']);
        		$sql = 'INSERT INTO ' . $Dao->getTableName() . joinInsertSql($v) .  'ON DUPLICATE KEY UPDATE `coin`="' . $v['coin'] . '"';
        		//echo $sql .chr(10);
        		$Dao->execute($sql);
        	}
        }
        if($bean_result) {
        	foreach($bean_result as $k=>$v) {
        		$v['date'] = $date;
        		$v['uid'] = $v['luid'];
        		unset($v['luid']);
        		$sql = 'INSERT INTO ' . $Dao->getTableName() . joinInsertSql($v) . ' ON DUPLICATE KEY UPDATE `bean`="' . $v['bean'] . '"';
        		//echo $sql .chr(10);
        		$Dao->execute($sql);
        	}
        }
        $result = $Dao->field('distinct(uid) as uid')->select();
        if($result) {
        	$Dao_anchor = D('anchor');
        	$anchor = $Dao_anchor->where(['uid'=>['in', array_column($result, 'uid')]])->getField('uid,cid');
        	foreach($result as $k=>$v) {
        		$where = ['uid'=>$v['uid']];
        		$data = ['cid'=>$anchor[$v['uid']]];
        		$Dao->where($where)->save($data);
        	}
        }
        
		$this->timingFansData();
	}
	
    /**
	 * 计算每个用户每天的直播时长，跨天直播按照24点分界
	 */
	function insertLiveLengthData($date = false)
	{
		$dao_live = D("Live");
		$dao_length = D("liveLength");  
		$Dao_statis = D('AnchorStatis');  //新增
        if(I('get.today') == 1) {
            $date = date('Y-m-d'); 
            $stime = $date . ' 00:00:00';
            $etime = date('Y-m-d H:i:s');
        } else {
            if(!$date) {
                $date = date('Y-m-d', strtotime('-1 day')); 
            }
            $stime = $date . ' 00:00:00';
            $etime = $date . ' 23:59:59';
        }
        if($uid = I('get.uid',false)) {
            $where['uid'] = $uid;
        }
		
		$where['stime'] = [['egt', '2017-06-18 00:00:00'], ['elt', $etime], 'and'];
		$where['etime'] = [['egt', $stime], ['eq', '0000-00-00 00:00:00'], 'or'];
		$res = $dao_live->field('uid, stime, etime')->where($where)->order('liveid')->select();   //开播数据,基于此数据计算
		$arr = [];
        
        if(!$res) {
            return false;
        }
		foreach($res as $k=>$v) {
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
               if(isset($arr[$v['uid']])) {
                    $arr[$v['uid']] += $num;
               } else {
                    $arr[$v['uid']] = $num;
               }
            }
		}
		if($arr) {
			foreach($arr as $k=>$v) {
				$insertData = [
					'uid' => $k,
					'date' => $date,
					'length' => $v
				];
				$sql = "insert into " . $dao_length->getTableName() . joinInsertSql($insertData) . " on duplicate key update length=$v";
				$dao_length->execute($sql);
				//新增
				$sql = "insert into " . $Dao_statis->getTableName() . joinInsertSql($insertData) . " on duplicate key update length=$v";
				$Dao_statis->execute($sql);
			}
		}
	}

	
	
    public function insertIncomeData()
    {
        $date = '2017-03-01'; 
        do {
            $Dao = D('liveLength');
            $Dao->where(['date'=>$date])->save(['coin'=>0, 'bean'=>0]);
            echo $Dao->getLastSql() . chr(10);
            $this->timingIncomeData($date);
            $date = date('Y-m-d', strtotime($date) + 86400);  //增加一天
        }while($date < date('Y-m-d'));
    }
}
