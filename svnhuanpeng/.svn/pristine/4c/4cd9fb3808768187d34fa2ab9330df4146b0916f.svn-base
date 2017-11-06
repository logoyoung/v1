<?php

namespace Admin\Controller;


class ChannelController extends BaseController{

    protected $pageSize = 10;
    public function _access()
    {
        return [
           'channelsave' => ['channel'],
           'promocodesave' => ['promocode'],
           'applist' => ['applist'],
           'appsave' => ['appsave'],
           'appview' => ['applist'],
        ];
    }
    
    
    public function channel(){
        $dao = D('ChannelVersion');
        $this->status =$dao->getStatus();
        $this->vips =$dao->getVips();
        $this->client =$dao->getClient();
        if($channel = I('get.channel')){//渠道编号
            $where['channel'] = $channel;
        }
        if($channelname = I('get.channelname')){//渠道编号
            $where['channelName'] = ["like","%$channelname%"];
        }
        if(I('get.status')!=='-1'){
            $status = I('get.status');
            if(!isset($_GET['status'])){
                $_GET['status']='-1';
            }else{
                $where['status'] = $status;
            }
        }
        
        if(I('get.vip')!=='-1'){
            $vip = I('get.vip');
            if(!isset($_GET['vip'])){
                $_GET['vip']='-1';
            }else{
                $where['vip'] = $vip;
            }
        }
        
        if(I('get.client')!=='-1'){
            $client = I('get.client');
            if(!isset($_GET['client'])){
                $_GET['client']='-1';
            }else{
                $where['client'] = $client;
            }
        }
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->where($where)
            ->order('channel ')
            ->select();
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            
            $results = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('channel ')
            ->select();
        }
        
        foreach ($results as $result ){
            $data = $result;
            $data['client'] = $this->client[$result['client']];
            $data['status'] = $this->status[$result['status']];
            $data['vip'] = $this->vips[$result['vip']];
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('渠道编号','名称','类型','状态','重点合作','版本号','构建号','创建时间','描述');
            foreach ($datas as $data) {
                $excel[] = array($data['channel'],$data['channelname'],$data['client'],$data['status'],$data['vip'],$data['version'],$data['build'],$data['ctime'],$data['desc']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'渠道列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->display();
    }
    
    public function channelsave(){
        $dao = D('ChannelVersion');
        $id = is_numeric(I('get.channel'))?I('get.channel'):null;
        if(IS_POST){
            if($dao->create()){
                if($id){
                    $res = $dao->where('channel='.$id)->save();
                }else{
                    $res = $dao->add();
                }
                return $this->success('操作成功!',U('channel/channel'));
            }else{
                return $this->error($dao->getError());
            }
        }
        $assign = $id?$dao->where(["channel"=>$id])->find():[];
        $this->assign($assign);
        $this->hashclient = $dao->getClient();
        $this->hashstatus = $dao->getStatus();
        $this->hashvips = $dao->getVips();
        $this->display();
    }
    
    public function promocode(){
        $dao = D('Promocode');
        $this->status =$dao->getStatus();
        $this->vips =$dao->getVips();
        
        if($promocode = I('get.promocode')){//渠道编号
            $where['promocode'] = ["like","%$promocode%"];
        }
        $status = I('get.status', -1);
        if($status >= 0) {
            $where['status'] = $status;
        } else {
            $_GET['status']='-1';
        }
        $vip = I('get.vip', -1);
        if($vip >= 0) {
            $where['vip'] = $vip;
        } else {
            $_GET['vip']='-1';
        }
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->where($where)
            ->order('ctime desc')
            ->select();
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            
            $results = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('ctime desc')
            ->select();
        }
        
        foreach ($results as $result ){
            $data = $result;
            $data['status'] = $this->status[$result['status']];
            $data['vip'] = $this->vips[$result['vip']];
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
        	$excel[] = array('推广码','名称','状态','重点合作','创建时间','描述');
            foreach ($datas as $data) {
            	$excel[] = array($data['promocode'],$data['name'],$data['status'],$data['vip'],"\t".$data['ctime'],$data['desc']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'推广码列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->companyUrl = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['company-domain'];
        $this->display();
    }
    
    public function promocodesave(){
        $dao = D('Promocode');
        $promocode = I('get.promocode');
        if(IS_POST){
            if($dao->create()){
                if($promocode){
                    $res = $dao->where(['promocode'=>$promocode])->save();
                }else{
                    $res = $dao->add();
                }
                return $this->success('操作成功!',U('channel/promocode'));
            }else{
                return $this->error($dao->getError());
            }
        }
        $assign = $promocode ? $dao->where(["promocode"=>$promocode])->find() : [];
        $this->assign($assign);
        $this->hashstatus = $dao->getStatus();
        $this->hashvips = $dao->getVips();
        $this->display();
    }
    
    public function appsave(){
        $dao = D('admin_app');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        if(IS_POST){
            $message = ['status'=>0,'info'=>'操作失败'];
            $data = $dao->create();
            if($id){
                $data['udate']= get_date();
                $data['uuid']= get_uid();
                $res = $dao->where('id='.$id)->save($data);
            }else{
                $data['cdate'] = $data['udate'] = get_date();
                $data['cuid']= get_uid();
                $res = $dao->add($data);
            }
            if($res){
                $message['status'] = 1;
                $message['info'] = '操作成功';
            }
            return $this->ajaxReturn($message);
        }
        $assign = $id?$dao->find($id):[];
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->assign($assign);
        $this->display();
    
    }
    public function appview(){
        $dao = D('admin_app');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        $assign = $id?$dao->find($id):[];
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->assign($assign);
        $this->display();
    
    }
    
    public function applist(){
        $dao = D('admin_app');
        if($channel = I('get.channel')){
            $where['channel_id'] = $channel;
        }
        if($name = I('get.name')){
            $where['name'] = ["like","%$name%"];
        }
        if($version = I('get.version')){
            $where['version'] = ['like',"%$version%"];
        }
        if($appname = I('get.app_name')){
            $where['app_name'] = ['like',"%$appname%"];
        }
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->where($where)
            ->order('id desc ')
            ->select();
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id  desc ')
            ->select();
        }
    
        $users = D("admin_acl_user")->getField("uid,realname");
        foreach ($results as $result){
            $data = $result;
            $data['cuname'] = $users[$data['cuid']];
            $datas[] = $data;
        }
    
        if($export = I('get.export')){//导出数据
            $excel[] = array('游戏ID','名称','类型','描述');
            foreach ($datas as $data) {
                $excel[] = array($data['gameid'],$data['name'],$data['type'],$data['description']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }
}
