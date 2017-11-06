<?php
namespace Company\Controller;

class PromotionController extends BaseController{

    protected $pageSize = 20;

    protected function _access(){
        return [
			'anchorstatistics' => ['index'],
            'checkapply' => ['apply'],
        ];
    }
    
    function __construct()
    {
        parent::__construct();
        $where['uid'] = \HP\Op\Admin::getUid();
        $user = D('AclUser')->where($where)->find();
        if($user['promocode']) {
        	$this->promocode = $user['promocode'];
        } else {
        	$this->promocode = I('get.promocode', ''); 
        	if($this->promocode) {
        		cookie('op_promocode', $this->promocode);
        	}
        	if(!$this->promocode && cookie('op_promocode')) {
        		$this->promocode = cookie('op_promocode');
        	}
        }
        if(!$this->promocode) {
        	exit('您还没有推广码，请联系技术人员');
        }
        //$uids = $dao->where(['promocode'=>$this->promocode])->getField('uid,channel');
    }

   /**  
    * 推广主播列表
    */
    public function index()
    {
    	$dao = D('channelUser');
        $userstaticDao = D('userstatic');
        
        $where['a.promocode'] = $this->promocode;
        if($uid = I('get.uid')){
            $where['a.uid'] = $uid;
        }
        if($nick = I('get.nick')){
            $where['b.nick'] = ['like',"%$nick%"];
        }
        if($startTime = I('get.timestart')) {
        	$where['b.rtime'][] = ['egt', $startTime . ' 00:00:00'];
        }
		if($endTime = I('get.timeend')) {
			$where['b.rtime'][] = ['lgt', $startTime . ' 23:59:59'];
        }

        if($export = I('get.export')){//导出数据
        	$results = $dao
        	->alias('a')
        	->join(" left join ".$userstaticDao->getTableName()." as b on a.uid = b.uid ")
        	->where($where)
        	->order('uid desc')
        	->field('a.uid,b.nick,b.rtime')
        	->select();
        }else{
        	$count = $dao
        	->alias('a')
        	->join(" left join ".$userstaticDao->getTableName()." as b on a.uid = b.uid ")
        	->where($where)->count();
        	
        	$Page = new \HP\Util\Page($count, $this->pageSize);
        	$results = $dao
	        	->alias('a')
	        	->join(" left join ".$userstaticDao->getTableName()." as b on a.uid = b.uid ")
	        	->where($where)
	        	->limit($Page->firstRow.','.$Page->listRows)
	        	->order('uid desc')
	        	->field('a.uid,b.nick,b.rtime,b.pic')
	        	->select();
        	foreach($results as $k=>$result) {
        		$results[$k]['pic'] = avator($result['pic']);
        	}
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('UID','昵称','注册时间');
            foreach ($results as $data) {
            	$excel[] = array("\t".$data['uid'],"\t".$data['nick'],"\t".$data['rtime']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'主播列表');
        }
        $this->data = $results;
        $this->page = $Page->show();
        $this->display();
    }

}
