<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017-6-12
 * Time: 下午4:53:43
 * Desc: 统计用户评论tags 频数高的写入redis作为用户 tags展示
 */
namespace due;
ignore_user_abort();
set_time_limit(0);
include '/data/huanpeng/include/init.php';
use system\DbHelper;
use system\RedisHelper;
use service\due\DueTagsService;

class resetUserTags
{
    public $dbConfig = 'huanpeng';
    public $redisConfig = 'huanpeng';
    private $Db;
    private $_redis;
    
    private $tagService;
    private $page = 0;
    private $size = 20;
    const USER_TAGS = 'userTags';
    const USER_TAG_TABLE = 'due_user_tags';
    
    public function __construct(){
        $this->tagService = new DueTagsService();
        $this->Db = DbHelper::getInstance($this->dbConfig);
        $this->_redis = RedisHelper::getInstance($this->redisConfig);
    }
    /**
     * 批量重置用户tags
     * -------------
     */
    public function resetTags(){
        //分片获取评论 tag_ids、uid
        $i = 1;
        $logInfo = '';
        while (true){
            $this->page = $i;
            $uids = $this->getUidByPage($this->page, $this->size);
            if(empty($uids)) break;
            
            foreach($uids as $vo){
                //清理该用户的redis tags缓存
                $this->clearRedisUserTags($vo['uid']);
                //获取用户被评论tag
                $tags = $this->getUserTagsByUids($vo['uid']);
                foreach($tags as $k=>$v){
                    if($k==4) break;
                    $this->_redis->rpush(self::USER_TAGS.":".$v['uid'],$v['tagid']);
                }
                $tagids = implode(",", array_column($tags, "tagid"));
                $logInfo .="主播uid：{$v['uid']} [".$tagids."]\n";
                usleep(2);
                //                 var_dump($this->getTags($vo['uid']));
            }
            sleep(2);
            $i++;
        } 
        $logInfo = "执行脚本：/data/huanpeng/bin/due/resetUserTags.php \n".$logInfo."================".date("Y-m-d H:i:s")."===============\n\n";
        mylog($logInfo,"/data/logs/due_tags.log".date("Ym"));
    }
    /**
     * 获取redis结果
     * -----------
     */
    private function getTags($uid){
        $data = $this->_redis->
        lrange(self::USER_TAGS.":".$uid,0,-1);
        return $data;
    }
    /**
     * 获取评数最多的前四条
     * --------------------
     * @return array
     */
    private function getUidByPage($page=0,$num=20){
        return $this->tagService->getUidByPage($page, $num);
    }
    private function getUserTagsByUids(int $uid){
        return $this->tagService->getUserTagsByUids($uid);
    }
    private function clearRedisUserTags($uids){
//         foreach($uids as $vo){
//             $key = self::USER_TAGS.":".$vo['uid'];
//             $this->_redis->delete($key);
//         } 
        $result = $this->_redis->delete(self::USER_TAGS.":".$uids);
//         var_dump($result);
        mylog("清除{$uids}的缓存：".$result,"/data/logs/due_tags.log".date("Ym"));
        unset($result);
    }
    //将due_comment表中记录  灌入 due_user_tags表中  ，并且清理 redis tagids
    public function inUserTagsByComment(){
        //1获取due_comment表中所有记录
        $comments = $this->Db->query("select *from due_comment where tag_ids!=''");
        //2统计每个主播每个标签的次数
        $userTags=[];
        foreach($comments as $v){
            $userTags[$v['cert_uid']] .= $v['tag_ids'].",";
        } 
        $userTagInfo = [];
        foreach ($userTags as $k=>$v){
            $v = mb_substr($v,0,mb_strlen($v)-1);
            $tagids = explode(",", $v);
            $userTagInfo[$k] = array_count_values($tagids); 
        }
        //3清due_user_tags原有数据
        $this->Db->query("delete from due_user_tags");
        //4灌入due_user_tags表
        $sql = 'insert into due_user_tags(uid,tagid,nums) values';
        foreach($userTagInfo as $k=>$v){
            $cert_uid = $k;
            foreach($v as $ko=>$vo){
                $sql .= "({$cert_uid},{$ko},{$vo}),";
            }
        }
        $sql = mb_substr($sql,0,mb_strlen($sql)-1);
        $this->Db->query($sql);
//         exit;
        //获取用户redis已有key并清理掉  以下慎用 可能 CPU 、内存受不了
//         $data = $this->_redis->keys("*".self::USER_TAGS."*");
        $data = $this->Db->query("SELECT DISTINCT cert_uid FROM `due_comment` where tag_ids!='';");
        foreach($data as $v){ 
            $this->_redis->delete(self::USER_TAGS.":".$v['cert_uid']);
            usleep(2);
        }
    }
}
$obj = new resetUserTags();
//$obj->inUserTagsByComment();  
$obj->resetTags();
?>
