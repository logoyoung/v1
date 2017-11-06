<?php

namespace lib;

use \DBHelperi_huanpeng;
use lib\CDNHelper;
use lib\LiveCacheBat;

/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/1
 * Time: 15:32
 */

/**
 * Class Video
 *
 * 录像类
 */
class Video
{

    /**
     * @var    $_liveID    直播id
     */
    private $_liveID;

    /**
     * @var $_videoID 录像id
     */
    private $_videoID;

    /**
     * @var    $_db  数据对象
     */
    private $_db;
	/**
	 * @var    $_redis  redis对象
	 */
	private $_redis;

    /**
     * @var $_videoTable 录像表
     */
    private $_videoTable;

    /**
     * @var $_videoMergeRecordTable 录像合并、转码、截图记录表
     */
    private $_videoMergeRecordTable;

    /**
     * @var    $_videoMergeQueueTable  录像合并队列
     */
    private $_videoMergeQueueTable;

    const VIDEO_TABLE = 'video';
    const FLV_RECORD_TABLE = 'live_VideoRecord';
    const MERGE_RECORD_TABLE = 'video_merge_record';
    const DOWNLOAD_TABLE = 'video_download_record';
    const OPT_MERGE = 1;
    const OPT_TRANSCODE = 2;
    const OPT_POSTER = 3;
    const OPT_COMPLETE = 1;
    const OPT_CLEAR = 2;
	const ERR_STACK = 'video_merge_error_stack';
    /**
     * WCS_BUCKET_VIDEO  网宿录像空间
     */
    const WCS_BUCKET_VIDEO = '6huanpeng-test001';

    /**
     * const VIDEO_LOG_FILE        录像错误日志
     */
    const VIDEO_LOG_FILE = LOG_DIR . 'video.error.log';

    /**
     * const CACHE 				  是否缓存任务
     */
    const CACHE = false;

    /**
     * @var $_cdnHelper           cdn处理对象
     */
    private $_cdnHelper = null;

    /**
     * Video constructor.
     */
    public function __construct($db = null, $cdnHelper = null)
    {
        if (!$db)
        {
            $db = new DBHelperi_huanpeng();
        }
        if (!$cdnHelper)
        {
            $cdnHelper = new CDNHelper();
        }
        $this->_db = $db;
        $this->_cdnHelper = $cdnHelper;
		$this->_redis = self::getRedis();
    }

    public static function getDB()
    {
        return new DBHelperi_huanpeng();
    }
    public static function getRedis()
	{
		$redis = new \RedisHelp();
		return $redis->getMyRedis();
	}

    /**
     * 获取某场直播录像片段
     *
     * @param int $liveid 直播id
     *
     * @return    array    flv片段
     */
    public static function getStreamFlvs($liveid, $db)
    {
        if (!(int) $liveid || !$db)
        {
            return false;
        }
        $r = $db->field('stream,`keys`,bucket')->where("liveid={$liveid}")->select(self::FLV_RECORD_TABLE);
        if (!is_array($r))
        {
            return false;
        }

        return $r;
    }

    public static function getFlvs($liveid, $db)
    {
        if (!(int) $liveid || !$db)
        {
            return false;
        }
        $r = $db->field('`keys`')->where("liveid={$liveid}")->select(self::FLV_RECORD_TABLE);
        if (!is_array($r))
        {
            return false;
        }
        $flvs = array_map(function ( $v )
        {
            return $v['keys'];
        }, $r);

        return $flvs;
    }

    /**
     * 拼接文件
     *
     * @param  array  $files    文件列表
     * @param  string $saveFile 保存文件
     *
     * @return string                          执行任务id或错误码
     */
    public function mergeFiles($files, $saveFile)
    {
        return $this->_cdnHelper->mergeFiles($files, $saveFile);
    }

    /**
     * 视频文件转码
     *
     * @param  string $file     文件名
     * @param  string $saveFile 保存文件
     *
     * @return string                            执行任务id或错误码
     */
    public function transcodeFile($file, $saveFile, $cache = self::CACHE)
    {
        if (!$cache)
        {
            return $this->_cdnHelper->transcodeFile($file, $saveFile);
        }
        $LiveCacheBat = new LiveCacheBat();
        $taskID = explode('.', $saveFile);
        $taskID = $taskID[0];
        $taskID = "hptask{$taskID}";
        $files = "$file/$saveFile/$taskID";
        $r = $LiveCacheBat->produceTanscodeTask($files);
        if (!$r)
        {
            return false;
        }
        $task = ['persistentId' => $taskID];

        return json_encode($task);
    }

    /**
     * 视频截图
     *
     * @param string $file     文件名
     * @param string $saveFile 保存文件
     * @param string $offset   截取时间位置
     *
     * @return string                         执行任务id或错误码
     */
    public function cutOutVideoPicture($file, $saveFile, $offset)
    {
        return $this->_cdnHelper->cutOutVideoPicture($file, $saveFile, $offset);
    }

    /**
     * 删除文件
     *
     * @param  string|array $files 文件名
     *
     * @return string                        执行任务id或错误码
     */
    public function deleteFiles($files)
    {
        return $this->_cdnHelper->deleteFiles($files);
    }

    /**
     * 获取下载链接
     *
     * @param  string $url url链接
     *
     * @return string            下载链接
     */
    public static function getDownloadUrl($url)
    {
        return CDNHelper::getDownloadUrl($url);
    }

    public function addOptRecord($data)
    {
        if (!is_array($data))
        {
            return false;
        }
        $r = $this->_db->insert(self::MERGE_RECORD_TABLE, $data);

        return $r ? true : false;
    }

    public static function getMergeRecordByTaskId($taskid, $db)
    {
        if (!$taskid || !$db)
        {
            return false;
        }
        $r = $db->field('liveid,vname,bucket')->where("taskid='{$taskid}'")->select(self::MERGE_RECORD_TABLE);

        return isset($r[0]['liveid']) ? $r : false;
    }

    /**
     * 获取录像信息
     *
     * @param $videoID 录像ID
     *
     * @return array    录像信息
     */
    public function getVideoInfo($videoID, $db)
    {
        $r = $db->where("videoid={$videoID}")->select(self::VIDEO_TABLE);

        return isset($r[0]) ? $r[0] : false;
    }

    /**
     * 发布录像
     *
     * @param $videoID    录像ID
     *
     * @return bool        是否发布成功
     */
    public function publishVideo()
    {
        
    }

    /**
     * 删除用户录像
     *
     * @param $uid            用户id
     * @param $videoid        待删除录像
     *                        单个删
     *
     * @return bool            是否删除成功
     */
    public function deleteVideo()
    {
        
    }

    /**
     * 获取用户录像
     *
     * @param $uid            用户id
     *
     * @return array          用户录像
     */
    public function getUserVideoInfo()
    {
        
    }

    /**
     * 获取录像播放地址
     *
     * @param $videoID          录像id
     *
     * @return string          录像播放地址
     */
    public function getVideoPlayUrl($videoID, $db)
    {
        $r = $db->field('vfile')->where("videoID={$videoID}")->select(self::VIDEO_TABLE);
        if (empty($r[0]['vfile']))
        {
            return false;
        }
        $wsSecret = CDNHelper::getPlayLiveSecret(basename($r[0]['vfile']));

        return $r[0]['vfile'] . "?" . $wsSecret;
    }

    /**
     * 评论录像
     *
     * @param $videoID        录像ID
     *
     * @return bool            是否评论成功
     */
    public function commentVideo()
    {
        
    }

    /**
     * 获取录像评论
     *
     * @param $videoID        录像ID
     *
     * @return array        录像评论
     */
    public function getVideoComments()
    {
        
    }

    /**
     * 获取推荐录像
     *
     * @param videoID        录像ID
     *
     * @return array        推荐录像
     */
    public function getRecommendVideoList()
    {
        
    }

    //todo 点赞
    //todo 收藏

    /**
     * 录像日志
     *
     * @param $error        错误信息
     *
     * @return bool            是否记录成功
     */
    private function _videoLog()
    {
        
    }

    /**
     * 添加flv片段记录
     *
     * @param array  $flv flv信息
     * @param object $db  数据库对象
     *
     * @return bool            返回值
     */
    public static function addFlvRecord($flv, $db)
    {
        if (!$flv || !$db)
        {
            return false;
        }
        $r = $db->insert(self::FLV_RECORD_TABLE, $flv);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    public static function updateMergeRecord($taskID, $duration, $db)
    {
        if (!$taskID || !$db)
        {
            return false;
        }
        $data = array(
            'status' => self::OPT_COMPLETE,
            'length' => $duration
        );
        $r = $db->where("taskid='{$taskID}'")->update(self::MERGE_RECORD_TABLE, $data, true);
        mylog($r, LOG_DIR . 'Live.error.log');
        $r = $db->where("taskid='{$taskID}'")->update(self::MERGE_RECORD_TABLE, $data);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    public static function getMergeRecordByLiveID($liveID, $db)
    {
        $record = $db->where("liveid={$liveID}")->select(self::MERGE_RECORD_TABLE, true);
        mylog($record, LOG_DIR . 'Live.error.log');
        $record = $db->where("liveid={$liveID}")->select(self::MERGE_RECORD_TABLE);

        return isset($record[0]) ? $record : false;
    }

    public static function addVideo($live, $db)
    {
        mylog("添加录像中", LOG_DIR . 'Live.error.log');
        if (!$live || !$db)
        {
            return false;
        }
        $video = array(
            'uid' => $live['uid'],
            'liveid' => $live['liveid'],
            'gametid' => $live['gametid'],
            'gameid' => $live['gameid'],
            'gamename' => $live['gamename'],
            'title' => $live['title'],
            'length' => '',
            'poster' => '',
            'ip' => $live['ip'],
            'port' => $live['port'],
            'viewcount' => 0,
            'vfile' => '',
            'orientation' => $live['orientation'],
            'stop_reason' => $live['stop_reason']
        );

        $mergeRecords = self::getMergeRecordByLiveID($live['liveid'], $db);
        foreach ($mergeRecords as $k => $mergeRecord)
        {
            if ($mergeRecord['opt'] == self::OPT_MERGE || $mergeRecord['opt'] == self::OPT_TRANSCODE)
            {
                $video['length'] = $mergeRecord['length'];
                mylog($mergeRecord['length'], LOG_DIR . 'Live.error.log');
                $video['vfile'] = $mergeRecord['vname'];
                mylog($mergeRecord['vname'], LOG_DIR . 'Live.error.log');
            } elseif ($mergeRecord['opt'] == self::OPT_POSTER)
            {
                $video['poster'] = $mergeRecord['vname'];
            }
        }
        mylog(json_encode($video), LOG_DIR . 'Live.error.log');
        $r = $db->insert(self::VIDEO_TABLE, $video);

        //error todo
        return true;
    }

    /**
     * 获取主播录像
     *
     * @param      $uid
     * @param      $size
     * @param null $db
     *
     * @return $this|bool
     */
    public static function getVideoListByUid($uid, $size, $db = null)
    {
        $r = $db->where("uid={$uid} and status=" . VIDEO . " order by ctime DESC limit {$size}")->select('video');

        return isset($r[0]) ? $r : false;
    }

    /**
     *
     *
     * @param      $videoID
     * @param null $db
     *
     * @return mixed
     */
    public static function getVideoFollowCount($videoID, $db = null)
    {
        $num = $db->field('count(*) as count')->where('videoid=' . $videoID . '')->select('videofollow');

        return isset($num[0]['count']) ? $num[0]['count'] : false;
    }

    /**
     * 获取评分
     *
     * @param type $videoid
     * @param type $db
     *
     * @return type
     */
    public static function getVideoRate($videoID, $db = null)
    {
        $rate = $db->field('avg(rate) as score')->where('videoid=' . $videoID . '')->select('videocomment');

        return isset($rate[0]['score']) ? $rate[0]['score'] : false;
    }

    /**
     * 批量获取录像评论总数
     */
    public static function getVideoCommentCountByVideoId($videoid, $db = null)
    {
        if (empty($videoid))
        {
            return false;
        }
        if (is_array($videoid))
        {
            $videoid = implode(',', $videoid);
            $res = $db->field("videoid, count(*) as count")->where("videoid in($videoid) group by videoid")->select('videocomment');
        } else
        {
            $res = $db->field("videoid, count(*) as count")->where("videoid=$videoid")->select('videocomment');
        }
        if ($res)
        {
            foreach ($res as $v)
            {
                $comment[$v['videoid']] = $v['count'];
            }
        } else
        {
            $comment = array();
        }

        return $comment;
    }

    public static function getClearTask($db)
    {
        /* $r = $db->field( 'liveid' )
          ->where( "ctime<DATE_SUB(CURDATE(), INTERVAL 1 MONTH) and opt=" . self::OPT_POSTER . " and status=" . self::OPT_COMPLETE . "" )
          ->select( self::MERGE_RECORD_TABLE ); */
        //先检测下载列表
        //todo
        $videoid = 0;
        $r = $db->field('videoid,liveid')
                ->where('videoid=' . $videoid . 'ctime<DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND status=' . VIDEO_WAIT)
                ->limit(1)
                ->select(self::VIDEO_TABLE);
        if (!isset($r[0]['videoid']) || !$r[0]['videoid'])
        {
            return false;
        }

        $sql = $db->where("videoid={$r[0]['videoid']}")->update(self::VIDEO_TABLE, array('status' => VIDEO_DEL), true);
        $db->query($sql);
        if (!$db->affectedRows)
        {
            return false;
        }

        return isset($r[0]['liveid']) ? $r[0]['liveid'] : false;
    }

    public static function getPublishStatus($liveID, $db)
    {
        $r = $db->field('publish')->where("liveis={$liveID}")->select(self::VIDEO_TABLE);

        return isset($r[0]['publish']) ? $r[0]['publish'] : false;
    }

    public static function downloadStatus($liveID, $db)
    {
        //todo
        $r = $db->field('liveid')->where("liveid={$liveID}")->select(self::DOWNLOAD_TABLE);

        return isset($r[0]['liveid']) ? true : false;
    }

    public static function getVideoByLiveID($liveID, $db)
    {
        $r = $db->where("liveid={$liveID}")->select(self::VIDEO_TABLE);

        return isset($r[0]) ? $r[0] : false;
    }

    public static function completeClear($liveID, $db)
    {
        $r = $db->where("liveid={$liveID}")->update(self::MERGE_RECORD_TABLE, array('status' => self::OPT_CLEAR));

        return $r;
    }

    public function updateTaskIDs($taskID, $ids)
    {
        $ids = array_map(function ($id)
        {
            return "'{$id}'";
        }, $ids);
        $ids = implode(',', $ids);
        $r = $this->_db->where("taskid in({$ids})")->update(self::MERGE_RECORD_TABLE, array('taskid' => $taskID));
        var_dump($r);
        return $r;
    }

    /**
     * 获取录像
     *
     * @param int    $gameId
     * @param string $order
     *
     * @return array
     */
    public function getVideoByGameId($gameId,$db)
    {
        if ($gameId)
        {
            $lives = $db->field('videoid,gamename,poster,length,uid,title,ctime,viewcount,orientation')->where('status=' . VIDEO . ' and gameid=' . $gameId . '')->select('video');
        } else
        {
            $lives = $db->field('videoid,gamename,poster,length,uid,title,ctime,viewcount,orientation')->where('status=' . VIDEO . '')->select('video');
        }
        return $lives ? $lives : [];
    }

    public  function pushErrtask($flvs){
		return $this->_redis->lPush(self::ERR_STACK,json_encode($flvs));
	}

}
