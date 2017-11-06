<?php

namespace Admin\Controller;
use HP\Log\Log;
use \HP\Op\Game;
use HP\Op\Check;
use HP\Op\Admin;
use HP\Op\publicRequist;
use HP\Cache\CacheKey;
use HP\Op\Live;
use HP\Util\Room;


class CheckController extends BaseController{

    protected $pageSize = 10;
    public function _access()
    {
        return [
           'headpic' => ['headpic'],
           'headpicpass' => ['headpic'],
           'headpicunpass' => ['headpic'],
           'headpicptou' => ['headpic'],
           'realname' => ['realname'],
           'realnamepass' => ['realname'],
           'realnameunpass' => ['realname'],
           'video' => ['video'],
           'videopass' => ['video'],
           'videounpass' => ['video'],
		   'videodetail' => ['video'],
           'bulletincheck' => ['livebulletin'],
           'livetitlecheck' => ['livetitle'],
           'videocommentcheck' => ['videocomment'],
           'checkapply' => ['apply'],
           'clearfailcount' => ['apply'],
        ];
    }
    
    //头像审核列表
    public function headpic(){
        //dump(S(CacheKey::DIFF_CHECK_USERPIC));
        $dao = D('Userstatic');
        $dao2 = D('UserPic');
        if($uid = I('get.uid')){
            $where['a.uid'] = $uid;
        }
        if($nick = I('get.name')){
            $where['nick'] = ['like',"%$nick%"];
        }
        
        isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']=USER_PIC_AUTO_PASS;
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['a.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}


        if(I('get.status')!='-1'){
			$status = I('get.status');
			$where['a.status'] = $status;
			/*if($status == '0'){
				$where['a.status'] = USER_PIC_WAIT;
				$where['a.status'] = USER_PIC_AUTO_PASS;
				$where['a.status'] = USER_PIC_AUTO_UNPASS;
				$where['_logic'] = 'or';
				$map['_complex'] = $where;
			}else{
				$where['a.status'] = $status;
			}*/

            /*if(I('get.status')==-2){//未上传
                $where['b.status'] = ["exp","is null"];
            }*/
        }
        
        
        if($export = I('get.export')){//导出数据
            $datas = $dao2
            ->alias('a')
            ->join(" left join ".$dao->getTableName()." b  on a.uid = b.uid ")
            ->where($where)
            ->field("b.nick,a.*")
            ->order('a.ctime desc ')
            ->select();
        }else{
            //获取被其他adminuid锁定的uids============
            /*if(I('get.status')==USER_PIC_WAIT)$diffids = Check::getdiffuid(CacheKey::DIFF_CHECK_USERPIC);
            if($diffids) $where['a.uid'] =["not in",$diffids]; */
            $count = $dao2
            ->alias('a')
            ->join(" left join ".$dao->getTableName()." b on a.uid = b.uid  ")
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            
            $datas = $dao2
            ->alias('a')
            ->join(" left join ".$dao->getTableName()." b  on a.uid = b.uid ")
            ->where($where)
            ->field("b.nick,a.*")
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.ctime desc  ')
            ->select();
        }
		$checkstatus = $dao2->getCheckstatus2();
        if($export = I('get.export')){//导出数据
            $excel[] = array('用户ID','昵称','头像','提交时间','审核时间','审核状态');
            foreach ($datas as $data) {
                $excel[] = array($data['uid'],$data['nick'],$data['pic'],$data['ctime'],$data['utime'],$checkstatus[$data['status']]);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'头像审核列表');
        }

        /*$uids = array_column($datas, "uid");
        //设置被锁定的uid============
        if(I('get.status')==USER_PIC_WAIT)Check::initdiffuid(CacheKey::DIFF_CHECK_USERPIC,$uids);*/
        $this->data = $datas;
        $this->page = $Page->show();
        $this->checkstatus = $checkstatus;
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }
    
    //审核通过
    function headpicpass(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $ids = I('post.ids');
            $pics = I('post.pics');
            $ids = explode(',', $ids);
            $pics = explode(',', $pics);
            $datas = array_combine($ids, $pics);
            $res = Check::headPicPass($datas);
            if($res){
                $msg['status'] = 1;
                $msg['info'] = "操作成功";
				publicRequist::askDota(implode(',',$ids),110);//事件推送
            }
        }
        return $this->ajaxReturn($msg);
    }
    //审核拒绝
    function headpicunpass(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            $res = Check::headPicUnpass($ids);
            if($res){
                $msg['status'] = 1;
                $msg['info'] = "操作成功";
            }
        }
        return $this->ajaxReturn($msg);
    }
    
    //通过了又要变成拒绝
    function headpicptou(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            $res = Check::headPicpton($ids);
            if($res){
                $msg['status'] = 1;
                $msg['info'] = "操作成功";
            }
            return $this->ajaxReturn($msg);
        }
    }
    
    //实名认证审核
    function realname(){
        $staticDao = D('Userstatic');
        $realnameDao = D('Userrealname');
        $checkstatus = $realnameDao->getCheckstatus();
        if($uid = I('get.uid')){
            $where['a.uid'] = $uid;
        }
        if($name = I('get.name')){
            $where['name'] = ['like',"%$name%"];
        }
        if($nick = I('get.nick')){
            $where['nick'] = ['like',"%$nick%"];
        }
        if($phone = I('get.phone')){
            $where['phone'] = ['like',"%$phone%"];
        }
        
        isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']='1';
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['a.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}


        if(I('get.status')!='-1'){
            $where['a.status'] = I('get.status');
            if(I('get.status')==0){//待申请
                $where['a.status'] = ["exp","is null"];
            }
        }

        
        if($export = I('get.export')){//导出数据
            $datas = $realnameDao
            ->alias('a')
            ->join(" left join ".$staticDao->getTableName()." b  on a.uid = b.uid ")
            ->where($where)
            ->field("a.*,b.nick,b.phone")
            ->order('a.ctime desc ')
            ->select();
        }else{
            //获取被其他adminuid锁定的uids============
            /*if(I('get.status')==RN_WAIT)$diffids = Check::getdiffuid(CacheKey::DIFF_CHECK_REALNAME);
            if($diffids) $where['a.uid'] =["not in",$diffids];*/
            $count = $realnameDao
            ->alias('a')
            ->join(" left join ".$staticDao->getTableName()." b  on a.uid = b.uid ")
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            
            $datas = $realnameDao
            ->alias('a')
            ->join(" left join ".$staticDao->getTableName()." b  on a.uid = b.uid ")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("a.*,b.nick,b.phone")
            ->order('a.ctime desc ')
            ->select();
        }

        //获取手机、身份证、银行卡权限
		$phoneauth = \HP\Op\Admin::checkAccessWithKey('phoneauthkey');
		$certauth  = \HP\Op\Admin::checkAccessWithKey('certauthkey');

        foreach ($datas as &$data){
			$data['realpapersid'] = $data['papersid'];
			if(!$phoneauth) $data['phone'] = get_secure_phone($data['phone']);
			if(!$certauth) $data['papersid'] = get_secure_cert($data['papersid']);
		}
        if($export = I('get.export')){//导出数据
            $excel[] = array('uid','昵称','姓名','手机号','身份证号','提交时间','审核状态');
            foreach ($datas as $data) {
                $excel[] = array($data['uid'],$data['nick'],$data['name'],"\"{$data['phone']}\"","\t".$data['papersid'],"\t".$data['ctime'],$checkstatus[$data['status']]);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'实名认证列表');
        }

        $uids = array_column($datas, "uid");
        //设置被锁定的uid============
        /*if(I('get.status')==RN_WAIT)Check::initdiffuid(CacheKey::DIFF_CHECK_REALNAME,$uids);*/
        $this->data = $datas;
        $this->page = $Page->show();
        $this->checkstatus = $checkstatus;
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }
    
    
    //实名认证审核通过
    function realnamepass(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $ids = I('post.ids');
            $uids = I('post.uids');
            $ids = explode(',', $ids);
            $uids = explode(',', $uids);
            $datas = array_combine($ids, $uids);
            $res = Check::realnamePass($datas);
            if($res){
                $msg['status'] = 1;
                $msg['info'] = "操作成功";
				$callback=publicRequist::askDota(implode(',',$uids),330);//事件推送
				Log::statis('uid=='.implode(',',$uids).'====callback=='.$callback,'','dota_realnamepass');
            }
        }
        return $this->ajaxReturn($msg);
    }
    
    //实名认证审核拒绝
    function realnameunpass(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $ids = I('post.ids');
			$uids = I('post.uids');
			$reason= I('post.reason');
			if(empty($reason)){
				return $this->ajaxReturn(array('status'=>0,'info'=>'请输入拒绝的原因'));
			}
            $ids = explode(',', $ids);
			$uid = implode( ',', array_filter( explode( ',', str_replace( '，', ',', $uids ) ) ) );
            $res = Check::realnameUnpass($ids,$reason,$uid);
            if($res){
                $msg['status'] = 1;
                $msg['info'] = "操作成功";
            }
        }
        return $this->ajaxReturn($msg);
    }
    
    
    //录像审核
    function video(){
        $liveDao = D('live');
        $staticDao = D('Video');
        $realnameDao = D('WaitPassVideo');

        if($uid = I('get.uid')){
            $where['b.uid'] = $uid;
        }
        if($videoid = I('get.videoid')){
            $where['b.videoid'] = $videoid;
        }
        if($title = I('get.title')){
            $where['title'] = ['like',"%$title%"];
        }
        if($gamename = I('get.gamename')){
            $where['gamename'] = ['like',"%$gamename%"];
        }
    
        isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']='0';
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
        if(I('get.status')!='-1'){
            $where['a.status'] = I('get.status');
        }/*else{
			$where['a.status'] = ['!=',VIDEO_DEL];
		}*/
        if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['a.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}

        if($export = I('get.export')){//导出数据
            $results = $realnameDao
            ->alias('a')
            ->join(" left join ".$staticDao->getTableName()." b  on a.videoid = b.videoid ")
            ->where($where)
            //->field("b.*,a.gamename,a.title,a.vfile,a.uid,a.length,a.poster")
			->field("b.videoid,b.gamename,b.title,b.vfile,b.uid,b.length,b.poster,b.status,b.ctime as bctime,a.ctime as actime,a.status")
            ->order('a.ctime desc ')
            ->select();
        }else{
            //获取被其他adminuid锁定的uids============
            /* if(I('get.status')==RN_WAIT)$diffids = Check::getdiffuid(CacheKey::DIFF_CHECK_REALNAME);
            if($diffids) $where['a.uid'] =["not in",$diffids]; */
            $count = $realnameDao
            ->alias('a')
            ->join(" left join ".$staticDao->getTableName()." b  on a.videoid = b.videoid ")
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
    
            $results = $realnameDao
            ->alias('a')
            ->join(" left join ".$staticDao->getTableName()." b  on a.videoid = b.videoid ")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            //->field("b.*,a.gamename,a.title,a.vfile,a.uid,a.length,a.poster")
			->field("b.liveid,b.videoid,b.gamename,b.title,b.vfile,b.uid,b.length,b.poster,b.status,b.ctime as bctime,a.ctime as actime,a.status")
            ->order('a.ctime desc ')
            ->select();
            $liveids = array_column($results, 'liveid');
        }
    
        $liveids && $livetimes = $liveDao->where(['liveid'=>['in',$liveids]])->getField('liveid,stime');
        
        foreach ($results as $result){
            $data = $result;
            $data['length'] = secondFormat($data['length']);
            $data['poster'] = sposter($data['poster']);
            $data['vfile'] = sfile($data['vfile']);
            $data['livetime'] = $livetimes[$data['liveid']];
            $datas[] = $data;
        }
		$checkstatus = $realnameDao->getCheckstatus2();
        if($export = I('get.export')){//导出数据
            $excel[] = array('录像ID','用户ID','游戏名称','标题','录像时长','生成时间','发布时间','审核状态');
            foreach ($datas as $data) {
                $excel[] = array($data['videoid'],$data['uid'],$data['gamename']
				,$data['title'],$data['length'],$data['bctime'],$data['actime'],$checkstatus[$data['status']]);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏类型列表');
        }
        //设置被锁定的uid============
        //if(I('get.status')==RN_WAIT)Check::initdiffuid(CacheKey::DIFF_CHECK_REALNAME,$uids);
        $this->data = $datas;
        $this->reason = Live::getUnpassreson();
        $this->page = $Page->show();
        $this->checkstatus = $checkstatus ;
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }

    function videodetail(){
		$this->reason = Live::getUnpassreson();
		$this->display();
	}
    
    function videopass(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $vid = I('post.videoid');
            if( $vid ){
                $res = Check::vidoepass($vid, $opt);
                if($res)$msg = ['status'=>1,'info'=>'操作成功'];
            }
        }
        return $this->ajaxReturn($msg);
    }
    function videounpass(){
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $vid = I('post.videoid');
            $reasontype = I('post.reasontype');
            $reason = I('post.reason');
            $opt =['reasontype'=>$reasontype,'reason'=>$reason];
            if( $vid && $opt ){
                $res = Check::vidoeUnpass($vid, $opt);
                if($res)$msg = ['status'=>1,'info'=>'操作成功'];
            }
        }
        return $this->ajaxReturn($msg);
    }
    
    /**
     * 公告列表
     */ 
    function livebulletin() 
    {
        $Dao = D('livebulletin');
		$_GET['status'] = $_GET['status']?$_GET['status']:0;
        $status = I('get.status', -1);

        if($status >= 0){
            $where['status'] = $status;
        } else {
            $_GET['status'] = -1;
        }
        if($luid = I('get.luid')){
            $where['luid'] = $luid;
        }

		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d',strtotime('-1 month'));
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}

        if($export = I('get.export')){//导出数据
        
        } else {
            $count = $Dao->where($where)->count();
            $Page = new \HP\Util\Page($count,$_GET['export'] ? 0 : $this->pageSize);
            $this->data = $Dao->where($where)->order('status')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
        $this->page = $Page->show();
        $this->checkstatus = $Dao->getCheckstatus();
        $this->display();
    }
    
    /**
     * 公告审核
     */ 
    function bulletincheck()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        do{
            if(IS_POST){
                if($luid = I('post.luid')){
                    $where['luid'] = $luid;
                } else {
                    break;
                }
                if(!($status = I('post.status'))){
                    break;
                }
                $data = [
                    'admin_id' => \HP\Op\Admin::getUid(),
                    'utime' => date('Y-m-d H:i:s'),
                    'status' => $status
                    ];
                $res = D('livebulletin')->where($where)->save($data);
                if($res){
                    $msg['status'] = 1;
                    $msg['info'] = "操作成功";
                }
            }
        }while(false);
        return $this->ajaxReturn($msg);
    }    
    
    /**
     * 直播列表
     */ 
    function livetitle() 
    {
        //
        $Dao = D('live');
        $Dao_record = D('liveTitleCheckRecord');
        $titlestatus = I('get.titlestatus');
        if ($titlestatus != -1) { 
            if($titlestatus === ''){
                $_GET['titlestatus'] = $titlestatus = 0;
            }
            $where['a.titlestatus'] = $titlestatus;
        }
        $status = I('get.status');
        if ($status != -1) { 
            if($status === ''){
                $_GET['status'] = $status = 100;
            }
            if($status == 100) {
                $where['a.status'] = $status;
            } else {
                $where['a.status'] = ['neq', 100];
            }
        }
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');

        if($uid = I('get.uid')){
            $where['a.uid'] = $uid;
        }
		/*if($timestart = I('get.timestart')){
			$where['a.ctime'] = ['egt',"$timestart 00:00:00"];
		}
		if($timeend = I('get.timeend')){
			$where['a.ctime'] = ['elt',"$timeend 23:59:59"];
		}*/

		if(($timeend = I('get.timeend')) && ($timestart = I('get.timestart'))){
			$where['a.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}
        if($title = I('get.title')){
            $or['a.title'] = ['like', "%$title%"];
            $or['b.title'] = ['like', "%$title%"];
            $or['_logic'] = 'OR';
        }
        $where['a.stime'] = ['neq', '0000-00-00 00:00:00'];
        if($or) {            
            $where['_complex'] = $or;
            $where['_logic'] = 'and';
        }                
        
        if($export = I('get.export')){//导出数据
        
        } else {
            $count = $Dao->alias('a')
                ->join(" left join ".$Dao_record->getTableName()." b  on a.liveid = b.liveid ")
                ->where($where)
                ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
    
            $data = $Dao->alias('a')
                ->join(" left join ".$Dao_record->getTableName()." b  on a.liveid = b.liveid ")
                ->field('a.stime,a.uid,a.liveid,a.etime,a.title,a.status,a.titlestatus,a.title,b.title as btitle,b.ctime as bctime')
                ->where($where)
                ->order('a.titlestatus,stime desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
        
        if($data) {
            foreach($data as $k=>$v) {
                if($v['status'] != 100) {
                    $data[$k]['status'] = 200;
                }
                if($v['etime'] == '0000-00-00 00:00:00' ) {
                    $data[$k]['etime'] = '未结束';
                }
            }
        }
        $this->data = $data;
        $this->page = $Page->show();
        $this->checkstatus = $Dao->getCheckstatus();
        $this->livestatus = $Dao->getLivestatus();
        $this->display();
    }
    
    /**
     * 直播标题审核
     */ 
    function livetitlecheck()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        do{
            if(IS_POST){
                if($liveid = I('post.liveid')){
                    $where['liveid'] = ['in', $liveid];
                } else {
                    break;
                }
                if(!($titlestatus = I('post.titlestatus'))){
                    break;
                }
                $Dao = D('live');
                $Dao_record = D('liveTitleCheckRecord');
                $liveids = explode(',', $liveid);
                $adminid = \HP\Op\Admin::getUid();
                if($titlestatus == 2) {
                    foreach($liveids as $v) {
                        $con['liveid'] = $v;
                        $liveInfo = $Dao->field('liveid,uid,title,titlestatus')->where($con)->find();
                        $userD = D('userstatic');
                        $user = $userD->field('nick')->where(['uid'=>$liveInfo['uid']])->find();
                        $update = [
                            'title' => $user['nick'] . '的直播间',
                            'titlestatus' => 2
                        ];
                        $Dao->where($con)->save($update);
                        
                        $insert = [
                            'liveid' => $liveInfo['liveid'],
                            'title' => $liveInfo['title'],
                            'admin_id' => $adminid,
                            'status' => 2
                            ];
                        $res = $Dao_record->add($insert);
                    }
                } elseif($titlestatus == 1) {
                    $update = [
                        'titlestatus' => 1
                    ];
                    $res = $Dao->where($where)->save($update);
                    foreach($liveids as $v) {
                        $insert = [
                            'liveid' => $v,
                            'title' => '',
                            'admin_id' => $adminid,
                            'status' => 1
                            ];
                        $res = $Dao_record->add($insert);
                    }
                }
                if($res){
                    $msg['status'] = 1;
                    $msg['info'] = "操作成功";
                }
            }
        }while(false);
        return $this->ajaxReturn($msg);
    }
    
    /**
     * 录像评论列表
     */ 
    function videocomment() 
    {
        $Dao = D('videocomment');
		$_GET['status'] = $_GET['status']?$_GET['status']:3;
        $status = I('get.status', -1);
        if($status >= 0){
            $where['status'] = $status;
        } else {
            $_GET['status'] = -1;
        }
        if($uid = I('get.uid')){
            $where['uid'] = $uid;
        }
        if($videoid = I('get.videoid')){
            $where['videoid'] = $videoid;
        }

		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['tm'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}
        if($export = I('get.export')){//导出数据
            $results = $Dao->where($where)
                    ->limit('0,1000')
                    ->order('id desc')
                    ->select();
        } else {
            $count = $Dao->where($where)->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $Dao->where($where)
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->order('id desc')
                    ->select();
        }
        $checkstatus = $Dao->getCheckstatus();
        if($results) {
            foreach ($results as &$result){
                $result['link'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . '/videoRoom.php?videoid=' . $result['videoid'];
                $result['utime'] = $result['utime'] == '0000-00-00 00:00:00' ? '--' : $result['utime'];
            }
            
            if($export = I('get.export')){//导出数据
                $excel[] = array('录像ID','用户ID','评论时间','评论内容','审核状态','审核时间');
                foreach ($results as $data) {
                    $excel[] = array($data['videoid'],$data['uid'],$data['tm'],$data['comment'],$checkstatus[$data['status']],$data['utime']);
                }
                \HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏类型列表');
            }
        }
        $this->data = $results;
        $this->page = $Page->show();
        $this->checkstatus = $checkstatus;
        $this->display();
    }
    
    /**
     * 录像评论审核
     */ 
    function videocommentcheck()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        do{
            if(IS_POST){
                if($id = I('post.id')){
                    $where['id'] = ['in', $id];
                } else {
                    break;
                }
                if(!($status = I('post.status'))){
                    break;
                }
                $data = [
                    'admin_id' => \HP\Op\Admin::getUid(),
                    'utime' => date('Y-m-d H:i:s'),
                    'status' => $status
                    ];
                $res = D('videocomment')->where($where)->save($data);
                if($res){
                    $msg['status'] = 1;
                    $msg['info'] = "操作成功";
                }
            }
        }while(false);
        return $this->ajaxReturn($msg);
    }
    
    /**
     * 申请签约列表
     */
    function apply()
    {
        $Dao = D('anchorapplycompany');
        $Dao_user = D('userstatic');
        $checkStatus = $Dao->getCheckstatus();
        $liveStyle = $Dao->getLiveStyle();
        $company = \HP\Op\Company::getCompanymap();
        $status = I('get.status', -1);
        if($status >= 0) {
            $where['a.status'] = $status;
        }
        $_GET['status'] = $status;
        if($cid = I('get.cid')) {
            $where['cid'] = $cid;
        }
        if($uid = I('get.uid', 0)) {
            $where['a.uid'] = $uid;
        }
        if($nick = I('get.nick', 0)) {
            $where['b.nick'] = ['like', "%$nick%"];
        }
        if($timestart = I('get.timestart')){
        	$where['a.ctime'][] = ['egt', $timestart . ' 00:00:00'];
        }
        if($timeend = I('get.timeend')){
        	$where['a.ctime'][] = ['elt', $timeend . ' 23:59:59'];
        }
        $count = $Dao
            ->alias(' a ')
            ->join(" left join " . $Dao_user->getTableName() . " as b on a.uid = b.uid ")
            ->where($where)->count();
        $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
        
        $results = $Dao
            ->alias(' a ')
            ->join(" left join " . $Dao_user->getTableName() . " as b on a.uid = b.uid ")
            ->where($where)
            ->order('id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->field('a.*,b.nick,b.pic')
            ->select();
		$certauth  = \HP\Op\Admin::checkAccessWithKey('certauthkey');
        if($results) {
            $videoid = array_column($results, 'videoid');
            $video = D('video')->where(['videoid'=>['in', $videoid]])->getField('videoid,poster,vfile');
            $uid = array_column($results, 'uid');
            $user = D('userrealname')->where(['uid'=>['in', $uid]])->getField('uid,papersid,name');
            $gameid = array_column($results, 'gameid');
            if(!empty($gameid)) {
                $game = D('game')->where(['gameid' => ['in', $gameid]])->getField('gameid,name');
            }
            foreach($results as &$result) {
            	$result['pic'] = avator($result['pic']);
                $result['vfile'] = sfile($video[$result['videoid']]['vfile']);
                $result['poster'] = sposter($video[$result['videoid']]['poster']);
                $result['company'] = $company[$result['cid']]['name'];
				if($certauth)
                	$result['papersid'] = $user[$result['uid']]['papersid'];
				else
					$result['papersid'] = get_secure_cert($user[$result['uid']]['papersid']);
                $result['realname'] = $user[$result['uid']]['name'];
                $result['gamename'] = isset($game[$result['gameid']]) ? $game[$result['gameid']] : '';
                $result['showface'] = isset($liveStyle[$result['showface']]) ? $liveStyle[$result['showface']] : '';
            }
        }
        $this->data = $results;
        $this->checkStatus = $checkStatus;
        $this->company = $company;
        $this->page = $Page->show();
        $this->display();
    }
    
    /**
     * 签约审核
     */
    function checkapply()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        
        $id = I('post.id', 0);
        $Dao_apply = D('anchorapplycompany');
        $Dao_anchor = D('anchor');
        $info = $Dao_apply->where("id=$id")->find();
        $anchor = $Dao_anchor->where("uid=" . $info['uid'])->find();
        if(!$anchor) {
            $msg = ['status'=>0,'info'=>'主播不存在'];
            return $this->ajaxReturn($msg);
        }
        
        if($info['status'] == 2 && $anchor['cid'] == 0) {
            $status = I('post.status');
            if($status == 4 || $status == 5) {
                $reason = trim(I('post.reason', ''));
                if($status == 5 && $reason == '') {
                    $msg = ['status'=>0,'info'=>'提交失败！拒绝原因为必填项'];
                    return $this->ajaxReturn($msg);
                }
                $time = get_date();
                
                if($status == 4) { //通过更改比率
                    $companyinfos = \HP\Op\Company::getCompangInfo();
                    $update = [
                        'cid' => $info['cid'],
                        'utime' => $time,
                        'uid' => $info['uid'],
                        'rate' => $companyinfos[$info['cid']]['rate']
                        ]; 
                    \HP\Op\Anchor::setAnchorContract(0, $update);
                }

                $data = [
                    'status' => $status,
                    'admintime' => $time,
                    'adminid' => \HP\Op\Admin::getUid(),
                    'adminreason' => $reason
                    ];
                $res = $Dao_apply->where("id=$id")->save($data);
                if($res) {
                    $msg = ['status'=>1,'info'=>'操作成功'];
                }
                        
            }
        } else {
            $msg = ['status'=>0,'info'=>'状态已变，无法修改'];
        }
            
        return $this->ajaxReturn($msg);
    }   
    
    /**
     * 如果某一主播申请同一公司被管理员拒绝三次，经纪公司管理人员不能再同意此人的申请，除非管理员将申请次数置为-1
     */
    function clearfailcount()
    {
    	$msg = ['status'=>0,'info'=>'操作失败'];
    	$id = I('post.id', 0);
    	$Dao_apply = D('anchorapplycompany');
    	$res = $Dao_apply->where(['id'=>$id])->save(['failcount'=>-1]);
    	if($res) {
    		$msg = ['status'=>1,'info'=>'操作成功'];
    	}
    	
    	return $this->ajaxReturn($msg);
    }   
}
