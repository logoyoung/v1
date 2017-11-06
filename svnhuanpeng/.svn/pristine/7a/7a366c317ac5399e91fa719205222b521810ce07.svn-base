<?php
/**
 * Created by PhpStorm.
 * User: shijiantao
 * Date: 2017/9/5
 * Time: 10:55
 */

namespace Admin\Controller;

class VoteactivityController extends BaseController
{

    protected $pageSize = 20;

    protected function _access()
    {
        return [
            'votelog' => ['index'],
            'enroll' => ['index'],
        ];
    }

    function index()
    {
        $where = [];
		$where['status'] = 1;
        if($game_id = I('get.game_id')){
            $where['game_id'] = $game_id;
        }
        if($aid = I('get.aid')){
            $where['aid'] = $aid;
        }
        if($activity = I('get.activity')) {
            $where['activity'] = ['like', "%$activity%"];
        }
        if(I('get.stime') && I('get.etime')) {
            $con['_logic'] = 'or';
        }
        if($stime = I('get.stime')){
            $con['etime'] = ['egt',$stime . ' 00:00:00'];
        }
        if($etime = I('get.etime')){
            $con['stime'] = ['elt',$etime . ' 23:59:59'];
        }
		if($con) {
            $where['_complex'] = $con;
        }
		$Dao = D('voteActivity');
		if(I('get.export')){//导出数据
        	$results = $Dao->where($where)->order('activity_id desc')->select();
        }else{
            $count = $Dao->where($where)->count();
			
            $Page = new \HP\Util\Page($count, $this->pageSize);
            $results = $Dao
                ->where($where)
                ->order('activity_id desc')
                ->limit($Page->firstRow, $Page->listRows)
                ->select();
        }
		if($results) {
			$gameids = $Dao->distinct(true)->field('game_id')->select();
			$game = D('game')->where(['gameid'=>['in',array_column($gameids, 'game_id')]])->getField('gameid, name');
			foreach($results as $k=>$v) {
				$results[$k]['game_name'] = $game[$v['game_id']];
			}
		}
		if($export = I('get.export')){//导出数据
            $excel[] = array('活动ID','活动名称','游戏ID','游戏名称','详细信息','开始时间','结束时间');
            foreach ($results as $data) {
            	$excel[] = array($data['activity_id'],$data['activity'],$data['game_id'],$data['game_name'],$data['desc'],"\t".$data['stime'],"\t".$data['etime']);
            }
            \HP\Util\Export::outputCsv($excel, '投票活动列表');
        }
		$this->games = $game;
		$this->data = $results;
        $this->page = $Page->show();
        $this->allactivity = D('voteActivity')->where(['status'=>1])->order('activity_id desc')->getField('activity_id,activity');
        $this->display();
    }

    function votelog()
    {
        if(!$aid = I('get.aid')){
            $this->error('缺少活动ID');
        }
        $condition['activity_id'] = $aid;
		$activity = D('voteActivity')->where($condition)->find();
        $voteNums= D('voteNums')->where($condition)->getField('hero_id,hero,img,bgimg,nums');
        if($voteNums) {
            foreach($voteNums as $k=>$v) {
                $voteNums[$k]['img'] = getPic($v['img']);
                $voteNums[$k]['bgImg'] = getPic($v['bgImg']);
            }
        }

        $where = $condition;
        if($uid = I('get.uid')) {
            $where['uid'] = $uid;
        }
		if($hero_id= I('get.hero_id')) {
            $where['hero_id'] = $hero_id;
        }
        if($stime = I('get.stime')){
            $where['utime'][] = ['egt',$stime . ' 00:00:00'];
        }
        if($etime = I('get.etime')){
            $where['utime'][] = ['elt',$etime . ' 23:59:59'];
        }
        $Dao = D('voteLog');
        if(I('get.export')){//导出数据
            $results = $Dao->where($where)->order('id desc')->select();
        }else{
            $count = $Dao->where($where)->count();

            $Page = new \HP\Util\Page($count, $this->pageSize);
            $results = $Dao
                ->where($where)
                ->order('id desc')
                ->limit($Page->firstRow, $Page->listRows)
                ->select();
        }
        if($results) {
            $users = D('userstatic')->where(['uid'=>['in',array_column($results, 'uid')]])->getField('uid,nick,pic');
            foreach($results as $k=>$v) {
                $results[$k]['nick'] = $users[$v['uid']]['nick'];
                $results[$k]['avator'] = avator($users[$v['uid']]['pic']);
                $results[$k]['hero'] = $voteNums[$v['hero_id']]['hero'];
            }
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('用户ID','昵称','英雄','时间');
            foreach ($results as $data) {
                $excel[] = array($data['uid'],$data['nick'],$data['hero'],"\t".$data['utime']);
            }
            \HP\Util\Export::outputCsv($excel, $activity['activity'].'投票详情');
        }

        $this->voteNums = $voteNums;
        $this->activity = $activity;
        $this->data = $results;
        $this->page = $Page->show();
		$this->allactivity = D('voteActivity')->where(['status'=>1])->order('activity_id desc')->getField('activity_id,activity');
        $this->display();
    }

    function enroll()
    {
        if(!$aid = I('get.aid')){
            $this->error('缺少活动ID');
        }
        $condition['activity_id'] = $aid;
        $activity = D('voteActivity')->where($condition)->find();

        $where = $condition;
        if($uid = I('get.uid')) {
            $where['uid'] = $uid;
        }
        if($stime = I('get.stime')){
            $where['ctime'][] = ['egt',$stime . ' 00:00:00'];
        }
        if($etime = I('get.etime')){
            $where['ctime'][] = ['elt',$etime . ' 23:59:59'];
        }
        $Dao = D('enroll');
        if(I('get.export')){//导出数据
            $results = $Dao->where($where)->order('id desc')->select();
        }else{
            $count = $Dao->where($where)->count();

            $Page = new \HP\Util\Page($count, $this->pageSize);
            $results = $Dao
                ->where($where)
                ->order('id desc')
                ->limit($Page->firstRow, $Page->listRows)
                ->select();
        }
        if($results) {
            $users = D('userstatic')->where(['uid'=>['in',array_column($results, 'uid')]])->getField('uid,nick,pic');
            foreach($results as $k=>$v) {
                $results[$k]['nick'] = $users[$v['uid']]['nick'];
                $results[$k]['avator'] = avator($users[$v['uid']]['pic']);
            }
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('用户ID','昵称','QQ','游戏昵称','等级','报名时间');
            foreach ($results as $data) {
                $excel[] = array($data['uid'],$data['nick'],"\t".$data['qq'],$data['game_nick'],$data['level'],"\t".$data['ctime']);
            }
            \HP\Util\Export::outputCsv($excel, $activity['activity'].'报名详情');
        }

        $this->activity = $activity;
        $this->data = $results;
        $this->page = $Page->show();
		$this->allactivity = D('voteActivity')->where(['status'=>1])->order('activity_id desc')->getField('activity_id,activity');
        $this->display();
    }
}
