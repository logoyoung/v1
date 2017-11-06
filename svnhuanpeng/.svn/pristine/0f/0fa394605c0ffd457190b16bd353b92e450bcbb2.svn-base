<?php
namespace Admin\Controller;

class GiftController extends BaseController{

    protected $pageSize = 20;
    public function _access()
    {
        return [
           'giftsave' => ['giftlist'],
        ];
    }
    
    public function giftlist(){
        $dao = D('Gift');
        $siteNotify = $dao->getSiteNotify();
        $getType = $dao->getType();

        $all_site_notify = I('get.all_site_notify', -1);
        if($all_site_notify >= 0){
        	$where['all_site_notify'] = $all_site_notify;
        } else {
        	$_GET['all_site_notify'] = -1;
        }
        if($giftname = I('get.giftname')){
        	$where['giftname'] = ['like',"%$giftname%"];
        }
        
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->where($where)
            ->order('type desc,money desc')
            ->select();
        }else{
            $count = $dao->where($where)->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $dao
	            ->where($where)
	            ->limit($Page->firstRow.','.$Page->listRows)
	            ->order('type desc,money desc')
	            ->select();
        }
        
        foreach ($results as $result){
            $result['all_site_notify'] = $siteNotify[$result['all_site_notify']];
            $result['type'] = $getType[$result['type']];
            $result['poster'] = getPic($result['poster']);
            $result['poster_3x'] = getPic($result['poster_3x']);
            $result['bg'] = getPic($result['bg']);
            $result['bg_3x'] = getPic($result['bg_3x']);
            $result['web_preview'] = getPic($result['web_preview']);
            $result['web_bg'] = getPic($result['web_bg']);
            $result['thumb_poster'] = getPic($result['thumb_poster']);
            $result['thumb_poster_3x'] = getPic($result['thumb_poster_3x']);
            $datas[] = $result;
        }
        /*
        if($export = I('get.export')){//导出数据
            $excel[] = array('游戏ID','名称','类型','描述');
            foreach ($datas as $data) {
                $excel[] = array($data['gameid'],$data['name'],$data['type'],$data['description']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏列表');
        }*/
        $this->data = $datas;
        $this->siteNotify = $siteNotify;
        $this->page = $Page->show();
        $this->display();
    }
    
    public function giftsave(){
        $dao = D('Gift');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        if(IS_POST){
            $message = ['status'=>0,'info'=>'操作失败'];
            if($dao->create()){
                if($id){
                    $res = $dao->where('id='.$id)->save();
                }else{
                	$dao->type = 2;
                    $res = $insertid = $dao->add();
                }
                if(false !==$res){
                	$message = ['status'=>1,'info'=>'操作成功', 'id'=>$insertid];
                }
            }
            return $this->ajaxReturn($message);
        }
        $assign = $id ? $dao->find($id) : [];
        $this->assign($assign);
        $this->siteNotify = $dao->getSiteNotify();
        $this->getType = $dao->getType();
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    
    }
    
    /**
     * 礼物赠送统计
     * 礼物会有变化，所以统计数据为每个礼物每天一条数据， 为了更直观的比较，输出为每天一条数据，每个礼物为一列
     */
    public function giftrecordstatistics()
    {
    	$dao = D('Gift');
    	$gift = $dao->getField('id,giftname');
    	
    	if(!($startTime = I('get.timestart'))) {
    		$_GET['timestart'] = $startTime = date('Y-m-01');
    	}
    	if(!($endTime = I('get.timeend'))) {
    		$_GET['timeend'] = $endTime = date('Y-m-d', strtotime('-1 day'));
    	}
    	$where['date'][] = ['egt', $startTime];
    	$where['date'][] = ['elt', $endTime];
    	$dao = D('GiftRecord');
    	$gifts = $dao->distinct(true)->where($where)->field('giftid')->order('giftid')->select();
    	foreach($gifts as $v) {
    		$allGift[] = $gift[$v['giftid']];
    	}
		
    	if($export = I('get.export')){//导出数据
    		$tmpDate = $dao
    		->where($where)->distinct(true)->order('date desc')
    		->field('date')->select();
    	} else {
    		$count = $dao->where($where)->count('distinct date');
    		$Page = new \HP\Util\Page($count, $this->pageSize);
    		
    		$tmpDate = $dao
    			->where($where)->distinct(true)->order('date desc')
	    		->limit($Page->firstRow.','.$Page->listRows)
	    		->field('date')->select();
    	}
    	$dataArr = array_column($tmpDate, 'date');
    	$record = $dao->where(['date'=>['in', $dataArr ? $dataArr : '']])->order('date desc')->select();
    	
    	$results = [];
    	foreach($tmpDate as $v) {
    		foreach($gifts as $vv) {
    			$results[$v['date']][$vv['giftid']] = ['num'=>0, 'cost'=>0];
    		}
    	}
    	if($record) {
    		foreach($record as $k=>$v) {
    			$results[$v['date']][$v['giftid']] = ['num'=>$v['num'], 'cost'=>(int)$v['cost']];
    		}
    	}
    	
	    $total = $dao->where($where)->field('giftid,sum(num) as num, sum(cost) as cost')->group('giftid')->order('giftid')->select();
	    if($total) {
	    	foreach($total as $k=>$v) {
	    		$total[$k]['cost'] = (int)$v['cost'];
	    	}
	    }
	    
	    $this->total = $total;
    	$this->data = $results;
    	$this->allGift = $allGift;

    	if($export = I('get.export')){//导出数据
    		$content = $this->fetch('exportgiftrecord');
    		//var_dump($content); exit;
    		\HP\Util\Export::outputXml($content, $startTime . '~' . $endTime .'礼物赠送记录');
    	}
    	$this->page = $Page->show();
    	$this->display();
    }
}
