<?php

namespace Admin\Controller;

use \HP\Op\Recommend;
use \HP\Op\Company;
use HP\Op\Game;

class RecommendController extends BaseController{

    protected $pageSize = 10;

    protected function _access(){
        return [
            'anchorlist' => ['anchorlist'],
            'recommendsave' => ['anchorlist'],
            'anchorlivesave' => ['anchorlist'],
            'game' => ['game'],
        ];
    }
    /*
     * 从列表中选取设置为待推荐
     * 从待推荐中选取 设置为首页推荐
     * zwq add 2017年5月8日
     */
    
    public function anchorlist(){
        $anchorDao = D('anchor');
        $ouids = \HP\Op\Recommend::getInfo(2);
        $adminRecommendLive = D('AdminRecommendLive');
        $userstaticDao = D('userstatic');
        if($ouids){
            $where['a.uid'][] = ['not in',$ouids];
        }
        if($id = I('get.uid')){
            $where['a.uid'][] = $id;
        }
        if($username = I('get.username')){
            $where['c.username'] = ['like',"%$username%"];
        }
        if($nick = I('get.nick')){
            $where['c.nick'] = ['like',"%$nick%"];
        }
        if($phone = I('get.phone')){
            $where['c.phone'] = ['like',"%$phone%"];
        }
        if(I('get.yesno') !='-1'){
            $yesno = I('get.yesno');
            $yesno==1?$where['b.uid'] = ['EXP','IS NOT NULL']:$where['b.uid'] = ['EXP','IS NULL'];
        }
        
        if($export = I('get.export')){//导出数据
            $results = $anchorDao
            ->alias(' a ')
            ->join(" left join ".$adminRecommendLive->getTableName()." as b on a.uid = b.uid and b.status = 0 ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)
            ->order('a.uid desc')
            ->field('a.uid,c.nick,c.username,a.cid,b.status')
            ->select();
        }else{
            $count = $anchorDao
            ->alias(' a ')
            ->join(" left join ".$adminRecommendLive->getTableName()." as b on a.uid = b.uid  and b.status = 0 ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)->count();
            
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $anchorDao
            ->alias(' a ')
            ->join(" left join ".$adminRecommendLive->getTableName()." as b on a.uid = b.uid and b.status = 0  ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.uid desc')
            ->field('a.uid,c.nick,c.username,a.cid,b.status')
            ->select();
            $uids = array_column($results, 'uid');
        }
        $type = D('Company')->getType();
        $companymap = \HP\Op\Company::getCompanymap();
        $liveinfo = \HP\Op\Live::liveInfo($uids);
        foreach ($results as $result ){
            $data = $result;
            $data['status_str'] = is_null($result['status'])?'未推荐':'已推荐';
            $data['live_str'] = $liveinfo[$data['uid']]?'直播中':'未直播';
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('主播ID','主播昵称','是否已推荐');
            foreach ($datas as $data) {
                $excel[] = array($data['uid'], $data['nick'],$data['status_str']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'主播推荐列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->yesno = [1=>'已推荐',2=>'未推荐'];
        $this->display();
    }
    
    
    //首页直播推荐，zwq 2017年5月11日 add
    public function anchorlive(){
        $dao = D('RecommendLive');
        $message= ['status'=>1,'msg'=>'操作成功'];
        if(IS_POST){//排序
            $sort = I('post.sort');
            $cline = I('post.clinet');
            $dao->where(['client'=>2])->save(['list'=>$sort]);
            return $this->ajaxReturn($message);
        }
        $uids = \HP\Op\Recommend::getInfo(2);
        $userinfo = \HP\Op\Anchor::anchorInfo($uids);
        $liveinfo = \HP\Op\Live::liveInfo($uids);
        foreach ($uids as $uid){
            $data = [];
            $data['uid'] = $uid;
            $data['pic'] = $userinfo[$uid]['pic'];
            $data['nick'] = $userinfo[$uid]['nick'];
            $data['islive'] = $liveinfo[$uid]?'直播中':'未直播';
            $datas[] = $data;
        }
        $this->data = $datas;
        $this->display();
    }
    
    //首页推荐 删除和增加
    public function anchorlivesave(){
        $dao = D('RecommendLive');
        $message= ['status'=>0,'msg'=>'操作失败'];
        $uids = \HP\Op\Recommend::getInfo(2);
        if(IS_POST){//增加或删除
            $client = I('post.client')?I('post.client'):2;
            I('post.list')?$postuids = explode(',', I('post.list')):$this->ajaxReturn($message);
            switch (I('post.act')) {
                case 'add'://增加：判断是否超过数量
                    $data['client'] = $client;
                    $data['ctime'] = $data['utime'] = get_date();
                    $uids = array_merge($uids,$postuids);
                    $list = array_unique((array)array_merge($uids,$postuids));
                    if(count($list) > LIVE_RECOMMENT_NUMBER){
                        $message['msg'] = '超过最大主播数量:'.LIVE_RECOMMENT_NUMBER;
                        $this->ajaxReturn($message);
                    }
                    $data['list'] = implode(',', $list) ;
                    $dao->add($data,'',true);
                    $message['status'] = 1;
                    $message['msg'] = '添加成功！';
                break;
                case 'del'://删除：替换字符串
                    $data['client'] = $client;
                    $data['ctime'] = $data['utime'] = get_date();
                    $uids = array_diff($uids, $postuids);//找到要删除的
                    $data['list'] = implode(',', $uids) ;
                    $dao->add($data,'',true);
                    $message['status'] = 1;
                    $message['msg'] = '添加成功！';
                break;
                
                default:
                    ;
                break;
            }
        }
        return $this->ajaxReturn($message);
    }
    
    
    /*
     * 设置签约公司
     * zwq add 2017年5月11日
     */
    public function recommendsave(){
        $dao = D('AdminRecommendLive');
        $message= ['status'=>1,'msg'=>'操作成功'];
        if(IS_POST){
            $id = is_numeric(I('post.uid'))?I('post.uid'):null;
            $type = I('post.type');
            if($type=='unset'){
                $dao->delete($id);
            }elseif($type=='set'){
                if($dao->create()){
                    $data = $dao->data();
                    $anchorInfo = \HP\Op\Anchor::anchorInfo([$id]);
                    $data['ctime'] = get_date();
                    $data['status'] = 0;
                    $data['adminid'] = \HP\Op\Admin::getUid();
                    $data['nick'] = $anchorInfo[$id]['nick'];
                    $data['head'] = $anchorInfo[$id]['pic'];
                    $dao->add($data,'',true);
                }else{
                    $message['status'] = 0;
                    $message['msg'] = '操作失败';
                }
            }else{
            }
        }
       return $this->ajaxReturn($message);
    }
    
    public function game(){
        if(IS_POST){
            $type = I("post.type");
            $gameid = I("post.gameid");
            $num = I("post.num")?I("post.num"):0;
            if($type && $gameid )$res = Game::updateRecommend($type,$gameid,$num);
            if($res){
                $msg = ["status"=>1,'info'=>'操作成功'];
            }else{
                $msg = ["status"=>0,'info'=>'操作失败'];
            }
            $this->ajaxReturn($msg);
        }
        $games = Game::getGames();
        $recommendGame = Game::getRecommendGame();
        $formatRecommendGame = Game::formatRecommendGame($recommendGame);
        $this->games = $games;
        $this->formatRecommendGame = $formatRecommendGame;
        $this->recommendType = Game::$recommendType;
        $this->floor_recommend_num = [1,2,3,4,5,6];
        $this->floor_num = [1,2,3];
        $this->navi_recommend_num = [3,6,9,12];
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }


    public function appgame(){
		if(IS_POST){
			$type = I("post.type")?I("post.type"):'';
			$gameid = I("post.gameid")?I("post.gameid"):'';
			$num = I("post.num")?I("post.num"):0;
			$isExist=Game::checkIsExistByType($type);
			if($isExist){
				$res = Game::updateRecommend($type,$gameid,$num);
			}else{
				$res=Game::addRecomgameByType($type,$gameid,$num);
			}
			if($res){
				$msg = ["status"=>1,'info'=>'操作成功'];
			}else{
				$msg = ["status"=>0,'info'=>'操作失败'];
			}
			$this->ajaxReturn($msg);
		}
		$games = Game::getGames();
		$recommendGame = Game::getRecommendGame();
		$formatRecommendGame = Game::formatRecommendGame($recommendGame);
//		dump($formatRecommendGame[4]);
//		if($formatRecommendGame[4]){
//			$formatRecommendGame[4]=array('recommendNumber'=>2);
//		}
		$this->games = $games;
		$this->formatRecommendGame = $formatRecommendGame;
		$this->recommendType = Game::$recommendType;
		$this->floor_recommend_num = [1,2,3,4,5,6,7,8];
		$this->floor_num = [0,1,2,3,4,5,6,7,8];
		$this->double_num = [0,1,2,3,4,5,6,7,8];
		$this->navi_recommend_num = [3,6,9,12];
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->display();
	}

}
