<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\due;

use system\RedisHelper;
use lib\due\DueComment;
use service\due\DueOrderService;
use lib\due\DueTags;

class DueCommentService {

    public $libDueComment = null;
    public $_redis = null;
    public $redisConfig = 'huanpeng';

    public function getRedisCommentAverageCacheKey($skill_id) {
        return 'DueCommentService_CommentAverage_20170609_' . $skill_id;
    }

    public function getRedisListSetCacheKey() {
        return 'DueCommentService_ListSet_20170609';
    }

    /**
     * 获取redis资源
     * @return type
     */
    public function getRedis() {
        if (is_null($this->_redis)) {
            $this->_redis = RedisHelper::getInstance($this->redisConfig);
        }
        return $this->_redis;
    }

    public function getDueCommon() {
        if (is_null($this->libDueComment)) {
            $this->libDueComment = new DueComment();
        }
        return $this->libDueComment;
    }

    /**
     * 添加评论
     * @param int $order_id
     * @param int $uid
     * @param int $cert_uid
     * @param int $skill_id
     * @param int $star
     * @param string $tag_ids
     * @param string $comment
     * @return boolean
     */
    public function addComment(int $order_id, int $uid, int $cert_uid, int $skill_id, int $star, string $tag_ids, string $comment): bool {
        /**
         * @todo 参数过滤
         */
        $data = [
            'order_id' => $order_id,
            'uid' => $uid,
            'cert_uid' => $cert_uid,
            'skill_id' => $skill_id,
            'star' => $star,
            'tag_ids' => $tag_ids,
            'comment' => $comment,
        ];
        $res = $this->getDueCommon()->addComment($data);
        if ($res) {
            $this->updateCount($skill_id, $star);
            //更新用户标签计数<yalong2017@6.cn>
            $this->updateUserTags($cert_uid,$tag_ids);
            ### [ 更新订单评论状态 ]
            $orderObj = new DueOrderService();
            $orderObj->updateOrderCommentStatus($order_id);
            $orderObj->sendPush($uid, $cert_uid, 0);
        }
        return $res;
    }
    /**
     * 更新用户tags
     * ----------
     * yalong2017@6.cn
     * 2017-07-28 13:01
     */
    public function updateUserTags($cert_uid,$tag_ids){
        //获取主播的 tags
        $userTags = new DueTags();
        $data = $userTags->_getUserTagsByUids($cert_uid);
        $commentTagids = explode(",", $tag_ids);
        $tagids = array_column($data, "tagid");
        $insertData = [];
        $updateData = [];
        foreach($commentTagids as $v){
            if(!in_array($v, $tagids)){ //说明 该主播还没有被评过 该标签id ，则写入此标签
                $insertData[] = $v;
            }else{ //说明该主播已被评过该标签 更新库 频数即可
                $updateData[] = $v;
            }
        }
        if(!empty($insertData)){
            $res1 = $userTags->_insertUserTags($cert_uid,$insertData);
        }
        if(!empty($updateData)){
            $res2 = $userTags->_updateUserTags($cert_uid,$updateData);
        }
        if($res1 || $res2) return true;
        else{
            write_log(__CLASS__."评论时更新用户 due_user_tags表失败，第".__LINE__."行");
            return false;
        }
    }
    /**
     * 更新计数
     * @param type $skill_id
     * @param type $star
     * @param type $refresh
     */
    public function updateCount($skill_id, $star, $refresh = false) {
//        $ListSetkey = $this->getRedisListSetCacheKey();
        $CommentAvgkey = $this->getRedisCommentAverageCacheKey($skill_id);
        if ($skill_id > 0 && $star >= 1 && $star <= 10) {
            $redisObj = $this->getRedis();
            $res = $redisObj->hMget($CommentAvgkey, array('num', 'total', 'skill_id'));
            if ($res === false || empty($res['num']) || $refresh  ) {
                $row = $this->getDueCommon()->getTotal($skill_id);
                $row['skill_id'] = $skill_id;
                $redisObj->hMset($CommentAvgkey, $row);
                $redisObj->expire($CommentAvgkey, 86400 * 20);
                $res = $row;
            } else {
                $res['num'] ++;
                $res['total'] += $star;
                $redisObj->hMset($CommentAvgkey, $res);
                $redisObj->expire($CommentAvgkey, 86400 * 20);
            }
//            $chcheAvg = intval(round($res['total'] / $res['num'], 3) * 1000);
            if ($res['num'] != 0) {
                $avg = floor($res['total'] / $res['num']);
            } else {
                $avg = 0;
            }
            $result = $this->getDueCommon()->updateAvg($skill_id, $avg, $res['total'], $res['num']);
//            $redisObj->zAdd($ListSetkey, $chcheAvg, $skill_id);
//            if ($redisObj->zCard($ListSetkey) == 0) {
//                $this->createCommentZset();
//            }
            
            return $result;
        }
    }

    /**
     * 返回按评分排序的技能id
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getListSkillId(int $page = 1, int $size = 1): array {
        $start = ($page - 1) * $size;
        $end = $start + $size;
        $redis = $this->getRedis();
        $key = $this->getRedisListSetCacheKey();
        $data = $redis->zRevRange($key, $start, $end);
        return $data;
    }

    /**
     * 
     * 
     * 通过技能ID返回平均分
     * @param array $ids  [1] 或者 [1,2]
     * @return array
     */
    public function getAvgBySkillId(array $ids): array {
        $redis = $this->getRedis();
        $key = $this->getRedisListSetCacheKey();
        $data = [];
        foreach ($ids as $member) {
            $data[$member] = $redis->zScore($key, $member);
        }
        return $data;
    }

    /**
     * 重建skill排序集合
     * @return type
     */
    public function createCommentZset() {
        $ListSetkey = $this->getRedisListSetCacheKey();
        $res = $this->getDueCommon()->getTotalGroupBySkill();
        $redisObj = $this->getRedis();
        foreach ($res as $key => $row) {
            if (($key + 1) % 500 == 0) {
                $redisObj->expire($ListSetkey, 86400 * 20);
            }
            $avg = intval(round($row['total'] / $row['num'], 3) * 1000);
            $redisObj->zAdd($ListSetkey, $avg, $row['skill_id']);
        }
        return;
    }

    /**
     * 获取用户的指定评论
     * @return type
     */
    public function getCommentByOrderIdAndUid($uid, $orderId) {
       return $this->getDueCommon()->getUserCommentByUid($uid, $orderId);
    }

    /**
     * 获取所有被评论过得主播uids
     * --------------------
     * @author yalongSun<yalong2017@6.cn>
     * @param $page 页码
     * @param $num  
     * @return array
     */
    public function getUserIdsByPage($page = 0, $num = 20) {
        $commentObj = new DueComment();
        return $commentObj->getUserIdsByPage($page, $num);
    }

    /**
     * 通过主播uids拉去评论信息
     * ------------------
     * @author yalongSun<yalong2017@6.cn>
     * @param $uids 主播uids 索引数组
     * @return array
     */
    public function getCommentsByUids($uids = 0) {
        if (!is_array($uids))
            return false;
        $commentObj = new DueComment();
        return $commentObj->getCommentsByUids($uids);
    }

}
