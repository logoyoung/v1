<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年8月30日
 * Time: 下午5:14:39
 * Desc: H5投票活动服务类
 */
namespace service\due;

use lib\due\DueVote;
use service\common\UploadImagesCommon;

class DueVoteActivity
{
    const USER_LEVEL_PIC = 'userLevelPic';
    
    public $vote = null;
    public function __construct(){
        if(is_null($this->vote)){
            $this->vote = new DueVote();
        }
    }
    //是否已投票
    public function isVote(int $uid,int $activity_id):bool{
        $data = $this->vote->_isVote($uid,$activity_id);
        return $data[0]['voteNum'] == 0 ? true : false;
    }
    //是否已生成活动 如果没有生成则生成
    public function isHasActivity($activity,$hero){
        return $this->vote->checkActivity($activity,$hero);
    } 
    //投票
    public function vote($uid,$activity_id,$hero_id){
        return $this->vote->_vote($uid,$activity_id,$hero_id);
    }
    //上传报名等级图片
    public function uploadLevelImg(int $uid){
        return UploadImagesCommon::uploadImage($uid,self::USER_LEVEL_PIC);
    }
    //是否已经报名
    public function isEnroll(int $uid,int $game_id,int $activity_id){
        return $this->vote->_isEnroll($uid,$game_id,$activity_id);
    }
    //报名
    public function enroll(int $uid,array $data){
        return $this->vote->enroll($uid,$data);
    }
    //获取活动 英雄投票总数
    public function returnVoteNums(int $activity_id){
        return $this->vote->getVoteNums($activity_id);
    }
} 
