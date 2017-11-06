<?php
// +----------------------------------------------------------------------
// | 统计工具类
// +----------------------------------------------------------------------
// | Author: zwq
// +----------------------------------------------------------------------
namespace HP\Op;
use Org\Util\Date;
class Statis extends \HP\Cache\Proxy{
    
    const MONEY_CODE = 1000;//充值表rmb单位
    const RECHARGE_TYPE_DAY = 1;
    const RECHARGE_TYPE_CHANNEL = 2;
    const RECHARGE_STATUS_SECCESS = 100;
    const RECHARGE_STATUS_FAILED = 0;
    
    const REGISTER_TYPE_DAY = 1;
    const REGISTER_TYPE_DAY_CHANNEL = 2;
    const REGISTER_TYPE_HOURS = 3;
    const REGISTER_TYPE_HOURS_CHANNEL = 4;
    
    /**获取主播时长
     * 2017年5月31日 zwq
     * live表 stime<=结束时间  并且 etime >= 开始时间
     * 
     */
    static function getAnchorLength($stime,$etime){
        $db = D('Live');
        $stime = $stime." 00:00:00";
        $etime = $etime." 23:59:59";
        $res = $db->field("uid,stime,etime")->where("stime>0 and stime <= '$etime' and etime >= '$stime'  ")->order("liveid")->select();
        foreach ($res as $data){
            $data['stime'] < $stime && $data['stime'] = $stime;
            $data['etime'] > $etime && $data['etime'] = $etime;
            $datas[$data['uid']]['data'][] = $data;
            strtotime($data['etime']) > strtotime($data['stime']) && $lengths[$data['uid']] += strtotime($data['etime']) - strtotime($data['stime']);
        }
        return ['lengths'=>$lengths,'data'=>$data];
    }
    
    /**获取注册用户
     * 2017年6月1日  zwq
     * 根据日期聚合
     */
    
    static function getUserByday($stime,$etime,$userchannel=null,&$datas=[]){
        $dao = D("userstatic");
        $key_day = self::getDatakey('day');
        $key_hours = self::getDatakey('hours');
        $res = $dao->field("uid,rtime,phone")->where("  rtime >= '$stime'  and rtime <='$etime' ")->order("uid")->select();
        foreach ($res as $data){
            $day = date($key_day,strtotime($data['rtime']));
            $datas['data_day'][$day]["register"]++;
            $data['phone'] && $datas['data_day'][$day]["phoneuser"]++;
            $channelid = $userchannel[$data["uid"]];
            $data['phone'] && $datas['data_channel'][$day][$channelid]["phoneuser"]++;
            $datas['data_channel'][$day][$channelid]["register"]++;
          
            $day = date($key_hours,strtotime($data['rtime']));
            $datas['data_hours'][$day]["register"]++;
            $data['phone'] && $datas['data_hours'][$day]["phoneuser"]++;
            $channelid = $userchannel[$data["uid"]];
            $data['phone'] && $datas['data_hours_channel'][$day][$channelid]["phoneuser"]++;
            $datas['data_hours_channel'][$day][$channelid]["register"]++;
            }
        return $datas;
    }
    
    /**获取新增注册用户
     * 2017年6月1日  zwq
     * 根据日期聚合
     */
    
    static function getNewUserByday($type,$stime,$etime,$userchannel=null,&$datas=[]){
        $dao = D("userdevice");
        $key = self::getDatakey($type);
        $res = $dao->field("uid,rtime,phone")->where("  rtime >= '$stime'  and rtime <='$etime' ")->order("uid")->select();
        foreach ($res as $data){
            $day = date($key,strtotime($data['rtime']));
            $datas['data_day'][$day]["register"]++;
            $data['phone'] && $datas['data_day'][$day]["phoneuser"]++;
            $channelid = $userchannel[$data["uid"]];
            $data['phone'] && $datas['data_channel'][$day][$channelid]["phoneuser"]++;
            $datas['data_channel'][$day][$channelid]["register"]++;
        }
        return $datas;
    }
    
    /**获取实名认证用户
     * 2017年6月1日  zwq
     * 根据日期聚合
     */
    
    static function getRealUserByday($stime,$etime,$userchannel=null,&$datas=[]){
        $dao = D("userrealname");
        $key_day = self::getDatakey('day');
        $key_hours = self::getDatakey('hours');
        $res = $dao->field("uid,passtime")->where(" status = ".RN_PASS." and  passtime >= '$stime'  and passtime <='$etime' ")->order("passtime")->select();
        foreach ($res as $data){
            $day = date($key_day,strtotime($data['passtime']));
            $datas['data_day'][$day]["realuser"]++;
            $channelid = $userchannel[$data["uid"]];
            $datas['data_channel'][$day][$channelid]["realuser"]++;
            
            $day = date($key_hours,strtotime($data['passtime']));
            $datas['data_hours'][$day]["realuser"]++;
            $channelid = $userchannel[$data["uid"]];
            $datas['data_hours_channel'][$day][$channelid]["realuser"]++;
        }
        return $datas;
    }
    
    /**获取充值数据
     * 2017年6月1日  zwq
     * 根据日期聚合
     */
    
    static function getRechargeByday($type,$stime,$etime,$userchannel=null,&$datas=[]){
        $dao =  D("Userstatic");
        $firstRecharge = $dao->where("first_recharge_time >0")->getField("uid,first_recharge_time");
        $dao = new \Common\Model\HPFMonthModel("rechargeRecord",$stime);
        $key = self::getDatakey($type);
        $res = $dao->field("uid,ctime,rmb")->where(" status = 100 and  ctime >= '$stime'  and ctime <'$etime' ")->order("ctime")->select();
        foreach ($res as $data){
            $day = date($key,strtotime($data['ctime']));
            $datas['data_day'][$day]["recharge"]++;
            $datas['data_day'][$day]["rmb"] += $data['rmb']/self::MONEY_CODE;
            $channelid = $userchannel[$data["uid"]];
            $datas['data_channel'][$day][$channelid]["recharge"]++;
            $datas['data_channel'][$day][$channelid]["rmb"] += $data['rmb']/self::MONEY_CODE;
            
            $firstRechargedate = $firstRecharge[$data["uid"]];//第一次充值时间
            if($firstRechargedate && $data['ctime'] == $firstRechargedate){
                $datas['data_day'][$day]["recharge_new"]++;
                $datas['data_day'][$day]["rmb_new"] += $data['rmb']/self::MONEY_CODE;
                $datas['data_channel'][$day][$channelid]["recharge_new"]++;
                $datas['data_channel'][$day][$channelid]["rmb_new"] += $data['rmb']/self::MONEY_CODE;
            }
        }
        return $datas;
    }
    
    /**获取充值数据
     * 2017年6月13日  zwq
     * 根据日期聚合
     */
    
    static function getRechargeByType($type,$stime,$etime){
        $datas = [];
        $groups = [
            'day'=>"DATE_FORMAT(date,'%Y%m%d')",
            'week'=>"DATE_FORMAT(date,'%Y%u')",
            'month'=>"DATE_FORMAT(date,'%Y%m')",
            
        ];
        $group = $groups[$type];
        if(!$group) return $datas;
        $dao =  D("statisrecharge");
        $where['date'] = [['egt',$stime],['elt',$etime]];
        $where['type'] = self::REGISTER_TYPE_DAY;//日数据
        $where['status'] = self::RECHARGE_STATUS_SECCESS;//充值成功
        $res = $dao
        ->where($where)
        ->field("$group as date,sum(count_num) as recharge,sum(count_user) as recharge_user ,sum(count_rmb) as rmb ,sum(count_num_new) as recharge_new ,sum(count_rmb_new) as rmb_new ")
        ->group($group)
        ->select();
        foreach ($res as $rs){
            $datas[$rs['date']] = $rs;
        }
        return $datas;
    }
    
    /**获取充值数据
     * 2017年6月13日  zwq
     * 根据渠道聚合
     */
    
    static function getRechargeByChannel($type,$stime,$etime){
        $datas = [];
        $groupdates = [
            'day'=>"DATE_FORMAT(date,'%Y%m%d')",
            'week'=>"DATE_FORMAT(date,'%Y%u')",
            'month'=>"DATE_FORMAT(date,'%Y%m')",
            'hours'=>"concat(date,':',hours)",
        ];
        $groups = [
            'day'=>"DATE_FORMAT(date,'%Y%m%d'),channel",
            'week'=>"DATE_FORMAT(date,'%Y%u'),channel",
            'month'=>"DATE_FORMAT(date,'%Y%m'),channel",
            'hours'=>"date,hours,channel",
        ];
        $group = $groups[$type];
        if(!$group) return $datas;
        $dao =  D("statisrecharge");
        $where['date'] = [['egt',$stime],['elt',$etime]];
        $where['type'] = self::RECHARGE_TYPE_DAY;//日数据
        $type=='hours' && $where['type'] = self::RECHARGE_TYPE_CHANNEL;//小时数据
        $where['status'] = self::RECHARGE_STATUS_SECCESS;//充值成功
        $res = $dao
        ->where($where)
        ->field("$groupdates[$type] as date,channel,sum(count_num) as recharge,sum(count_user) as recharge_user ,sum(count_rmb) as rmb ,sum(count_num_new) as recharge_new ,sum(count_rmb_new) as rmb_new ")
        ->group($group)
        ->select();
        foreach ($res as $rs){
            $datas[$rs['date']][$rs['channel']] = $rs;
        }
        return $datas;
    }
    
    /**获取充值数据
     * 2017年8月29日  sjt
     * 根据渠道聚合
     */
    
    static function getRechargeByPromocode($type,$stime,$etime){
    	$datas = [];
    	$groupdates = [
    			'day'=>"DATE_FORMAT(date,'%Y%m%d')",
    			'week'=>"DATE_FORMAT(date,'%Y%u')",
    			'month'=>"DATE_FORMAT(date,'%Y%m')",
    			'hours'=>"concat(date,':',hours)",
    	];
    	$groups = [
    			'day'=>"DATE_FORMAT(date,'%Y%m%d'),promocode",
    			'week'=>"DATE_FORMAT(date,'%Y%u'),promocode",
    			'month'=>"DATE_FORMAT(date,'%Y%m'),promocode",
    			'hours'=>"date,hours,promocode",
    	];
    	$group = $groups[$type];
    	if(!$group) return $datas;
    	$dao =  D("statisrecharge");
    	$where['date'] = [['egt',$stime],['elt',$etime]];
    	$where['type'] = self::RECHARGE_TYPE_DAY;//日数据
    	$type=='hours' && $where['type'] = self::RECHARGE_TYPE_CHANNEL;//小时数据
    	$where['status'] = self::RECHARGE_STATUS_SECCESS;//充值成功
    	$where['promocode'] = ['neq', ''];//不为空
    	$res = $dao
	    	->where($where)
	    	->field("$groupdates[$type] as date,promocode,sum(count_num) as recharge,sum(count_user) as recharge_user ,sum(count_rmb) as rmb ,
					sum(count_num_new) as recharge_new ,sum(count_rmb_new) as rmb_new ")
	    	->group($group)
	    	->select();
    	foreach ($res as $rs){
    		$datas[$rs['date']][$rs['promocode']] = $rs;
    	}
    	return $datas;
    }
    
    
    /**活跃设备
     * 2017年6月6日  zwq
     * 根据日期聚合
     */
    
    static function getUserViewByday($stime,$etime,$userChannel=null,&$datas){
        $dao = D("Userdevice");
        $udischannel = $dao->getField("udid,channel");
        
        $dao =D("Userviewrecord");
        $res = $dao->field("ctime,channel,uid,udid ")->where(" ctime >= '$stime'  and ctime <'$etime' ")->select();
        $key_day = self::getDatakey('day');
        $key_hours = self::getDatakey('hours');
        foreach ($res as $data){
            isset($udischannel[$data['udid']]) && $data["channel"] = $udischannel[$data['udid']];//第一次渠道来源
            $day = date($key_day,strtotime($data['ctime']));
            $datas['data_day'][$day]["userview"][$data['udid']]=1;
            $datas['data_channel'][$day][$data["channel"]]["userview"][$data['udid']]=1;
            
            $day = date($key_hours,strtotime($data['ctime']));
            $datas['data_hours'][$day]["userview"][$data['udid']]=1;
            $datas['data_hours_channel'][$day][$data["channel"]]["userview"][$data['udid']]=1;
        }
        return $datas;
    }
    
    /**新增设备
     * 2017年6月6日  zwq
     * 根据日期聚合
     */
    
    static function getUserDeviceByday($stime,$etime,$userChannel=null,&$datas){
        $dao =D("Userdevice");
        $key_day = self::getDatakey('day');
        $key_hours = self::getDatakey('hours');
        $res = $dao->field("ctime ,channel,udid ")->where(" ctime >= '$stime'  and ctime <'$etime' ")->select();
        foreach ($res as $data){
            $day = date($key_day,strtotime($data['ctime']));
            $datas['data_day'][$day]["userdevice"] ++;
            $datas['data_channel'][$day][$data["channel"]]["userdevice"] ++;
            $day = date($key_hours,strtotime($data['ctime']));
            $datas['data_hours'][$day]["userdevice"] ++;
            $datas['data_hours_channel'][$day][$data["channel"]]["userdevice"] ++;
        }
        return $datas;
    }
    
    
    static function getUserChannels($stime,$etime){
        $dao = D("channelUser");
        $datas = $dao->getField("uid,channel");
        return $datas;
    }
    
	static function getUserPromocodes($stime,$etime){
        $dao = D("channelUser");
        $datas = $dao->where(['promocode'=>['neq', '']])->getField("uid,promocode");
        return $datas;
    }
    
    static function getRechargeStatisByday($type,$stime,$etime){
        $days = get_days($type,$stime,$etime);
        $datas = [];
        $stime = $stime." 00:00:00";
        $etime = date("Y-m-d",strtotime($etime)+86400)." 00:00:00";
        $datas['data_day'] = $days;
        $userChannel = self::getUserChannels($stime, $etime);
        return $datas;
    }
    
    static function getDatakey($type){
        switch ($type) {
            case 'hours':
                $key ="Y-m-d H";
                break;
            case 'day':
                $key ="Y-m-d";
                break;
            case 'week':
                $key = "Y-W";
                break;
            case 'month':
                $key="Y-m";
                break;
            default:
                $key="Y-m-d";
        }
        return $key;
    }
    
    
    static function getRecharges($stime,$etime){
        
        $stime = $stime." 00:00:00";
        $etime = date("Y-m-d",strtotime($etime)+86400)." 00:00:00";
        if(!$stime||!$etime) return;
        $channels = self::getUserChannels($stime, $etime);
        $dao =  D("Userstatic");
        $firstRecharge = $dao->where("first_recharge_time >0")->getField("uid,first_recharge_time");
        $dao = new \Common\Model\HPFMonthModel("rechargeRecord",$stime);
        $where['ctime'] = [['egt',$stime],['lt',$etime]];
        $res = $dao->field("ctime,rmb,status,client,channel,uid")->where($where)->select();
        $keys = [1=>"Ymd",2=>"YmdH"];
        foreach ($res as $rs){
            $rs["channel_user"] = $channels[$rs["uid"]]?$channels[$rs["uid"]]:0;
            foreach ($keys as $type=>$format){
                $key = date($format,strtotime($rs['ctime']))."_".$rs["client"]."_".$rs["channel"]."_".$rs["status"]."_".$rs["channel_user"];
                $datas[$key]["type"] = $type;
                $datas[$key]["date"] = date("Ymd",strtotime($rs["ctime"]));
                if($type==2)$datas[$key]["hours"] = date("H",strtotime($rs["ctime"]));
                $datas[$key]["client"] = $rs["client"];
                $datas[$key]["pay_channel"] = $rs["channel"];
                $datas[$key]["channel"] = $rs["channel_user"];
                $datas[$key]["status"] = $rs["status"];
                $datas[$key]["count_rmb"] += $rs["rmb"];
                $datas[$key]["count_num"] ++;
                $tmp[$key][$rs["uid"]] || $datas[$key]["count_user"]++;
                $tmp[$key][$rs["uid"]] =1;
                $firstRechargedate = $firstRecharge[$rs["uid"]];//第一次充值时间
                if($firstRechargedate && $rs['ctime'] == $firstRechargedate){
                    $datas[$key]["count_rmb_new"] += $rs["rmb"];
                    $datas[$key]["count_num_new"] ++;
                }
            }
        }
        return $datas;
    }
    
}