<?php
//工资计算
namespace Admin\Controller;
use HP\Op\Admin;
use HP\Op\Wages;
use HP\Op\Company;
class WagesController extends BaseController
{
	protected $pageSize = 10;
	protected $type_finance_lock=1;//锁定
	protected $type_finance_unlock=0;//解锁
	protected $tpl ='';
	protected function _access(){
		return [
			'anchorlist'=>['anchorlist'],
			'anchorlist2'=>['anchorlist2'],
			'companyanchorlist'=>['companyanchorlist'],
			'companyanchorlist2'=>['companyanchorlist2'],
			'anchorcheck'=>self::ACCESS_LOGIN,
			'log'=>self::ACCESS_LOGIN,
			'companylist'=>['companylist'],
			'companylist2'=>['companylist2'],
            'anchorbank'=>['anchorlist'],
			'companyanchorcheck'=>['companylist'],
			'companywithdrawpass'=>['withdrawcompany'],
			'withdrawanchor'=>['withdrawanchor'],
			'withdrawcompany'=>['withdrawcompany'],
			'anchorwithdrawpass'=>['withdrawanchor'],
			'companyanchorpass'=>['anchorlist'],
			'wagesdiff'=>['wagesdiff'],
			'repair'=>['wagesdiff'],
			'checklock'=>self::ACCESS_LOGIN,
			'islocked'=>self::ACCESS_LOGIN,
			'lockall'=>self::ACCESS_LOGIN,
			'anchorlistmanager'=>['anchorlistmanager'],//运营复审列表
			'anchorlistfinance'=>['anchorlistfinance'],//财务审核列表
		    'anchorcheckmanager'=>['anchorlistmanager'],//运营复审
		    'anchorcheckfinance'=>['anchorlistfinance'],//财务复审
		    'companylistmanager'=>['companylistmanager'],//运营复审公司列表
		    'companylistfinance'=>['companylistfinance'],//财务复审公司列表
		    'companyanchorlistmg'=>['companylistmanager'],//运营复审主播列表
		    'companyanchorlistfn'=>['companylistfinance'],//财务复审主播列表
		    'companyanchorcheckmg'=>['companylistmanager'],//运营复审
		    'companyanchorcheckfn'=>['companylistfinance'],//财务复审
		];
	}
	
	
	public function islocked(){//是否存在未提交
	    $message = [ 'status' => 0, 'info' => '隐藏' ];
	    $dao = D('admin_wages');
	    $type = I('post.type');
	    $month = I('post.month');
	    $statusx = I('post.statusx');//status_opmanager,status_finance
	    if(!$type || !$month || !$statusx ){
	        $message['info'] = '缺少参数！';
	        return $this->ajaxReturn($message);
	    }
	    $where = self::getcompanyidbytype($type);
	    $where['month'] = $month;
	    $res = $dao->where($where)->group('is_lock')->getField('is_lock,count(*) as num');
	    
	    switch ($statusx){
	        case 'status_op':
	            if($res[0]>0){
                    $message = [ 'status' => 1, 'info' => '待提交' ];
	            }else{
                    $message = [ 'status' => 2, 'info' => '已提交' ];
	            }
	            break;
	        case 'status_opmanager':
	            if($res[1]>0){
                    $message = [ 'status' => 1, 'info' => '待提交' ];
	            }elseif($res[2]>0||$res[3]>0){
                    $message = [ 'status' => 2, 'info' => '已提交' ];
	            }
	            break;
	        case 'status_finance_check':
	            if($res[2]>0){
                    $message = [ 'status' => 1, 'info' => '待提交' ];
	            }elseif($res[3]>0){
                    $message = [ 'status' => 2, 'info' => '已提交' ];
	            }
	            break;
	    }

	    return $this->ajaxReturn($message);
	}
	
	//提交到下个流程。
	//1.前一个流程没有待审核
	public function checklock(){//数据流转到下一个环节 判断是否有未审核的数据存在
	    $message = [ 'status' => 0, 'info' => '操作失败' ];
	    $dao = D('admin_wages');
	    $type = I('post.type');
	    $month = I('post.month');
	    $statusx = I('post.statusx');
	    if(!$type || !$month || !$statusx ){
	        $message['info'] = '缺少参数！';
	        return $this->ajaxReturn($message);
	    }
	    $where = self::getcompanyidbytype($type);
	    switch ($statusx){
	        case 'status_op'://提交到复审，判断是否存在未审核
	            $where['status_op'] = 0;//初审待审核
	            $info  = '存在待初审数据，无法完成此操作：';
	            break;
	        case 'status_opmanager'://提交到财务，判断是否存在未提交到复审
	            $where['is_lock'] = 0;
	            $info  = '存在待提交复审数据，无法完成此操作：';
	            break;
	        case 'status_finance_check'://提交到财务，判断是否存在未提交到财务
	            $where['is_lock'] = ['in',[0,1]];
	            $info  = '存在待提交财务数据，无法完成此操作：';
	            break;
	    }
	    
	    $where['month'] = $month;
	    $res = $dao->where($where)->getField('id,uid');
        if($res){
            $message['info'] = $info;
    	    foreach ($res as $id=>$uid){
    	        $message['info'] .= $uid."|";
    	    }
        }else{
            $message = [ 'status' => 1, 'info' => '操作成功' ];
        }
	    return $this->ajaxReturn($message);
	}
	
	public  function  lockall(){//运营初审到待复审。
        $message = [ 'status' => 0, 'info' => '缺少参数' ];
	    $Dao = D( 'admin_wages' );
	    $month=I('post.month')?I('post.month'):'';
	    $type=I('post.type')?I('post.type'):'';
	    $statusx=I('post.statusx')?I('post.statusx'):'status_op';

	    if( !$month || !$type|| !$statusx   )
	    {
	        return $this->ajaxReturn( $message );
	    }
	    $where = self::getcompanyidbytype($type);
	    switch ($statusx){
	        case 'status_op':
	            $save['uaid_op'] = get_uid();
	            $save['utime_op'] = get_date();
	            $save['is_lock'] = 1;
	            $where['is_lock'] = 0;
	            $save['status_opmanager'] = 3; 
	            $logtype = Wages::LOG_TYPE_COMIT_MANAGER;
	            break;
	        case 'status_opmanager':
	            $save['uaid_opmanager'] = get_uid();
	            $save['utime_opmanager'] = get_date();
	            $save['status_opmanager'] = 1;
	            $save['is_lock'] = 2;
	            $save['status_finance_check'] = 3;
	            $where['is_lock'] = 1;
	            $logtype = Wages::LOG_TYPE_COMIT_FINANCE;
	            break;
	        case 'status_finance_check':
	            $save['uaid_finance_check'] = get_uid();
	            $save['utime_finance_check'] = get_date();
	            $save['status_finance_check'] = 1;
	            $save['is_lock'] = 3;
	            $save['status_finance'] = 3;
	            $where['is_lock'] = 2;
	            $logtype = Wages::LOG_TYPE_COMIT_PAY;
	            break;
	    }
	    $where['month'] = $month;
	    $ids = $Dao->where($where)->getField('id',true);
	    Wages::log(['type'=>$logtype,'company_type'=>$type,'status'=>Wages::LOG_STATUS_PASS],$ids);
	    $res = $Dao->where( $where )->save( $save );
	    if($res===false){
	        $message = [ 'status' => 0, 'info' => '操作失败' ];
	    }else{
	        $message = [ 'status' => 1, 'info' => '操作成功' ];
	    }
	    return $this->ajaxReturn( $message );
	}
	
	public function getcompanyidbytype($type){//$type
	    $companyids = Company::getCompanyids02();
	    $companyids[0] = 0;
	    $companyids = array_keys($companyids);
	    if($type==1){//平台签约
	        $where["company_id"] = ['in',$companyids];
	    }elseif($type==2){//公司签约
	        $where["company_id"] = ['not in',$companyids];
	    }
	    return $where;
	}
	
	
	public function anchorlist(){
        $dao = D('admin_wages');
        $_POST['statusx'] = $_POST['statusx']?$_POST['statusx']: 'status_op';
        $statusop = Wages::STATUS_OP;//初审
        $statusopmanager = Wages::STATUS_OPMANAGER;//复审
        $statusfinancecheck = Wages::STATUS_FINANCE_CHECK;//财务审核
        $statusfinance = Wages::STATUS_FINANCE;//财务汇款
        $isblack = Wages::IS_BLACK;
        $yesno = ['1'=>'是','2'=>'否'];
        $post_is_lock = $_POST['is_lock']?$_POST['is_lock']:0;
        $is_lock = 1;
        $where = self::getcompanyidbytype(1);
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        if( $uid = I('get.uid') ) {
            $uid = explode(" ",$uid);
            $where['uid'] =["in",$uid];
        }
        if(!isset($_GET['status_op'])) {
            $_GET['status_op'] = -1;
        }
        
        if($_GET['status_op']>=0){
            $where["status_op"] = $_GET['status_op'];
        }
        
        if(!isset($_GET['status_opmanager'])) {
            $_GET['status_opmanager'] = -1;
        }
        
        if($_GET['status_opmanager']>=0){
            $where["status_opmanager"] = $_GET['status_opmanager'];
        }
        
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
        
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
        
        if(!isset($_GET['status_finance_check'])) {
            $_GET['status_finance_check'] = -1;
        }
        
        if($_GET['status_finance_check']>=0){
            $where["status_finance_check"] = $_GET['status_finance_check'];
        }
        
        if(!isset($_GET['yesno'])) {
            $_GET['yesno'] = -1;
        }
        
        if($_GET['yesno']>=0){
            $_GET['yesno'] == 1 && $where['company_id'] = 15;
            $_GET['yesno'] == 2 && $where['company_id'] = [$where['company_id'],['not in',15]];
        }else{
        }
        
        $where["month"] = $_GET['month'];
        
        $order = "company_id ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->where($where)->order($order)->select();
        }else{
            $sumdatas = $dao->where($where)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        foreach ($datas as &$data){
            $data['wages_sum'] = $data['wages_base_edit']+$data['wages_gift_edit']+$data['wages_bonuses'];//总收入
            $data['wages_tax'] = Wages::getanchortax($data['wages_sum']);//扣税
            $data['wages_base_afttax'] = $data['wages_sum'] - $data['wages_tax'];//应付
            $data['status_op_str'] = $statusop[$data['status_op']];
            $data['status_opmanager_str'] = $statusopmanager[$data['status_opmanager']];
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['ishuanpeng'] = $data['company_id'] == 15?"是":"否";
            $data['live_length'] = secondFormatH($data['live_length']);
            $data['bank_card'] = str_replace(' ','',$data['bank_card']);
            $data['address'] = explode(" ",$data['bankaddress']);
            $data['is_black'] = $isblack[$data['is_black']];
            if( $data['is_lock'] == $post_is_lock ){
                $is_lock = 0;
            }
        }
        
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit'] + $sumdata['wages_bonuses'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base_edit'] += $sumdata['wages_base_edit'];//底薪
            $sum['wages_gift_edit'] += $sumdata['wages_gift_edit'];//礼物
            $sum['wages_bonuses'] += $sumdata['wages_bonuses'];//奖励
            $sum['wages_sum']  += $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit']+$sumdata['wages_bonuses'];
            $sum['wages_tax'] += Wages::getanchortax($sumdata['wages_sum']);//扣税
            $sum['wages_base_afttax'] += ($sumdata['wages_sum'] - $sumdata['wages_tax']);//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,姓名,uid,身份证号,有效时长,有效天数,是否禁播,礼物收益,首次签约,底薪,奖励,总收入,代扣个税,应支付金额,银行,银行开户账号,收款账号省份,收款账号地市,收款账号地区码,开户银行,官方签约,初审状态,复审状态,财务审核,汇款状态');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['realname'],$d['uid'],"\t".$d['card'],$d['live_length'],$d['live_day'],$d['is_black'],$d['wages_gift_edit'],$d['live_start'],$d['wages_base_edit'],$d['wages_bonuses'],$d['wages_sum'],$d['wages_tax'],$d['wages_base_afttax'],$d['bank_name'],"\t".$d['bank_card'],$d['address'][0],$d['address'][1],$d['address'][2],$d['accountbank'],$d['ishuanpeng'],$d['status_op_str'],$d['status_opmanager_str'],$d['status_finance_check_str'],$d['status_finance_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'个人主播提现审核表');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusop = $statusop;
        $this->statusopmanager = $statusopmanager;
        $this->statusfinance = $statusfinance;
        $this->statusfinancecheck = $statusfinancecheck;
        $this->is_lock = $is_lock;//锁定状态为不可审核编辑。
        $this->yesno = $yesno;
        $this->display();
    }
    
    
    public function anchorlistmanager(){
        $_POST['is_lock'] = 1;
        $_POST['statusx'] = 'status_opmanager';
        $this->anchorlist();
    }
    public function anchorlistfinance(){
        $_POST['is_lock'] = 2;
        $_POST['statusx'] = 'status_finance_check';
        $this->anchorlist();
    }
    
    
    public function companylist(){
        $dao = D('admin_wages');
        $_POST['statusx'] = $_POST['statusx']?$_POST['statusx']: 'status_op';
        $statusop = Wages::STATUS_OP;//初审
        $statusopmanager = Wages::STATUS_OPMANAGER;//复审
        $statusfinancecheck = Wages::STATUS_FINANCE_CHECK;//财务审核
        $statusfinance = Wages::STATUS_FINANCE;//财务汇款
        $typecompany = Wages::TYPE_COMPANY;
        $isblack = Wages::IS_BLACK;
        
        if(!isset($_GET['type_company'])) {
            $_GET['type_company'] = 2;
        }
        
        if($_GET['type_company']>0){
            if($_GET['type_company']==1){//经纪公司签约
                $where = self::getcompanyidbytype(1);
            }else{//平台，工会，未签约等。
                $where = self::getcompanyidbytype(2);
            }
        }
        
        if($cid = I('get.cid')){
            $where['company_id'] = [$where['company_id'],['in',$cid]];
        }
    
        if(!isset($_GET['status_op'])) {
            $_GET['status_op'] = -1;
        }
    
        if($_GET['status_op']>=0){
            $where["status_op"] = $_GET['status_op'];
        }
    
        if(!isset($_GET['status_opmanager'])) {
            $_GET['status_opmanager'] = -1;
        }
    
        if($_GET['status_opmanager']>=0){
            $where["status_opmanager"] = $_GET['status_opmanager'];
        }
    
    
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
    
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
    
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
        if(!isset($_GET['status_finance_check'])) {
            $_GET['status_finance_check'] = -1;
        }
    
        if($_GET['status_finance_check']>=0){
            $where["status_finance_check"] = $_GET['status_finance_check'];
        }
    
        $where["month"] = $_GET['month'];
        $group = " company_id ";
        $order = "company_id ";
        $field = "status_finance,count(*) as anchornum,month,company_id,company_tax,company_rate,company_bank_name,company_bankaddress,company_name,company_realname,company_card,company_bank_card,sum(wages_base_edit) as wages_base_edit , sum(wages_gift_edit) as wages_gift_edit  , sum(wages_bonuses) as wages_bonuses ";
    
        if($export = I('get.export')){//导出数据
            $datas = $dao->field($field)->where($where)->group($group)->order($order)->select();
        }else{
            $sumdatas = $dao->field($field)->where($where)->group($group)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->field($field)
            ->group($group)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
    
        foreach ($datas as &$data){
            $data['month'] = $data['month']."月";
            $data['wages_sum'] = $data['wages_base_edit']+$data['wages_gift_edit']+$data['wages_bonuses'];//总收入
            $data['wages_base_afttax'] = $data['wages_sum'];//结算金额
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];//汇款状态
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['company_bank_card'] = str_replace(' ','',$data['company_bank_card']);
        }
    
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit'] + $sumdata['wages_bonuses'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base_edit'] += $sumdata['wages_base_edit'];//底薪
            $sum['wages_gift_edit'] += $sumdata['wages_gift_edit'];//礼物
            $sum['wages_bonuses'] += $sumdata['wages_bonuses'];//奖励
            $sum['wages_sum']  += $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit']+$sumdata['wages_bonuses'];
            $sum['wages_tax'] += Wages::getanchortax($sumdata['wages_sum']);//扣税
            $sum['wages_base_afttax'] = $sum['wages_sum'];//应付
        }
    
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,公司名称,主播数量,税率,分成比例,礼物收益,底薪收益,奖励收益,结算金额,收款人姓名,收款人身份证号,收款卡号,收款银行,收款开户行,汇款状态');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['company_name'],$d['anchornum'],$d['company_tax'],$d['company_rate'],$d['wages_gift_edit'],$d['wages_base_edit'],$d['wages_bounses'],$d['wages_base_afttax'],$d['company_name'],"\t".$d['company_card'],"\t".$d['company_bank_card'],$d['company_bank_name'],$d['company_bankaddress'],$d['status_finance_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'公司提现审核');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusfinance = $statusfinance;
        $this->statusfinancecheck = $statusfinancecheck;
        $this->statusop = $statusop;
        $this->statusopmanager = $statusopmanager;
        $this->typecompany = $typecompany;
        $this->display();
    }
    
    public function companylistmanager(){
        $_POST['statusx'] = 'status_opmanager';
        $this->companylist();
    }
    public function companylistfinance(){
        $_POST['statusx'] = 'status_finance_check';
        $this->companylist();
    }
    
    //财务查看系统生成的原始数据。
	public function anchorlist2(){
        $dao = D('admin_wages');
        $statusop = Wages::STATUS_OP;
        $statusfinance = Wages::STATUS_FINANCE;
        $isblack = Wages::IS_BLACK;
        $where = self::getcompanyidbytype(2);
        
        $where['type_system'] = 0;//财务查看系统初始化的数据。
        $yesno = ['1'=>'是','2'=>'否'];
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        if( $uid = I('get.uid') ) {
            $uid = explode(" ",$uid);
            $where['uid'] =["in",$uid];
        }
        if(!isset($_GET['status_op'])) {
            $_GET['status_op'] = -1;
        }
        
        if($_GET['status_op']>=0){
            $where["status_op"] = $_GET['status_op'];
        }
        
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
        
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
        
        if(!isset($_GET['yesno'])) {
            $_GET['yesno'] = -1;
        }
        
        if($_GET['yesno']>=0){
            $_GET['yesno'] == 1 && $where["company_id"] = 15;
            $_GET['yesno'] == 2 && $where["company_id"] = [$where['company_id'],['not in',15]];
        }
        
        $where["month"] = $_GET['month'];
        
        $order = "company_id ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->where($where)->order($order)->select();
        }else{
            $sumdatas = $dao->where($where)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        foreach ($datas as &$data){
            $data['wages_sum'] = $data['wages_base']+$data['wages_gift'];//总收入
            $data['wages_tax'] = Wages::getanchortax($data['wages_sum']);//扣税
            $data['wages_base_afttax'] = $data['wages_sum'] - $data['wages_tax'];//应付
            $data['status_op_str'] = $statusop[$data['status_op']];
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['ishuanpeng'] = $data['company_id'] == 15?"是":"否";
            $data['live_length'] = secondFormatH($data['live_length']);
            $data['bank_card'] = str_replace(' ','',$data['bank_card']);
            $data['address'] = explode(" ",$data['bankaddress']);
            $data['is_black'] = $isblack[$data['is_black']];
        }
        
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base'] + $sumdata['wages_gift'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base'] += $sumdata['wages_base'];//底薪
            $sum['wages_gift'] += $sumdata['wages_gift'];//礼物
            $sum['wages_sum'] += $sumdata['wages_base']+$sumdata['wages_gift'];//总收入
            $sum['wages_tax'] += Wages::getanchortax($sumdata['wages_sum']);//扣税
            $sum['wages_base_afttax'] += ($sumdata['wages_sum'] - $sumdata['wages_tax']);//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,姓名,uid,身份证号,有效时长,有效天数,是否禁播,礼物收益,首次签约,底薪,奖励,总收入,代扣个税,应支付金额,银行,银行开户账号,收款账号省份,收款账号地市,收款账号地区码,开户银行,官方签约,审核状态,汇款状态');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['realname'],$d['uid'],"\t".$d['card'],$d['live_length'],$d['live_day'],$d['is_black'],$d['wages_gift'],$d['live_start'],$d['wages_base'],$d['wages_bonuses'],$d['wages_sum'],$d['wages_tax'],$d['wages_base_afttax'],$d['bank_name'],"\t".$d['bank_card'],$d['address'][0],$d['address'][1],$d['address'][2],$d['accountbank'],$d['ishuanpeng'],$d['status_op_str'],$d['status_finance_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'个人主播提现审核表');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusop = $statusop;
        $this->statusfinance = $statusfinance;
        $this->yesno = $yesno;
        $this->display();
    }
	public function companyanchorlist(){
        $dao = D('admin_wages');
        $statusop = Wages::STATUS_OP;
        $statusopmanager = Wages::STATUS_OPMANAGER;
        $statusfinance = Wages::STATUS_FINANCE;
        $statusfinancecheck = Wages::STATUS_FINANCE_CHECK;
        $typecompany = Wages::TYPE_COMPANY;
        $isblack = Wages::IS_BLACK;
        $post_is_lock = $_POST['is_lock']?$_POST['is_lock']:0;
        $is_lock = 1;
        
        if(!isset($_GET['type_company'])) {
            $_GET['type_company'] = 2;
        }
        
        if($_GET['type_company']>0){
            if($_GET['type_company']==1){//经纪公司签约
                $where = self::getcompanyidbytype(1);
            }else{//平台，工会，未签约等。
                $where = self::getcompanyidbytype(2);
            }
        }
        
        if($cid = I('get.cid')){
            $where['company_id'] = [$where['company_id'],['in',$cid]];
        }
        
        
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        
        if(!isset($_GET['status_op'])) {
            $_GET['status_op'] = -1;
        }
        
        if($_GET['status_op']>=0){
            $where["status_op"] = $_GET['status_op'];
        }
        if($uid = I('get.uid')){
            $uid = explode(" ",$uid);
            $where["uid"] = ['in',$uid];
        }
        
        if(!isset($_GET['status_opmanager'])) {
            $_GET['status_opmanager'] = -1;
        }
        
        if($_GET['status_opmanager']>=0){
            $where["status_opmanager"] = $_GET['status_opmanager'];
        }
		
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
        
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
        if(!isset($_GET['status_finance_check'])) {
            $_GET['status_finance_check'] = -1;
        }
        
        if($_GET['status_finance_check']>=0){
            $where["status_finance_check"] = $_GET['status_finance_check'];
        }
        
        $where["month"] = $_GET['month'];
        
        $order = "company_id ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->where($where)->order($order)->select();
        }else{
            $sumdatas = $dao->where($where)->order($order)->select();
            $count = count($sumdatas);
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
            $data['status_opmanager_str'] = $statusopmanager[$data['status_opmanager']];
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['is_black'] = $isblack[$data['is_black']];
            $data['live_length'] = secondFormatH($data['live_length']);
            if( $data['is_lock'] == $post_is_lock ){
                $is_lock = 0;
            }
        }
        
        
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit'] + $sumdata['wages_bonuses'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base_edit'] += $sumdata['wages_base_edit'];//底薪
            $sum['wages_gift_edit'] += $sumdata['wages_gift_edit'];//礼物
            $sum['wages_bonuses'] += $sumdata['wages_bonuses'];//奖励
            $sum['wages_sum']  += $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit']+$sumdata['wages_bonuses'];
            $sum['wages_tax'] += Wages::getanchortax($sumdata['wages_sum']);//扣税
            $sum['wages_base_afttax'] += ($sumdata['wages_sum'] - $sumdata['wages_tax']);//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,公司名称,uid,昵称,姓名,首次签约日期,是否禁播,有效时长,有效天数,礼物收益,底薪,奖励,总收入,初审,复审,财务审核,汇款');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['company_name'],$d['uid'],$d['nick'],$d['realname'],$d['live_start'],$d['is_black'],$d['live_length'],$d['live_day'],$d['wages_gift_edit'],$d['wages_base_edit'],$d['wages_bonuses'],$d['wages_sum'],$d['status_op_str'],$d['status_opmanager_str'],$d['status_finance_str'],$d['status_finance_check_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'公司统计表');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusop = $statusop;
        $this->statusopmanager = $statusopmanager;
        $this->statusfinance = $statusfinance;
        $this->statusfinancecheck = $statusfinancecheck;
        $this->is_lock = $is_lock;//财务锁定
        $this->typecompany = $typecompany;
        $this->type_finance = $type_finance;
        $this->display();
    }
    
    public function companyanchorlistmg(){
        $_POST['statusx'] = 'status_opmanager';
        $_POST['is_lock'] = 1;
        $this->companyanchorlist();
    }
    public function companyanchorlistfn(){
        $_POST['statusx'] = 'status_finance_check';
        $_POST['is_lock'] = 2;
        $this->companyanchorlist();
    }
    
	public function companyanchorlist2(){
        $dao = D('admin_wages');
        $statusop = Wages::STATUS_OP;
        $isblack = Wages::IS_BLACK;
        $where['type_system'] = 0;
        if($cid = I('get.cid')){
            $where['company_id'] = $cid;
        }else{
            $where['company_id'] = [['gt',0]];
        }
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        
        if(!isset($_GET['status_op'])) {
            $_GET['status_op'] = -1;
        }
        
        if($_GET['status_op']>=0){
            $where["status_op"] = $_GET['status_op'];
        }
        if($uid = I('get.uid')){
            $uid = explode(" ",$uid);
            $where["uid"] = ['in',$uid];
        }
		
        $where["month"] = $_GET['month'];
        
        $order = "company_id ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->where($where)->order($order)->select();
        }else{
            $sumdatas = $dao->where($where)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        
        foreach ($datas as &$data){
            $data['wages_base_afttax'] = $data['wages_sum'] = $data['wages_base']+$data['wages_gift'];//总收入
            $data['status_op_str'] = $statusop[$data['status_op']];
            $data['is_black'] = $isblack[$data['is_black']];
            $data['live_length'] = secondFormatH($data['live_length']);
        }
        
        
        foreach ($sumdatas as $sumdata){
            $data['wages_sum']  = $sumdata['wages_base'] + $sumdata['wages_gift'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base'] += $sumdata['wages_base'];//底薪
            $sum['wages_gift'] += $sumdata['wages_gift'];//礼物
            $sum['wages_sum']  += $sumdata['wages_base'] + $sumdata['wages_gift'];
            $sum['wages_base_afttax'] = $sum['wages_sum'];//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,公司名称,uid,昵称,姓名,首次签约日期,是否禁播,有效时长,有效天数,礼物收益,底薪,总收入,审核状态');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['company_name'],$d['uid'],$d['nick'],$d['realname'],$d['live_start'],$d['is_black'],$d['live_length'],$d['live_day'],$d['wages_gift'],$d['wages_base'],$d['wages_sum'],$d['status_op_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'公司统计表');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusop = $statusop;
        $this->display();
    }
    
    
	public function companylist2(){
        $dao = D('admin_wages');
        $statusfinance = Wages::STATUS_FINANCE;
        $where['type_system'] = 0;
        if($cid = I('get.cid')){
            $where['company_id'] = $cid;
        }else {
            $where['company_id'] = [['gt',0]];
        }
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
        
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
		
        $where["month"] = $_GET['month'];
        $group = " company_id ";
        $order = "company_id ";
        $field = "status_finance,count(*) as anchornum,month,company_id,company_tax,company_rate,company_bank_name,company_bankaddress,company_name,company_realname,company_card,company_bank_card,sum(wages_base) as wages_base , sum(wages_gift) as wages_gift   ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->field($field)->where($where)->group($group)->order($order)->select();
        }else{
            $sumdatas = $dao->field($field)->where($where)->group($group)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->field($field)
            ->group($group)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        
        foreach ($datas as &$data){
            $data['month'] = $data['month']."月";
            $data['wages_sum'] = $data['wages_base']+$data['wages_gift'];//总收入
            $data['wages_base_afttax'] = $data['wages_sum'];//结算金额
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];//汇款状态
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['company_bank_card'] = str_replace(' ','',$data['company_bank_card']);
        }
        
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base'] + $sumdata['wages_gift'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base'] += $sumdata['wages_base'];//底薪
            $sum['wages_gift'] += $sumdata['wages_gift'];//礼物
            $sum['wages_bonuses'] += $sumdata['wages_bonuses'];//奖励
            $sum['wages_sum']  += $sumdata['wages_base'] + $sumdata['wages_gift'];
            $sum['wages_tax'] += Wages::getanchortax($sumdata['wages_sum']);//扣税
            $sum['wages_base_afttax'] = $sum['wages_sum'];//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,公司名称,主播数量,税率,分成比例,礼物收益,底薪收益,结算金额,收款人姓名,收款人身份证号,收款卡号,收款银行,收款开户行,汇款状态');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['company_name'],$d['anchornum'],$d['company_tax'],$d['company_rate'],$d['wages_gift'],$d['wages_base'],$d['wages_base_afttax'],$d['company_name'],"\t".$d['company_card'],"\t".$d['company_bank_card'],$d['company_bank_name'],$d['company_bankaddress'],$d['status_finance_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'公司提现审核');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusfinance = $statusfinance;
        $this->display();
    }
    
    public function companyanchorcheck(){
        $_POST['statusx'] = 'status_opmanager';
        $this->anchorcheck();
    }
    public function companyanchorcheckmg(){
        $_POST['statusx'] = 'status_opmanager';
        $this->anchorcheck();
    }
    public function companyanchorcheckfn(){
        $_POST['statusx'] = 'status_finance_check';
        $this->anchorcheck();
    }
    public function anchorcheckmanager(){
        $_POST['statusx'] = 'status_opmanager';
        $this->anchorcheck();
    }
    public function anchorcheckfinance(){
        $_POST['statusx'] = 'status_finance_check';
        $this->anchorcheck();
    }
    public function anchorcheck(){
        $dao = D('admin_wages');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        $edit = is_numeric(I('get.edit'))?I('get.edit'):1;
        if(IS_POST){
            $data = $dao->create();
            if($data['id']){
                $statusx = I('post.statusx')?I('post.statusx'):'status_op';
                switch ($statusx){
                    case 'status_op':
                        $data['uaid_op'] = Admin::getUid();
                        $data['utime_op'] = get_date();
                        $logdata['type'] = Wages::LOG_TYPE_OP_1;
                        break;
                    case 'status_opmanager':
                        $data['uaid_opmanager'] = Admin::getUid();
                        $data['utime_opmanager'] = get_date();
                        if($data[$statusx] == 2){
                            $data['is_lock'] = 0 ;
                        }
                        $logdata['type'] = Wages::LOG_TYPE_OP_2;
                        break;
                    case 'status_finance_check':
                        $data['uaid_finance_check'] = Admin::getUid();
                        $data['utime_finance_check'] = get_date();
                        if($data[$statusx] == 2){
                            $data['is_lock'] = 0 ;
                        }
                        $logdata['type'] = Wages::LOG_TYPE_FINANCE_CHECK;
                        break;
                }
                $logdata['wages_id'] = $id;
                $logdata['status'] = $data[$statusx];
                $logdata['note'] = $data['note'];
                $data['uaid'] = Admin::getUid();
                $data['utime'] = get_date();
                $dao->data($data)->save();
                Wages::log($logdata);
                return $this->ajaxReturn(['status'=>0,'msg'=>'操作成功']);
            }else{
                return $this->ajaxReturn(['status'=>0,'msg'=>'操作成功']);
            }
        }
        $assign = $id?$dao->find($id):[];
        $assign['wages_sum'] = $assign['wages_base_edit']+$assign['wages_gift_edit']+$assign['wages_bonuses'];//总收入
        $assign['wages_tax']  = Wages::getanchortax($assign['wages_sum']);//扣税
        $assign['wages_base_afttax'] = $assign['wages_sum'] - $assign['wages_tax'];//应付
        $this->assign($assign);
        $this->statusop = Wages::STATUS_OP;
        $this->statusopmanager = Wages::STATUS_OPMANAGER;
        $this->statusfinancecheck = Wages::STATUS_FINANCE_CHECK;
        $this->edit = $edit;
        $this->display();
    }
    
    //批量审核
    public function companyanchorpass(){
        $msg = ['status'=>0,'info'=>'操作成功'];
        if(IS_POST){
            $ids = I('post.ids');
            $status = I('post.status');
            $note = I('post.note');
            $ids = explode(',', $ids);
            
            $statusx = I('post.statusx')?I('post.statusx'):'status_op';
            $data[$statusx] = $status;
            switch ($statusx){
                case 'status_op':
                    $data['uaid_op'] = Admin::getUid();
                    $data['utime_op'] = get_date();
                    $logdata['type'] = Wages::LOG_TYPE_OP_1;
                    break;
                case 'status_opmanager':
                    $data['uaid_opmanager'] = Admin::getUid();
                    $data['utime_opmanager'] = get_date();
                    if($data[$statusx] == 2){
                        $data['is_lock'] = 0 ;
                    }
                    $logdata['type'] = Wages::LOG_TYPE_OP_2;
                    break;
                case 'status_finance_check':
                    $data['uaid_finance_check'] = Admin::getUid();
                    $data['utime_finance_check'] = get_date();
                    if($data[$statusx] == 2){
                        $data['is_lock'] = 0 ;
                    }
                    $logdata['type'] = Wages::LOG_TYPE_FINANCE_CHECK;
                    break;
            }
            
            $data['uaid'] = Admin::getUid();
            $data['utime'] = date("Y-m-d H:i:s");
            $data['status_op'] = $status;
            $data['note'] = $note;
            foreach ($ids as $id){
                D("admin_wages")->data($data)->where(['id'=>$id])->save();
            }
            $logdata['status'] = $data[$statusx];
            $logdata['note'] = $note;
            Wages::log($logdata,$ids);
            $msg['status'] = 0;
            $msg['msg'] = "操作成功";
        }
        return $this->ajaxReturn($msg);
    }
    
    
    
    
    public function withdrawcompany(){
        $dao = D('admin_wages');
        $statusopMap = Wages::STATUS_OP_H;
        $statusfinance = Wages::STATUS_FINANCE;
        $where = self::getcompanyidbytype(2);
        if($cid = I('get.cid')){
            $where['company_id'] = [$where['company_id'],['in',$cid]];
        }
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
        
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
		
        $where["month"] = $_GET['month'];
        $where["status_op"] = $statusopMap['pass'];//运营审核通过
        $where["status_finance_check"] = 1;
        $group = " company_id ";
        $order = "bank_name,id ";
        $field = "status_finance,count(*) as anchornum,month,company_id,company_tax,company_rate,company_bank_name,company_bankaddress,company_name,company_realname,company_card,company_bank_card,sum(wages_base_edit) as wages_base_edit , sum(wages_gift_edit) as wages_gift_edit  , sum(wages_bonuses) as wages_bonuses ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->where($where)->field($field)->group($group)->order($order)->select();
        }else{
            $sumdatas = $dao->where($where)->field($field)->group($group)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->field($field)
            ->group($group)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        
        foreach ($datas as &$data){
            $data['month'] = $data['month']."月";
            $data['date'] = date("Ymd");
            $data['company_detail'] = 'company_'.$data['company_id'];
            $data['wages_sum'] = $data['wages_base_edit']+$data['wages_gift_edit']+$data['wages_bonuses'];//总收入
            $data['wages_base_afttax'] = $data['wages_sum'];//结算金额
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];//汇款状态
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['company_bank_card'] = str_replace(' ','',$data['company_bank_card']);
            
        }
        
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit'] + $sumdata['wages_bonuses'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base_afttax'] += $sumdata['wages_sum'];//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('币种,日期,明细标志,顺序号,付款账号开户行,付款账号/卡号,付款账号名称/卡名称,收款账号开户行,收款账号省份,收款账号地市,收款账号地区码,收款账号,收款账号名称,金额,汇款用途,备注信息,汇款方式,收款账户短信通知手机号码,自定义序号,汇款状态');
            foreach ($datas as $d) {
                ++$i;
                $excel[] = array('RMB',$d['date'],$d['company_detail'],$i,'中国银行股份有限工商霍城分行',"\t".'107062508897','北京六间房科技有限公司霍城分公司',$d['company_bankaddress'],'省','市','区码',"\t".$d['company_bank_card'],$d['company_name'],$d['wages_base_afttax'],'劳务费','备注','','',$d['company_id'],$d['status_finance_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'公司提现表');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->page = $Page->show();
        $this->statusfinance = $statusfinance;
        $this->display();
    }
    
    public function withdrawanchor(){
        $dao = D('admin_wages');
        $statusopMap = Wages::STATUS_OP_H;
        $statusfinance = Wages::STATUS_FINANCE;
        $where = self::getcompanyidbytype(1);
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        
        if(!isset($_GET['status_finance'])) {
            $_GET['status_finance'] = -1;
        }
        
        if($_GET['status_finance']>=0){
            $where["status_finance"] = $_GET['status_finance'];
        }
		
        if($uid = I("get.uid")){
            $uid = explode(" ",$uid);
            $where['uid'] = ["in",$uid];
        }
        
        $where["month"] = $_GET['month'];
        $where["status_op"] = $statusopMap['pass'];//运营审核通过
        $where["status_finance_check"] = 1;//财务审核通过
        $order = "bank_name ";
		
        if($export = I('get.export')){//导出数据
            $datas = $dao->where($where)->order($order)->select();
        }else{
            $sumdatas = $dao->where($where)->order($order)->select();
            $count = count($sumdatas);
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->order($order)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        
        foreach ($datas as &$data){
            $data['month'] = $data['month']."月";
            $data['date'] = date("Ymd");
            $data['company_detail'] = 'anchor_'.$data['uid'];
            $data['wages_sum'] = $data['wages_base_edit']+$data['wages_gift_edit']+$data['wages_bonuses'];//总收入
            $data['wages_tax'] = Wages::getanchortax($data['wages_sum']);//扣税
            $data['wages_base_afttax'] = $data['wages_sum'] - $data['wages_tax'];//结算金额
            $data['status_finance_str'] = $statusfinance[$data['status_finance']];//汇款状态
            $data['status_finance_check_str'] = $statusfinancecheck[$data['status_finance_check']];
            $data['address'] = explode(" ",$data['bankaddress']);
            $data['bank_card'] = str_replace(' ','',$data['bank_card']);
            if($data['status_finance'] == 1 ||$data['status_finance'] == 2 ){
                $is_lock = 1;
            }
        }
        foreach ($sumdatas as $sumdata){
            $sumdata['wages_sum']  = $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit'] + $sumdata['wages_bonuses'];
            $sum['sum_num'] ++;//底薪
            $sum['wages_base_edit'] += $sumdata['wages_base_edit'];//底薪
            $sum['wages_gift_edit'] += $sumdata['wages_gift_edit'];//礼物
            $sum['wages_bonuses'] += $sumdata['wages_bonuses'];//奖励
            $sum['wages_sum']  += $sumdata['wages_base_edit'] + $sumdata['wages_gift_edit']+$sumdata['wages_bonuses'];
            $sum['wages_tax'] += Wages::getanchortax($sumdata['wages_sum']);//扣税
            $sum['wages_base_afttax'] += ($sumdata['wages_sum'] - $sumdata['wages_tax']);//应付
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('币种,日期,明细标志,顺序号,付款账号开户行,付款账号/卡号,付款账号名称/卡名称,收款账号开户行,收款账号省份,收款账号地市,收款账号地区码,收款账号,收款账号名称,金额,汇款用途,备注信息,汇款方式,收款账户短信通知手机号码,自定义序号,汇款状态');
            foreach ($datas as $d) {
                ++$i;
                $excel[] = array('RMB',$d['date'],$d['company_detail'],$i,'工行',"\t".'0409003809300065690','北京六间房科技有限公司保定分公司',$d['accountbank'],$d['address'][0],$d['address'][1],$d['address'][2],"\t".$d['bank_card'],$d['realname'],$d['wages_base_afttax'],'劳务费','备注','','',$d['uid'],$d['status_finance_str']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'主播提现列表');
        }
        $this->data = $datas;
        $this->sum = $sum;
        $this->is_lock = $is_lock;
        $this->page = $Page->show();
        $this->statusfinance = $statusfinance;
        $this->display();
    }
    
    public function wagesdiff(){
        $dao = D('admin_wages');
        $statusop = Wages::STATUS_OP;
        $isblack = Wages::IS_BLACK;
        $where['_string'] = " wages_bonuses != 0 or wages_base_edit != wages_base or wages_gift_edit != wages_gift  ";
        if($cid = I('get.cid')){
            $where['company_id'] = $cid;
        }else{
            $where['company_id'] = [['gt',0]];
        }
        if(!($month = I('get.month'))) {
            $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
        }
        
        if(!isset($_GET['status_op'])) {
            $_GET['status_op'] = -1;
        }
        
        if($_GET['status_op']>=0){
            $where["status_op"] = $_GET['status_op'];
        }
        if($uid = I('get.uid')){
            $where["uid"] = $uid;
        }
		
        $where["month"] = $_GET['month'];
        
        $order = "company_id ";
		
        if($export = I('get.export')){//导出数据
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
            $data['wages_base_afttax'] = $data['wages_sum'] = $data['wages_base']+$data['wages_gift'] ;//总收入
            $data['wages_base_afttax_edit'] = $data['wages_sum_edit'] = $data['wages_base_edit']+$data['wages_gift_edit']+$data['wages_bonuses'];//总收入
            $data['status_op_str'] = $statusop[$data['status_op']];
            $data['is_black'] = $isblack[$data['is_black']];
            $data['live_length'] = secondFormatH($data['live_length']);
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('月份,公司名称,uid,昵称,姓名,首播日期,是否禁播,有效时长,有效天数,礼物收益,修改后礼物收益,底薪,修改后底薪,奖励,总收入,修改后总收入,审核状态,备注');
            foreach ($datas as $d) {
                $excel[] = array($d['month'],$d['company_name'],$d['uid'],$d['nick'],$d['realname'],$d['live_start'],$d['is_black'],$d['live_length'],$d['live_day'],$d['wages_gift'],$d['wages_gift_edit'],$d['wages_base'],$d['wages_base_edit'],$d['wages_bonuses'],$d['wages_sum'],$d['wages_sum_edit'],$d['status_op_str'],$d['note']);
            }
            \HP\Util\Export::outputCsv($excel,$month.'差异统计');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->statusop = $statusop;
        $this->display();
    }
    
    public function companywithdrawpass(){
        $msg = ['status'=>0,'info'=>'操作成功'];
        if(IS_POST){
            $ids = I('post.ids');
            $status = I('post.status');
            $month = I('post.month');
            $ids = explode(',', $ids);
            $data['uaid_finance'] = Admin::getUid();
            $data['utime_finance'] = date("Y-m-d H:i:s");
            $data['status_finance'] = $status;
            //汇款失败：退回到运营初审。修改银行卡信息。
            if($status ==2) $data['is_lock'] = 0;
            $where['status_op'] = 1;
            $where['month'] = $month;
            foreach ($ids as $id){
                $where['company_id'] = $id;
                D("admin_wages")->data($data)->where($where)->save();
                $ids = D('admin_wages')->where($where)->getField('id',true);
                Wages::log(['type'=>Wages::LOG_TYPE_FINANCE,'status'=>$status], $ids);
            }
            $msg['status'] = 0;
            $msg['msg'] = "操作成功";
        }
        return $this->ajaxReturn($msg);
    }
    
    public function anchorwithdrawpass(){
        $msg = ['status'=>0,'info'=>'操作成功'];
        if(IS_POST){
            $ids = I('post.ids');
            $status = I('post.status');
            $ids = explode(',', $ids);
            $data['uaid_finance'] = Admin::getUid();
            $data['utime_finance'] = date("Y-m-d H:i:s");
            $data['status_finance'] = $status;
            if($status == 2)$data['is_lock'] = 0;
            foreach ($ids as $id){
                D("admin_wages")->data($data)->where(['id'=>$id])->save();
            }
            Wages::log(['type'=>Wages::LOG_TYPE_FINANCE,'status'=>$status], $ids);
            $msg['status'] = 0;
            $msg['msg'] = "操作成功";
        }
        return $this->ajaxReturn($msg);
    }
	
    
    public function repair(){//特殊数据处理
        $month = $_REQUEST['month'];
        $uids = $_REQUEST['uids'];
        $do = $_REQUEST['do'];
        $where['uid'] = ["in",$uids];
        $where['month'] = $month;
        $wages = D("admin_wages")->where($where)->select();
        if($wages) {
            echo "已存在：";
            dump($wages);
        }
        $repairwages = Wages::getWages($month.'-01',$uids);
        foreach ($repairwages as $repairwage){
            $where['uid'] = $repairwage['uid'];
            $where['month'] = $repairwage['month'];
            if($do=='do'){
                D("admin_wages")->where($where)->delete();
                D("admin_wages")->where($where)->add($repairwage);
            }else{
                echo D("admin_wages")->fetchSql(true)->where($where)->delete();echo "<br>";
                echo D("admin_wages")->fetchSql(true)->where($where)->add($repairwage);echo "<br>";
            }
        }
    }
    
    public function statement()
    {
    	if(!($month = I('get.month'))) {
    		$_GET['month'] = $month = date('Y-m');
    	}
    	if($month < '2017-03' || $month > date('Y-m', strtotime('+1 month'))) {
    		$this->error('输入月份错误');
    	}
    	$month = date('Ym',strtotime($month));
    	
    	
    	$redis = new \Think\Cache\Driver\Redis();
    	$datas = $redis->get('op_wages_statement' . $month);
    	if(!$datas || isset($_GET['del'])) {
	    	$users = $datas = [];
	    	$tmpMonth = $month;
	    	while($tmpMonth > '201703') {
	    		$tmpMonth = date('Ym',strtotime(date($tmpMonth . '01'))-86400);
	    		$dao = D('hpf_statement_' . $tmpMonth);
	    		$ids = $dao->field('max(id) as id')->group('uid')->select();
		    	$field = "uid,hb,gb,hd,gd";
		    	$statements = $dao->field($field)->where(['id'=>['in', implode(',',array_column($ids, 'id'))]])->select();
	    		//$field = "uid,hb,gb,hd,gd";
	    		//$statements = $dao->field($field)->order("id desc")->select();
	    		//echo $dao->getLastSql() . "<br/>";
		    	foreach ($statements as $statement){
		    		if($users[$statement['uid']])continue;
		    			$users[$statement['uid']] = $statement;
		    	}
	    	}
	    	foreach ($users as $user){
	    		$datas['hb'] += $user['hb'];
	    		$datas['gb'] += $user['gb'];
	    		$datas['hd'] += $user['hd'];
	    		$datas['gd'] += $user['gd'];
	    	}
	    	$dao = new \Common\Model\HPFMonthModel("rechargeRecord", date('Y-m-d',strtotime(date($month . '01'))-86400));
	    	$where = ['status'=>100];
	    	$where['ctime'][] = ['egt', date('Y-m-01 00:00:00', strtotime($month.'01') - 86400)];
	    	$where['ctime'][] = ['elt', date('Y-m-d 23:59:59', strtotime($month.'01') - 86400)];
	    	$hb = $dao->field('sum(hb) as hbd')->where($where)->find();
	    	$datas['hbd'] = $hb['hbd'];  //欢朋币充值
	    	$dao = new \Common\Model\HPFMonthModel("innerRechargeRecord", date('Y-m-d',strtotime(date($month . '01'))-86400));
	    	$hb = $dao->field('sum(hb) as hb')->where(['hb'=>['gt', 0],'channel'=>['neq', 0]])->find(); //运营下发欢朋币
	    	$datas['ophbd'] = $hb['hb'];
	    	$hb = $dao->field('sum(hd) as hd')->where(['hd'=>['gt', 0],'channel'=>['neq', 0]])->find();  //运营下发欢朋豆
	    	$datas['ophdd'] = $hb['hd'];
	    	$dao = D('hpf_statement_' . date('Ym',strtotime(date($month . '01'))-86400));
	    	$hb = $dao->field('sum(hdd) as hdd')->where(['hdd'=>['gt', 0],'type'=>9])->find();
	    	$datas['hdd'] = $hb['hdd'];
	    	foreach($datas as $k=>$v) {
	    		$datas[$k] = $v/1000;
	    	}
	    	$redis->set('op_wages_statement' . $month, $datas, 86400);
    	} 
    	$this->date = date('Y-m-d 23:59:59', strtotime($month.'01') - 86400);
    	$this->sdate = date('Y-m-01 00:00:00', strtotime($month.'01') - 86400);
    	$this->data = $datas;
    	$this->display();
    }

	
	//财务查看系统生成的原始数据。
	public function log(){
		$dao = D("admin_wages");
		$logdao = D("admin_wages_log");
		$types = Wages::TYPE_LOG;
		$status = Wages::STATUS_LOG;
		$typecompany = Wages::TYPE_COMPANY;
		$companys = D('admin_acl_user')->getField('uid,realname');
		if( $wages_id = I( 'get.wages_id' ) )
		{
			$where['a.wages_id'] = ['in',[$wages_id]];
		}
		
		
		if( $uid = I( 'get.uid' ) )
		{
			$where['a.uid'] = $uid;
		}
		if( $luid = I( 'get.luid' ) )
		{
			$where['a.luid'] = $luid;
		}
		if( $t = I( 'get.t' ) )
		{
			$where['a.type'] = $t;
		}
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-01');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if( $stime = I( "get.timestart" ) )
		{
			$stime .= " 00:00:00";
			$where['a.ctime'][] = [ 'egt', $stime ];
		}
		if( $etime = I( "get.timeend" ) )
		{
			$etime .= " 23:59:59";
			$where['a.ctime'][] = [ 'elt', $etime ];
		}
		if($username = I('get.username')){
			$where['b.nick'] = ['like',"%$username%"];
		}
		if($company = I('get.company')){
			list($companyname,$cid) = explode('|',$company);
			$where['c.cid'] = $cid;
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $logdao
				->alias( ' a ' )
				->join( " left join " . $dao->getTableName() . " as b on a.wages_id = b.id  " )
				->where( $where )
				->order( 'a.ctime desc ' )
				->select();
		}
		else
		{
			$count = $logdao
				->alias( ' a ' )
				->join( " left join " . $dao->getTableName() . " as b on a.wages_id = b.id " )
				->where( $where )->count();
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			
			$results = $logdao
			->alias( ' a ' )
			->join( " left join " . $dao->getTableName() . " as b on a.wages_id = b.id  " )
			->where( $where )
			->limit( $Page->firstRow . ',' . $Page->listRows )
			->order( 'a.ctime desc ' )
			->select();
			
		}
		foreach ($results as &$result){
		    $result['uaname'] = $companys[$result['uaid']];
		    $result['type_str'] = $types[$result['type']];
		    $result['status_str'] = $status[$result['status']];
		    
		}
		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '管理员ID', '管理员姓名','uid','昵称', '公司', '操作','状态','描述', '操作时间' );
			foreach ( $results as $data )
			{
				$excel[] = array( $data['uaid'], $data['uaname'],$data['uid'], $data['nick'], $data['company_name'],$data['type_str'], $data['status_str'], $data['note'], $data['ctime'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '直播审核列表' );
		}
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->companys = $companys;
		$this->data = $results;
		$this->types = $types;
		$this->page = $Page->show();
		$this->display();
	}

}
