<?php

namespace Admin\Controller;
use \HP\Op\Company;
use HP\Op\Anchor;
use HP\Op\Admin;


class CompanyController extends BaseController{

    protected $pageSize = 10;
    public function _access()
    {
        return [
           'companylist' => ['companylist'],
           'company' => ['company'],
           'companysave' => ['company'],
        ];
    }
    
    public function companylist(){
        $dao = D('Company');
        $daoAnchor = D('anchor');
        $daoLength = D('liveLength');
        if($cid = I('get.cid')){
            $where['id'] = $cid;
        }
        if(!($startTime = I('get.timestart'))) {
            $_GET['timestart'] = $startTime = date('Y-m-01');
        }
		if(!($endTime = I('get.timeend'))) {
            $_GET['timeend'] = $endTime = date('Y-m-d');
        }
        if(!($order = I('get.order'))) {
            $_GET['order'] = $order = 1;
        }
		
		$orderby = $dao->getOrderSql($order);
        $whereDate = ' `date`>="' . $startTime . '" and `date`<="' . $endTime . '"';
        $whereCompany = $cid ? ' a.id=' . $cid : ' 1 ';
        $sql = "select * from " . $dao->getTableName(). " a 
                left join (select cid,count(*) as companypeople from " . $daoAnchor->getTableName(). " group by cid)b on a.id=b.cid
                left join (select cid,sum(coin) as coin,sum(bean) as bean from " . $daoLength->getTableName(). " where " . $whereDate . " group by cid)c on a.id=c.cid 
                where " . $whereCompany . " order by " . $orderby;
        if($export = I('get.export')){//导出数据
            $datas = $dao->query($sql);
        }else{
            $count = $dao->where($where)->count();
            $Page = new \HP\Util\Page($count, $_GET['export'] ? 0 : $this->pageSize);
            $sql .= ' limit ' . $Page->firstRow.','.$Page->listRows;
            $datas = $dao->query($sql);
        }
        
        if($datas) { //公司收益是个人收益向下取整然后相加的总和
            $incomeWhere['date'][] = ['egt', $startTime];
            $incomeWhere['date'][] = ['elt', $endTime];
            foreach ($datas as $key=>$data) {
                $incomeWhere['cid'] = $data['id'];
                $income = $daoLength->field('uid,sum(bean) as bean,sum(coin) as coin')->where($incomeWhere)->group('uid')->select();
                $datas[$key]['intBean'] = $datas[$key]['intCoin'] = 0;
                if($income) {
                    foreach($income as $k=>$v) {
                        $datas[$key]['intBean'] += floor($v['bean']);
                        $datas[$key]['intCoin'] += floor($v['coin']);
                    }
                }
            }
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('公司ID','名称','主播数量','金币收益','金豆收益');
            foreach ($datas as $data) {
                $excel[] = array($data['id'],$data['name'],$data['companypeople'],$data['intCoin'],$data['intBean']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'公司统计表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->orderHash = $dao->getOrder();
        $this->display();
    }
    /*
    public function companylist(){
        $dao = D('Company');
        
        if($cid = I('get.cid')){
            $where['id'] = $cid;
        }
        if(!($startTime = I('get.timestart'))) {
            $_GET['timestart'] = $startTime = date('Y-m-01');
        }
		if(!($endTime = I('get.timeend'))) {
            $_GET['timeend'] = $endTime = date('Y-m-d');
        }
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->where($where)
            ->order('id desc')
            ->getField('id,name,status');
            $ids = array_keys($results);
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id desc')
            ->getField('id,name,status');
            $ids = array_keys($results);
        }
        $getCompanyPeople = \HP\Op\Company::getCompanyPeople($ids);
        $getCompanyIncome = \HP\Op\Company::getCompanyIncome($ids, $startTime, $endTime);
        foreach ($results as $result ){
            $data = $result;
            $data['companypeople'] = $getCompanyPeople[$result['id']];
            $data['bean'] = isset($getCompanyIncome[$result['id']]['bean']) ? $getCompanyIncome[$result['id']]['bean'] : 0;
            $data['coin'] = isset($getCompanyIncome[$result['id']]['coin']) ? $getCompanyIncome[$result['id']]['coin'] : 0;
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('公司ID','名称','主播数量','金币收益','金豆收益');
            foreach ($datas as $data) {
                $excel[] = array($data['id'],$data['name'],$data['companypeople'],$data['coin'],$data['bean']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'公司统计表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->display();
    }
    */
    
    public function company(){
        $dao = D('Company');
        if($cid = I('get.cid')){
            $where['id'] = $cid;
        }
        if(I('get.status')!=='-1'){
            $status = I('get.status');
            if(!isset($_GET['status'])){
                $_GET['status']='-1';
            }else{
                $where['status'] = $status;
            }
        }
        if(I('get.type')!=='-1'){
            $status = I('get.type');
            if(!isset($_GET['type'])){
                $_GET['type']='-1';
            }else{
                $where['type'] = $status;
            }
        }
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->field('*')
            ->where($where)
            ->select();
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id ')
            ->getField('id,name,type,rate,status');
            $ids = array_keys($results);
        }
        
        $type = $dao->getType();
        $status = $dao->getStatus();
        foreach ($results as $result ){
            $data = $result;
            $data['type'] = $type[$result['type']];
            $data['status'] = $status[$result['status']];
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $bank = D('bankBackend')->getField('id,name');
            $excel[] = array('公司ID','名称','类型','当前比率','当前税率','银行','银行卡号','开户行','姓名','身份证号','状态');
            foreach ($datas as $data) {
                $excel[] = array($data['id'],$data['name'],$data['type'],$data['rate'],$data['txtrate'],$bank[$data['bankid']],$data['cardid'],$data['bankaddress'],$data['ownername'],"\t".$data['papersid'],$data['status']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'公司列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->typeHash = $dao->getType();
        $this->statusHash = $dao->getStatus();
        $this->companyUrl = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['company-domain'];
        $this->display();
    }
    
    public function companysave(){
        $dao = D('Company');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        
        if(IS_POST){
            //$name = I('post.name', '');
            $rate = is_numeric(I('post.rate'))?I('post.rate'):BASE_RATE;
            if($dao->create()){
                if($id){
					$before = $dao->find($id);
					if($before['rate'] !=$rate){
						Company::afterCompanyRateChange(array('id'=>$id,'brate'=>$before['rate'],'arate'=>$rate,'adminid'=>Admin::getUid()));
					}
                    $save = [
                        'name' => I('post.name', ''),
                        'rate' => $rate,
                        'txtrate' => I('post.txtrate'),
                        'bankid' => I('post.bankid'),
                        'bankaddress' => I('post.bankaddress'),
                        'cardid' => I('post.cardid'),
                        'ownername' => I('post.ownername'),
                        'papersid' => I('post.papersid'),
                        'phone' => I('post.phone'),
                        'address' => I('post.address')
                        ];
                    $res = $dao->where('id='.$id)->save($save);
                }else{
                    $res = $dao->add();
                }
                if(false !==$res){
                    return $this->success('操作成功!',U('company/company'));
                }else{
                    return $this->error('操作失败!');
                }
            }else{
                return $this->error($dao->getError());
            }
        }
        $this->bank = D('bankBackend')->where(['status'=>0])->order('name')->getField('id,name');
        $assign = $id?$dao->find($id):[];
        $this->assign($assign);
        $this->typeHash = $dao->getType();
        $this->statusHash = $dao->getStatus();
        $this->display();
    }

    function bankbackend()
    {
        $Dao = D('bankBackend');        
        if(IS_AJAX){
            if(I('get.act')=='del'){
                $data = [];
                $id = I('post.id');
                $where['id'] = $id;
                $data['status'] = 1;                
                if(!($Dao->where($where)->save($data))) {
                    $data['status'] = 0;    
                }                
                return $this->ajaxReturn($data);
            }elseif(I('get.act')=='save'){
                if($name=I('post.name')){
                    $where['name'] = $name;
                    $bank = $Dao->where($where)->find();
                    
                    if($id=I('post.id') and is_numeric($id)){
                        if($bank && $bank['id'] != $id && $bank['status'] == 0) {
                            return $this->ajaxReturn(['status'=>0, 'msg'=>'银行名称重复']);
                        }
                        $Dao->where('id='.$id)->save(['name'=>$name]);
                    }else{
                        if($bank) {
                            if($bank['status'] == 0) {
                                return $this->ajaxReturn(['status'=>0, 'msg'=>'银行名称重复']);
                            } else {
                                $Dao->where('id='.$bank['id'])->save(['status'=>0]);
                            }
                        } else {
                            $Dao->add(['name'=>$name]);
                        }
                    }
                    $res = !$Dao->getError();
                    return $this->ajaxReturn(['status'=>($res?1:0)]);
                }
            }
        }        
        $this->data = $Dao->where(['status'=>0])->order('id desc')->select();
        $this->display();
    }

}
