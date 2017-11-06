<?php

namespace Admin\Controller;

class IpController extends BaseController{

    protected $pageSize = 10;
    protected function _access(){
        return [
            'usermap' => ['user'],
            'trend' => ['user'],
        ];
    }

   /**
    * 查询列表
    */
    public function user()
    {
        if($provinceid = I('get.provinceid')){
            $where['provinceid'] = $provinceid;
        }
        if(!$startTime = I('get.timestart')) {
            $_GET['timestart'] = $startTime = date('Y-m-01', strtotime('-1 day'));
        }
		if(!$endTime = I('get.timeend')) {
            $_GET['timeend'] = $endTime = date('Y-m-d', strtotime('-1 day'));
        }
		$where['date'][] = ['egt', $startTime];
		$where['date'][] = ['elt', $endTime];

        $Dao = D('statisticsip');
        $this->exportMax = 5000;
        if(I('get.export')){//导出数据
        	$results = $Dao
        		->field('*')
        		->where($where)
        		->order('date desc,totaluser desc')
        		->limit(0, $this->exportMax)
        		->select();
        } else {
			$this->pageSize = 35;
            $count = $Dao->where($where)->count();
            $Page = new \HP\Util\Page($count, $this->pageSize);
            $results = $Dao
        		->field('*')
        		->where($where)
        		->order('date desc,totaluser desc')
                ->limit($Page->firstRow, $Page->listRows)
                ->select();
        }

        if($export = I('get.export')){//导出数据
            $excel[] = array('日期','省份','注册总用户','当天新注册用户','充值用户总数','当天新充值用户数','充值总金额','当天充值金额');
            foreach ($results as $data) {
            	$excel[] = array("\t".$data['date'],$data['province'],$data['totaluser'],$data['newuser'],$data['totalrecharge'],$data['newrecharge'],$data['totalmoney'],$data['newmoney']);
            }
            \HP\Util\Export::outputCsv($excel, '地域分布列表');
        }

        $this->data = $results;
        $this->page = $Page->show();
        $this->province = $Dao->getProvince();
        $this->display();
    }

    function usermap()
    {
        if(!$startTime = I('get.timestart')) {
            $_GET['timestart'] = $startTime = date('Y-m-01', strtotime('-1 day'));
        }
        if(!$endTime = I('get.timeend')) {
            $_GET['timeend'] = $endTime = date('Y-m-d', strtotime('-1 day'));
        }
        $where['date'][] = ['egt', $startTime];
        $where['date'][] = ['elt', $endTime];

        $Dao = D('statisticsip');
        $results = $Dao
            ->field('province,provinceid,max(totaluser) as totaluser,sum(newuser) as newuser,max(totalrecharge) as totalrecharge,
                sum(newrecharge) as newrecharge,max(totalmoney) as totalmoney,sum(newmoney) as newmoney')
            ->where($where)
            ->group('provinceid')
            ->order('totaluser desc')
            ->select();

        $totaluser = $newuser = $totalrecharge = $newrecharge = $totalmoney = $newmoney = ['data'=>[],'max'=>0];
        if($results) {
            foreach($results as $k=>$v) {
                if($v['provinceid'] < 35) {
                    $totaluser['data'][] = ['name' => $v['province'], 'value'=>$v['totaluser']];
                    $totaluser['max'] = ($totaluser['max'] > $v['totaluser']) ? $totaluser['max'] : $v['totaluser'];

                    $newuser['data'][] = ['name' => $v['province'], 'value'=>$v['newuser']];
                    $newuser['max'] = ($newuser['max'] > $v['newuser']) ? $newuser['max'] : $v['newuser'];

                    $totalrecharge['data'][] = ['name' => $v['province'], 'value'=>$v['totalrecharge']];
                    $totalrecharge['max'] = ($totalrecharge['max'] > $v['totalrecharge']) ? $totalrecharge['max'] : $v['totalrecharge'];

                    $newrecharge['data'][] = ['name' => $v['province'], 'value'=>$v['newrecharge']];
                    $newrecharge['max'] = ($newrecharge['max'] > $v['newrecharge']) ? $newrecharge['max'] : $v['newrecharge'];

                    $totalmoney['data'][] = ['name' => $v['province'], 'value'=>$v['totalmoney']];
                    $totalmoney['max'] = ($totalmoney['max'] > $v['totalmoney']) ? $totalmoney['max'] : $v['totalmoney'];

                    $newmoney['data'][] = ['name' => $v['province'], 'value'=>$v['newmoney']];
                    $newmoney['max'] = ($newmoney['max'] > $v['newmoney']) ? $newmoney['max'] : $v['newmoney'];
                }
            }
        }
        $this->date = $startTime . ' 00:00:00' . '到' . $endTime . ' 23:59:59';

        $totaluser['data'] = json_encode($totaluser['data']);
        $this->totaluser = $totaluser;

        $newuser['data'] = json_encode($newuser['data']);
        $this->newuser = $newuser;

        $totalrecharge['data'] = json_encode($totalrecharge['data']);
        $this->totalrecharge = $totalrecharge;

        $newrecharge['data'] = json_encode($newrecharge['data']);
        $this->newrecharge = $newrecharge;

        $totalmoney['data'] = json_encode($totalmoney['data']);
        $this->totalmoney = $totalmoney;

        $newmoney['data'] = json_encode($newmoney['data']);
        $this->newmoney = $newmoney;

        $this->display();
    }

    /**
     * 查询列表
     */
    public function trend()
    {
        if(!$provinceid = I('get.provinceid')){
            $_GET['provinceid'] = $provinceid = 0;
        }
        if(!$startTime = I('get.timestart')) {
            $_GET['timestart'] = $startTime = date('Y-m-d', strtotime('-1 day'));
        }
        if(!$endTime = I('get.timeend')) {
            $_GET['timeend'] = $endTime = date('Y-m-d', strtotime('-1 day'));
        }

        $where['date'][] = ['egt', $startTime];
        $where['date'][] = ['elt', $endTime];

        $Dao = D('statisticsip');
        $province = $Dao->getProvince();
        $this->province = $province;
        if($provinceid == 0) {
            $this->provincename = '全国';
            $results = $Dao->field('date,sum(totaluser) as totaluser,sum(newuser) as newuser,sum(totalrecharge) as totalrecharge,
                    sum(newrecharge) as newrecharge,sum(totalmoney) as totalmoney,sum(newmoney) as newmoney')
                ->where($where)
                ->group('date')
                ->order('date')
                ->select();
        } else {
            $this->provincename = $province[$provinceid];
            $where['provinceid'] = $provinceid;
            $results = $Dao->field('*')->where($where)->order('date')->select();
        }


        $date = array_column($results, 'date');
        array_walk($date, 'ymd2md');
        $this->date = '"' . implode('","', $date) . '"';
        $this->totaluser = implode(',', array_column($results, 'totaluser'));
        $this->newuser = implode(',', array_column($results, 'newuser'));
        $this->totalrecharge = implode(',', array_column($results, 'totalrecharge'));
        $this->newrecharge = implode(',', array_column($results, 'newrecharge'));
        $this->totalmoney = implode(',', array_column($results, 'totalmoney'));
        $this->newmoney = implode(',', array_column($results, 'newmoney'));


        $this->display();
    }
}
