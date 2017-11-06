<?php



class videoHelper{
    
    /************* static *********************/
    //const FILE_VIDEO_DST_DIR = '/leofs/v/huanpeng';
    //const FILE_VIDEO_DST_DIR = '/home/guanlong/v';
    //const FILE_POSTER_DST_DIR = '/leofs/i/huanpeng';
    //const FILE_POSTER_DST_DIR = '/home/guanlong/i';
    
    //const LOG_FILE = '/data/logs/saverecord.log';
    
    //live status
    const LIVE_VIDEO = 103;
    const LIVE_SAVING = 102;
    const LIVE_STOP = 101;
    const VIDEO_UNPUBLISH = 1;
    const VIDEO_AUTO_PUBLISH = 1;
    const MERGE_FAILED = 1;
    const MERGE_SUCCESS = 2;

    //
    const WCS_VIDEO_MERGE = 1;
    const WCS_VIDEO_POSTER = 2;
    const WCS_VIDEO_COMPLETE = 3;
    const WCS_VIDEO_CLEAR = 4;
    private static $_instance = null;
    
    private $db = null;

    private function __construct($db = null){
        if(!$db) $this->db = new DBHelperi_huanpeng();
        else $this->db = $db;
    }
    /**
     * 
     * @return videoHelper  */
    public static function getInstance(){
        if( !isset(self::$_instance) )
            self::$_instance = new videoHelper();
        return self::$_instance;
    }
    /**
     * 不允许克隆
     *   */
    public function __clone(){
        die(' clone is not allowed '.E_USER_ERROR);
    }
    /**
     * 获取任务ID
     * @param number $start
     * @return Ambigous <boolean, number, unknown>  */
    public function getVId($start=0){
        return  self::$_instance->lockLiveByStatus(self::LIVE_SAVING, self::LIVE_STOP,$start);
    }

    public function getMergedVideo(){
        $sql = "SELECT * FROM `video_merge_record` WHERE `status`=".self::WCS_VIDEO_MERGE." LIMIT 1";
        $res = self::$_instance->db->query($sql);
        if (! $res)
            return 0;
        $video = $row = $res->fetch_assoc();
        if(!isset($video['id']))
            return 0;
        $sql = "UPDATE `video_merge_record` SET `status`=".self::WCS_VIDEO_POSTER." WHERE `id`={$video['id']}";
        $res = self::$_instance->db->query($sql);
        // 检查 当被其它进程上锁返回空行数
        if (! self::$_instance->db->affectedRows)
            return 0;
        return $video;
    }
    /**
     * 说明：
     * 主要用于扫描录像生成任务队列表，
     * 获取任务并更改状态（上锁）
     * 返回
     * 成功：直播ID
     * 失败：false
     *
     * @param object $db
     * @param number $srcStatus//初始状态
     * @param number $dstStatus//目标状态
     * @param number $start//扫描起始行
     * 根据日志或者给定不同进程不同的起始位置以缩小扫描的范围
     * @return boolean|number
     */
    public function lockLiveByStatus($dstStatus, $srcStatus, $start = 0){
        if (! $start)
            $sql = "SELECT `liveid` FROM `videosave_queue` WHERE `status`={$srcStatus} AND `go`=".self::WCS_VIDEO_MERGE." LIMIT 1";
        else
            $sql = "SELECT `liveid` FROM `videosave_queue` WHERE `id`>{$start} `status`={$srcStatus} AND `go`=".self::WCS_VIDEO_MERGE."  LIMIT 1";
        $res = self::$_instance->db->query($sql);
        if (! $res)
            return 0;
        $row = $res->fetch_row();
        $liveid = $row[0] ? $row[0] : 0;
        if (! $liveid)
            return 0;
        $sql = "UPDATE `videosave_queue` SET `status`={$dstStatus} WHERE `liveid`={$liveid} AND `status`={$srcStatus}";
        $res = self::$_instance->db->query($sql);
        // 检查 当被其它进程上锁返回空行数
        if (! self::$_instance->db->affectedRows)
            return 0;
        // 同步live表
        self::$_instance->updateLiveStatus($liveid, 'live', $dstStatus, $srcStatus);
        return $liveid;
    }
    /**
     * 说明：
     * 更改直播状态
     *
     * @param object $db
     * @param number $liveid//直播ID
     * @param String $table//表名
     * @param number $status//目标状态
     * @param number $status2//初始状态（可选）
     * @return boolean
     */
    function updateLiveStatus($liveid, $table, $status, $status2 = NULL){
        if (! $status2)
            $sql = "UPDATE {$table} SET `status`={$status} WHERE `liveid`={$liveid}";
        else
            $sql = "UPDATE {$table} SET `status`={$status} WHERE `liveid`={$liveid} AND `status`={$status2}";
        $res = self::$_instance->db->query($sql);
        if (! self::$_instance->db->affectedRows)
            return false;
        return true;
    }
    
    /**
     * 说明：
     * 通过直播ID获取该直播所有流名称以及流所在的服务器
     * 
     * @param number $liveid
     * @return boolean|string  */
    public function getStreamListByLive($liveid=0){   
        if(!$liveid)
            return false;
        $addr = array();
        $sql = "SELECT `stream`,`server` FROM `liveStreamRecord` WHERE `liveid`={$liveid}";
        $res = self::$_instance->db->query($sql);
        if (! $res)
            return false;
        if (! $res->num_rows)
            return false;
        while ($row = $res->fetch_assoc()) {
            $addr[] = $row;
        }
        return json_encode($addr);
   }

   public function getKeysListByLive($liveid=0){
       if(!$liveid)
           return false;
       $keys = array();
       $sql = "SELECT `keys`,`bucket` FROM `live_VideoRecord` WHERE `liveid`={$liveid} ";
       $res = self::$_instance->db->query($sql);
       if (! $res)
           return false;
       if (! $res->num_rows)
           return false;
       $bucket = '';
       while ($row = $res->fetch_assoc()) {
           //todo process json keys
           $bucket = $row['bucket'];
           $keys = array_merge($keys,json_decode($row['keys']));
       }
       return json_encode(array('bucket'=>$bucket,'keys'=>$keys));
   }
   
   public function getUserByLive($liveid=0){
       if(!$liveid) return false;
        $sql = "SELECT `uid` FROM `live` WHERE `liveid`={$liveid}";
        $res = self::$_instance->db->query($sql);
        if (! $res)
            return false;
        $row = $res->fetch_row();
        return $row[0] ? $row[0] : false;
    }
   public function sendMsg($uid,$msg=''){
       //保存发送消息
   }
   public function saveDB($video){
       if(!is_array($video)) 
           return false;
       $insertID = self::$_instance->db->insert('video', $video);
       if(!$insertID) 
           return false;
       if($video['status']!=self::VIDEO_UNPUBLISH)
           return true;
       $data = array('videoid'=>$insertID);
       $tid = self::$_instance->db->insert('admin_wait_pass_video', $data);
       return $tid?true:false;
   }
   public function save($livedata,$callback,$msg){
       if(!is_array($livedata)||!$callback||!$msg)
           return false;
       //重组录像数据
       $livet = self::getLiveById($livedata['liveid']);
       $livedata['uid'] = $livet['uid'];
       $livedata['gametid'] = $livet['gametid'];
       $livedata['gameid'] = $livet['gameid'];
       $livedata['gamename'] = $livet['gamename'];
       $livedata['title'] = $livet['title'];
       //$livedata['poster'] = $livedata['poster']?'/'.$livedata['poster']:$livet['poster'];
       $livedata['ip'] = $livet['ip'];
       $livedata['port'] = $livet['port'];
       //$livedata['vfile'] = '/'.$livedata['vfile'];
       $livedata['orientation'] = $livet['orientation'];
       //$livedata['status'] = ($livet['antopublish']==self::VIDEO_AUTO_PUBLISH)?self::VIDEO_UNPUBLISH:0;
       if($livet['antopublish']==self::VIDEO_AUTO_PUBLISH){
           $livedata['status'] = self::VIDEO_UNPUBLISH;
           //$msgc = $msg['auto'];
           $msgc = str_replace('{gamename}-{title}',"{$livedata['gamename']}-{$livedata['title']}",$msg['auto']);
       }else{
           $livedata['status'] = self::VIDEO_UNPUBLISH;
           $msgc = str_replace('{gamename}-{title}',"{$livedata['gamename']}-{$livedata['title']}",$msg['notAuto']);
       }
       //移动录像文件和海报
       $poster = self::mvfile($livedata['poster'],$GLOBALS['env-def'][$GLOBALS['env']]['img-dir']);
       $livedata['poster'] = $poster?$poster:$livet['poster'];
       $vfile = self::mvfile($livedata['vfile'],$GLOBALS['env-def'][$GLOBALS['env']]['video-dir'] );
       if(!$vfile)
           return false;
       $livedata['vfile'] = $vfile;
       $r = self::saveDB($livedata);
       if(!$r)
           return false;
       self::$_instance->updateLiveStatus($livedata['liveid'], 'live', self::LIVE_VIDEO,self::LIVE_SAVING);
       self::$_instance->updateLiveStatus($livedata['liveid'], 'videosave_queue', self::LIVE_VIDEO,self::LIVE_SAVING);
       if(!function_exists($callback))
           return true;
       $callback($livedata['uid'],$msg['title'],$msgc,0,self::$_instance->db);
       return true;
   }
   public function mvfile($src, $base=''){
       if(!is_file($src) || !is_dir($base))
           return false;
       $ext = pathinfo($src,PATHINFO_EXTENSION);
       $rand = md5($src);
       $dstfile = '/'.$rand[0].'/'.$rand[1].'/'.$rand.'.'.$ext;
       if(!self::mkdirs($base.'/'.$rand[0].'/'.$rand[1]) )
           return false;
       $r = rename($src, $base.$dstfile);
       if(!$r) 
           return false;
       return $dstfile;
   } 
   
   public function mkdirs($dir, $mode = 0755) {
    if (is_null($dir) || $dir === "")
        return FALSE;
    if (is_dir($dir) || $dir === "/")
        return TRUE;
    if (self::mkdirs(dirname($dir), $mode)) {
        $r = mkdir($dir, $mode);
        if (!$r)
            exit(-1902);
        return $r;
    }
    return FALSE;
}
   public function getLiveById($liveid=0){
       if(!$liveid) return false;
       $sql = "SELECT * FROM `live` WHERE `liveid`={$liveid}";
       $res = self::$_instance->db->query($sql);
       if(!$res)
           return false;
       $row = $res->fetch_assoc();
       return $row;
   }
   /*public function report($liveid,$status){
       if(!$liveid || !$status)
           return false;
       self::$_instance->db->where("liveid=$liveid")->update('admin_videomerge_failed', array('status'=>$status));
       return true;
   }*/
   public function report($data){
       if(!is_array($data))
           return false;
       self::$_instance->db->insert('admin_videomerge_failed', $data);
   }
   public function recordOpt($data){
       if(!is_array($data))
           return false;
       self::$_instance->db->insert('video_merge_record',$data);
   }
   public function updatePosterOpt($data){
       if(!$data||!is_array($data))
           return false;
       self::$_instance->db->where("id=".$data['id'])->update('video_merge_record',array('posterid'=>$data['posterid']));
   }

    /**
     * @return int
     */
    public function getDid(){
        $sql = "SELECT * FROM `video_merge_record` WHERE `status`=".self::WCS_VIDEO_COMPLETE." LIMIT 1";
        $res = self::$_instance->db->query($sql);
        if (! $res)
            return 0;
        $video = $row = $res->fetch_assoc();
        if(!isset($video['id']))
            return 0;
        $sql = "UPDATE `video_merge_record` SET `status`=".self::WCS_VIDEO_CLEAR." WHERE `id`={$video['id']}";
        $res = self::$_instance->db->query($sql);
        // 检查 当被其它进程上锁返回空行数
        if (! self::$_instance->db->affectedRows)
            return 0;
        return $video;
    }
}

?>