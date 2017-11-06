<?php

namespace service\due;

use system\RedisHelper;
use lib\due\DueTags;

class DueTagsService {

    //获取标签失败
    const ERROR_GET_TAG = -8008;
    public static $errorMsg =[
        self::ERROR_GET_TAG =>'获取标签失败',
    ];
    public $libDueTags = null;
    public $_redis = null;
    public $redisConfig = 'huanpeng';

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

    public function getDueTags() {
        if (is_null($this->libDueTags)) {
            $this->libDueTags = new DueTags();
        }
        return $this->libDueTags;
    }

    public function getTagsByids($ids) {
        $idArray = [];

        if (!empty($ids)) {
            if (is_array($ids)) {
                $idArray = $ids;
            } else if (is_string($ids)) {
                if (FALSE === strpos($ids, ',')) {
                    $idArray = [$ids];
                } else {
                    $idArray = explode(',', $ids);
                }
            } else if (is_int($ids)) {
                $idArray = [$ids];
            }
            return $this->getDueTags()->getTags($idArray);
        }
        return [];
    }

    /**
     * 获取所有标签 按星级返回
     * @return array
     */
    public function getAllTags()
    {
        $res = [];
       $data =  $this->getDueTags()->getAllTags();
        if(false === $data)
        {
            $code   = self::ERROR_GET_TAG;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            foreach($data as $key=>$value)
            {
                $res[] = $value;
            }
            return $res;
        }
    }

    /**
     * 获取标签名
     * @param $ids
     * @param $allTags ['标签id'=>'标签名'] 一次取库 或redis
     * @return array
     */
    public function getTagNameByIds($ids,$allTags=[])
    {
        if(empty($allTags))
        {
            return [];
        }
        $res = [];
        $idArray = [];
        $tags = [];
        if (!empty($ids)) {
            if (is_array($ids)) {
                $idArray = $ids;
            } else if (is_string($ids)) {
                if (FALSE === strpos($ids, ',')) {
                    $idArray = [$ids];
                } else {
                    $idArray = explode(',', $ids);
                }
            } else if (is_int($ids)) {
                $idArray = [$ids];
            }
            foreach($idArray as $value)
            {
               foreach($allTags as $key=>$v)
               {
                   if($value == $v['id'])
                   {
                       $tags['id'] = $v['id'];
                       $tags['tag'] = $v['tag'];
                       $res[] = $tags;
                   }
               }
            }
            return $res;
        }
    }
    //获取用户的tags
    public function getUserTagsByUid($uid=0)
    {
        $this->getRedis();
        if($uid==0) return false;
        try{
            $data = $this->_redis->lrange("userTags:" . $uid,0,-1);
//             throw new \Exception("测试 redis服务挂机 自动切 库读取用户标签");
        }catch (\Exception $e){
            //redis 异常 强制读库
            $info = $this->getUserTagsByUids($uid);
            $tagids = array_slice($info, 0,4,true);
            $data = array_column($tagids, "tagid");
            write_log(__CLASS__."第".__LINE__."行  描述：".$e->getMessage());
        } 
        return $data;
    }
    /**
     * 通过用户uid 拉去最近一条被评论的tag
     * ----------------------------
     * @author yalongSun
     * @return array
     */
    public function getLastSqlByUid(int $uid){
        $tags = $this->getDueTags()->getLastSqlByUid($uid);
        return explode(",", $tags[0]['tag_ids']);
    }
    //按分页去查询 被评论过得用户uid  从 due_user_tags表中获取
    public function getUidByPage(int $page,int $size):array{
        return $this->getDueTags()->getUidByPage($page, $size);
    }
    //获取用户 tagids
    public function getUserTagsByUids($uid):array{
        return $this->getDueTags()->_getUserTagsByUids($uid);
    }
}
