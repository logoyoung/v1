<?php
namespace Cli\Controller;

class IpController extends \Think\Controller
{

    public function insertData()
    {
        $date = '2017-01-12';
        do {
            $this->timingInsertAddressData($date);
            $date = date('Y-m-d', strtotime($date) + 86400);  //增加一天
        }while($date < date('Y-m-d'));
    }


    public function timingInsertAddressData($date = false)
    {
        if(!$date) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        $DaoStatis = D('statisticsip');
        $DaoStatis->where(['date'=>$date])->delete();

        $Dao = D('ipAddress');
        //截止到当天用户总数
        $totaluser = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>1,'b.rtime'=>['elt',$date . ' 23:59:59']])
            ->group('a.provinceid')
            ->getField('a.provinceid,count(*)');
        $totaluser[35] = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>0,'b.rtime'=>['elt',$date . ' 23:59:59']])
            ->count();
        //当天新注册用户
        $newuser = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>1,'b.rtime'=>[['egt',$date . ' 00:00:00'],['elt',$date . ' 23:59:59']]])
            ->group('a.provinceid')
            ->getField('a.provinceid,count(*)');
        $newuser[35] = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>0,'b.rtime'=>[['egt',$date . ' 00:00:00'],['elt',$date . ' 23:59:59']]])
            ->count();
        //截止到当天充值用户总数
        $totalrecharge = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>1,'b.first_recharge_time'=>[['elt',$date . ' 23:59:59'],['neq','0000-00-00 00:00:00']]])
            ->group('a.provinceid')
            ->getField('a.provinceid,count(*)');
        $totalrecharge[35] = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>0,'b.first_recharge_time'=>[['elt',$date . ' 23:59:59'],['neq','0000-00-00 00:00:00']]])
            ->count();
        //当天新充值用户
        $newrecharge = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>1,'b.first_recharge_time'=>[['egt',$date . ' 00:00:00'],['elt',$date . ' 23:59:59']]])
            ->group('a.provinceid')
            ->getField('a.provinceid,count(*)');
        $newrecharge[35] = $Dao
            ->alias(' a ')
            ->join(" left join userstatic as b on a.longip = b.rip ")
            ->where(['a.countryid'=>0,'b.first_recharge_time'=>[['egt',$date . ' 00:00:00'],['elt',$date . ' 23:59:59']]])
            ->count();

        $suffix = str_replace( '-', '', substr( $date, 0, 7 ) );
        if($suffix >= '201703') {
            //$DaoRecharge = new \Common\Model\HPFMonthModel('hpf_rechargeRecord_' . $suffix);
            $rechargeTable = 'hpf_rechargeRecord_' . $suffix;
            //当天充值额度
            $newmoney = $Dao
                ->alias(' a ')
                ->join(" left join userstatic as b on a.longip = b.rip ")
                ->join(" left join " . $rechargeTable . " as c on b.uid = c.uid ")
                ->where(['a.countryid'=>1,'c.status'=>100,'c.ctime'=>[['egt',$date . ' 00:00:00'],['elt',$date . ' 23:59:59']]])
                ->group('a.provinceid')
                ->getField('a.provinceid,sum(c.rmb) as rmb');
            $other = $Dao
                ->alias(' a ')
                ->join(" left join userstatic as b on a.longip = b.rip ")
                ->join(" left join " . $rechargeTable . " as c on b.uid = c.uid ")
                ->where(['a.countryid'=>0,'c.status'=>100,'c.ctime'=>[['egt',$date . ' 00:00:00'],['elt',$date . ' 23:59:59']]])
                ->field('sum(c.rmb) as rmb')
                ->find();
            $newmoney[35] = $other['rmb'];
            $yesterday = $DaoStatis
                ->where(['date'=>date('Y-m-d', strtotime($date.' 00:00:00') - 86400)])
                ->getField('provinceid,totalmoney');
            echo $DaoStatis->getLastSql() . chr(10);
        }

        $province = $DaoStatis->getProvince();
        foreach($province as $k=>$v) {
            $money = (isset($newmoney[$k]) ? $newmoney[$k] : 0)/1000;
            $totalmoney = isset($yesterday[$k]) ? $yesterday[$k] : 0;
            $data = [
                'provinceid' => $k,
                'province' => $v,
                'date' => $date,
                'totaluser' => isset($totaluser[$k]) ? $totaluser[$k] : 0,
                'newuser' => isset($newuser[$k]) ? $newuser[$k] : 0,
                'totalrecharge' => isset($totalrecharge[$k]) ? $totalrecharge[$k] : 0,
                'newrecharge' => isset($newrecharge[$k]) ? $newrecharge[$k] : 0,
                'totalmoney' => $totalmoney + $money,
                'newmoney' => $money
            ];
            $DaoStatis->add($data);
            //echo time() . $DaoStatis->getLastSql() . chr(10);
        }
    }


    public function timingInsert()
    {
        $where = [];
        $where['rip'] = ['neq',0];
        $where['rtime'] = ['egt', date('Y-m-d 00:00:00', strtotime('-1 day'))];
        $userDao = D('userstatic');
        $ips = $userDao->distinct(true)->where($where)->field('rip')->order('uid')->select();

        if($ips) {
            $dao = D('ipAddress');
            $province = array_flip(D('statisticsip')->getProvince());
            foreach($ips as $k=>$v) {
                $res = $dao->where(['longip'=>$v['rip']])->find();
                if(!$res) {
                    $arr = json_decode(get_ip_address($v['rip']), true);
                    if ($arr['ret'] == 1) {
                        $data = $arr['data'];
                        unset($arr['ret']);
                        $arr['longip'] = $v['rip'];
                        $arr['ip'] = long2ip($v['rip']);
                        $arr['countryid'] = ($arr['country'] == '中国') ? 1 : 0;
                        $arr['provinceid'] = isset($province[$arr['province']]) ? $province[$arr['province']] : 0;
                        //如果是php是32位的这里会出错 int长度不够
                        $arr['rtime'] = $userDao->field('min(rtime) as rtime')->where(['rip'=>$v['rip']])->find()['rtime'];
                        $dao->add($arr);
                        echo time() . $dao->getLastSql() . chr(10);
                    }
                }
            }
        }
        $this->timingInsertAddressData();
    }

    public function update()
    {
        $dao = D('ipAddress');
        $province = array_flip(D('statisticsip')->getProvince());
        $res = $dao->where(['provinceid'=>0, 'country'=>'中国'])->limit('0,10000')->select();
        foreach($res as $k=>$v) {
            $save['countryid'] = ($v['country'] == '中国') ? 1 : 0;
            $save['provinceid'] = isset($province[$v['province']]) ? $province[$v['province']] : 0;
            $dao->where(['id'=>$v['id']])->save($save);
            echo 1 . chr(10);
        }
    }



    public function getip()
    {
        $userDao = D('userstatic');
        $ips = $userDao->distinct(true)->where(['rip'=>['neq',0]])->field('rip')->order('uid')->select();

        if($ips) {
            $dao = D('ipAddress');
            $province = array_flip(D('statisticsip')->getProvince());
            foreach($ips as $k=>$v) {
                $res = $dao->where(['longip'=>$v['rip']])->find();
                if(!$res) {
                    $arr = json_decode(get_ip_address($v['rip']), true);
                    if ($arr['ret'] == 1) {
                        $data = $arr['data'];
                        unset($arr['ret']);
                        $arr['longip'] = $v['rip'];
                        $arr['ip'] = long2ip($v['rip']);
                        $arr['countryid'] = ($arr['country'] == '中国') ? 1 : 2;
                        $arr['provinceid'] = isset($province[$arr['province']]) ? $province[$arr['province']] : 0;
                        //如果是php是32位的这里会出错 int长度不够
                        $arr['rtime'] = $userDao->field('min(rtime) as rtime')->where(['rip'=>$v['rip']])->find()['rtime'];
                        $dao->add($arr);
                        echo time() . $dao->getLastSql() . chr(10);
                    }
                }
            }
        }
    }

}