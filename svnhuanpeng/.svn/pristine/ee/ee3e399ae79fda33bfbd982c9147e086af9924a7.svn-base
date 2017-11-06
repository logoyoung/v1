<?php

namespace Company\Controller;
class WagesController extends BaseController{

    protected $pageSize = 10;

    protected function _access(){
        return [
        ];
    }
    
    function __construct()
    {
        parent::__construct();

        $where['uid'] = \HP\Op\Admin::getUid();
        $user = D('AclUser')->where($where)->find();
        if($user['companyid']) {
        	$this->companyId = $user['companyid'];
        } else {
        	$this->companyId = I('get.cid', 0); 
        	if($this->companyId) {
        		cookie('op_company_id', $this->companyId);
        	}
        	if(!$this->companyId && cookie('op_company_id')) {
        		$this->companyId = cookie('op_company_id');
        	}
        }
        if(!$this->companyId) {
        	exit('您不是管理人员，请联系技术人员');
        }
        $this->company = \HP\Op\Company::getCompangInfo()[$this->companyId];//公司名称
    }

   	public function wageslist()
	{
        $export = I('get.export', 0);
        if($export == 2) {
            $this->exportstatement(); exit;
        }
        $dao = D('admin_wages');
        $isblack = \HP\Op\Wages::IS_BLACK;

		$where['company_id'] = $this->companyId;
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        $where["status_op"] = 1; //只能查看审核成功的
        if($uid = I('get.uid')){
            $uid = explode(" ",$uid);
            $where["uid"] = ['in',$uid];
        }

        $where["month"] = $month;
        
        $order = "live_day desc";
		
        if($export == 1){//导出数据
            $datas = $dao->where($where)->order($order)->select();
        }else{
            $count = $dao->where($where)->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        
        foreach ($datas as &$data){
            $data['wages_base_afttax'] = $data['wages_sum'] = $data['wages_base_edit']+$data['wages_gift_edit']+$data['wages_bonuses'];//总收入
            $data['status_op_str'] = $statusop[$data['status_op']];
            $data['is_black'] = $isblack[$data['is_black']];
            $data['live_length'] = secondFormatH($data['live_length']);
        }
        
        if($export == 1){//导出数据
            $excel[] = array('月份,uid,昵称,姓名,首次签约日期,是否禁播,有效时长,有效天数,礼物收益,底薪,奖励,总收入');
            foreach ($datas as $d) {
                $excel[] = array("\t".$d['month'],$d['uid'],$d['nick'],$d['realname'],"\t".$d['live_start'],$d['is_black'],"\t".$d['live_length'],$d['live_day'],$d['wages_gift_edit'],$d['wages_base_edit'],$d['wages_bonuses'],$d['wages_sum']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'公司收入统计表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->display();
    }

    function exportstatement()
    {
        if(!($month = I('get.month'))) {
            $month = date('Y-m',strtotime(date('-1 month')));
        }

        $where['company_id'] = $this->companyId;
        $where["month"] = $month;
        $wages = D('adminWages')->field('sum(wages_base_edit) as bedit,sum(wages_gift_edit) as gedit')->where($where)->find();
        $companyInfo = D('company')->where(['id'=>$this->companyId])->find();
        if(!$wages || !$companyInfo) {
            exit('没有找到记录');
        }

        $firstDay = date('Y.m.d', strtotime($month . '-01'));
        $lastDay = date('m.d', strtotime($month . '-01' ." +1 month -1 day"));
        $this->date = $firstDay . '-' . $lastDay;

        $this->title = '北京六间房科技有限公司霍城分公司';
        $this->aCompany = [
                'name' => '北京六间房科技有限公司霍城分公司',
                'contacter' => '王云',
                'phone' => '010-88516686',
                'address' => '北京市海淀区首体南路9号主语国际5号楼8层'
            ];
        $this->bCompany = [
            'name' => $companyInfo['name'],
            'contacter' => $companyInfo['ownername'],
            'phone' => $companyInfo['phone'],
            'address' => $companyInfo['address'],
            'rate' => $companyInfo['rate'] ? $companyInfo['rate']/100 : BASE_RATE/100,
            'wage' => number_format((int)$wages['bedit'], 2),
            'gift' => number_format((int)$wages['gedit'], 2),
            'total' => number_format((int)$wages['bedit'] + (int)$wages['gedit'], 2),
            'chineseTotal' => numToRmb($wages['bedit'] + $wages['gedit']),
            'bankaddress' => $companyInfo['bankaddress'],
            'cardid' => $companyInfo['cardid'],
            'bank' => D('BankBackend')->where(['id'=>$this->$companyInfo['bankid']])->find()['name'],
        ];

        $content = $this->fetch('statement');
        \HP\Util\Export::outputXml($content, $month . '对账单');
    }
}
