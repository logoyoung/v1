<?php

namespace lib;

use \DBHelperi_huanpeng;
use lib\WcsHelper;
use lib\Anchor;
use lib\CDNHelper;
use lib\LiveRoom;
use lib\Video;
use \RedisHelp;
use lib\SiteMsgBiz;
use lib\MsgPackage;
use service\game\GameService;
use service\event\EventManager;

/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/3/31
 * Time: 15:10
 */
/*
  define( 'STREAM_CREATE', 0 );
  define( 'STREAM_START', 100 );
  define( 'STREAM_CREATE_REF', 0 );
  define( 'STREAM_START_REF', 100 );
  define( 'STREAM_DISCONNECT_USER', 240 );
  define( 'STREAN_DISCONNECT_CDN', 200 );


  define( 'LIVE_TO_FLV', 120 );
  define( 'FLV_TO_VIDEO', 130 ); */

/**
 * 直播类
 *
 *
 *
 */
class Live
{

    /**
     * @var bool $_debug 调试模式
     */
    private $_debug = true;

    /**
     * @var        $_uid        主播id
     */
    private $_uid;

    /**
     * @var            $_liveID    直播id
     */
    private $_liveID;

    /**
     * @var            $_lastLiveID    最后一场直播id
     */
    private $_lastLiveID;

    /**
     * @var            $_streamID        直播流id
     */
    private $_streamID;

    /**
     * @var            $_streamName    直播流名称
     */
    private $_streamName;

    /**
     * @var            $_roomID        房间id
     */
    private $_roomID;

    /**
     * @var           $_room            房间对象
     */
    private $_room;

    /**
     * @var            $_videoID        视频id
     */
    private $_videoID;

    /**
     * @var            $_liveInfo        直播信息
     */
    private $_liveInfo;

    /**
     * @var            $_db            数据库对象
     */
    private $_db;

    /**
     * @var            $_ip            用户ip
     */
    private $_ip;

    /**
     * @var            $_port            用户端口
     */
    private $_port;

    /**
     * @var bool $_newLive 是否新直播
     */
    private $_newLive = true;

    /**
     * @var            $_publishRtmpUrl    推流地址
     */
    private $_publishRtmpUrl;

    /**
     * @var            $_playRtmpUrl    拉流地址
     */
    private $_playRtmpUrl;

    /**
     *        LIVE_TABLE        直播表
     */
    public $liveStopReason = [
        '0' => '主播停止直播',
        '1' => '您的网络连接超时，直播已结束！',
        '2' => '您的直播内容违规，已被管理结束直播！',
        '3' => '直播异常结束'
    ];

    const STOP_TYPE_ANCHOR = '0';
    const STOP_TYPE_TIMEOUT = '1';
    const STOP_TYPE_ADMIN = '2';
    const STOP_TYPE_EXCEPTION = '3';
    const LIVE_TABLE = 'live';
    const VIDEO_TABLE = 'video';
    const LIVE_TYPE_01 = 0;//录屏直播
    const LIVE_TYPE_02 = 1;//摄像头直播
    const LIVE_TYPE_03 = 2;//pc直播
    const LIVE_TYPE_04 = 3;//双屏主
    const LIVE_TYPE_05 = 4;//双屏从
    
    /**
     *        STREAM_TABLE    直播流表
     */
    const STREAM_TABLE = 'liveStreamRecord';

    /**
     *        STREAM_LOG_TABLE    直播流操作日志
     */
    const STREAM_LOG_TABLE = 'liveStreamLog';

    /**
     *        GAME_TABLE        游戏表
     */
    const GAME_TABLE = 'game';

    /**
     *        LOG_FILE    日志文件
     */
    const LOG_FILE = LOG_DIR . 'Live.error.log';
    const OTHER_GAME = 401;

    /**
     * Live constructor.
     *
     * @param int    $uid 用户id
     * @param object $db  数据库对象
     */
    public function __construct($uid = NULL, $db = NULL)
    {
        if (!(int) $uid)
        {
            return false;
        }
        $this->_uid = (int) $uid;
        if ($db)
        {
            $this->_db = $db;
        } else
        {
            $this->_db = self::getDB();
        }
        $this->_ip = fetch_real_ip($this->_port);
        $this->_ip = ip2long($this->_ip);
        $this->_publishRtmpUrl = $GLOBALS['env-def'][$GLOBALS['env']]['stream-pub'];
        $this->_playRtmpUrl = $GLOBALS['env-def'][$GLOBALS['env']]['stream-watch'];

        return true;
    }

    /**
     * 直播预创建
     *
     * @param null $liveParams 直播信息
     *
     * @return array|bool|void  过滤后的直播信息
     */
    private function _preCreateLive($liveParams = NULL)
    {
        //主播检测
        /* $pass = false;
          if( RN_MODEL )
          {
          $pass = Anchor::isRealAnchor( $this->_uid, $this->_db );
          }
          else
          {
          $pass = Anchor::isAnchor( $this->_uid, $this->_db );
          }
          if( !$pass )
          {
          return false;
          }
          //黑名单检测

          if( Anchor::isBlack( $this->_uid, $this->_db ) )
          {
          return false;
          } */
        //发直播权限检测
        if (Anchor::isSendLive($this->_uid, $this->_db) !== true)
        {
            return -7001;
        }
        //当前是否有直播检测,强行检查主库
        $lastLive = self::getLastLive($this->_uid, $this->_db, DBHELPERI_DBW);

        //无直播
        if (!count($lastLive) || ( $lastLive['status'] != LIVE && $lastLive['status'] != LIVE_CREATE ))
        {
            return array();
        }
        //有直播
        //是否异地登录
        if ($liveParams['deviceid'] != $lastLive['deviceid'])
        {
            return -7002;
        }
        //审核模式检测，直播标题关键字、长度过滤
        //todo
        return $lastLive;
    }

    /**
     * 创建直播
     *
     * @param null $liveParams 直播信息
     *
     * @return array|int      流地址数组｜错误代码
     *                       ［rtmp：//xxxx.com,stream］|70001
     */
    public function createLive($liveParams)
    {
        //直播预创建
        $lastLive = $this->_preCreateLive($liveParams);
        //无直播权限
        if (!is_array($lastLive))
        {
            return self::liveErrorLog($lastLive);
            //return $this->liveErrorLog( array( 70002, '预创建直播失败' ) );
        }
        //已有直播，继续直播
        if (is_array($lastLive) && count($lastLive))
        {
            $this->_liveID = $lastLive['liveid'];
            $this->_newLive = false;
            $this->_streamName = $lastLive['stream'];
            //停掉老流
            $streamStatus = $this->_getStreamStatus($lastLive['stream']);
            if ($streamStatus == STREAM_START)
            {
                $this->_setStreamStatus(STREAM_COVER, time());
            }
        }
        //无直播创建直播
        else
        {
            //数据过滤
            $liveParams = $this->_liveDataFilter($liveParams);
            if (!$liveParams)
            {
                return self::liveErrorLog(-7003);
                //return self::liveErrorLog( array( 70003, '直播数据不全或有误' ) );
            }

            $this->_liveID = $this->_db->insert(self::LIVE_TABLE, $liveParams);
            if (!$this->_liveID)
            {
                return self::liveErrorLog(-7004);
            }
        }
        //创建流
        $this->_streamID = $this->_createStream($this->_liveID);
        if (!$this->_streamID)
        {
            return self::liveErrorLog(-7005);
        }
        $this->_streamName = $this->_getStreamName($this->_streamID);
        //更新直播流信息
        $this->_setLiveStream();
        //返回推流地址
        $rtmpArr = $this->_getLivePublishRtmpUrl();
        mylog($rtmpArr['stream'], LOG_DIR . 'Live.error.log');

        return array(
            'liveID' => $this->_liveID,
            'stream' => $rtmpArr['stream'],
            'rtmpServer' => $rtmpArr['rtmpServer']
        );
    }

    /**
     * 直播开始
     *
     * @return bool
     */
    public function startLive($time = null)
    {
        //获取当前直播
        $live = self::getLastLive($this->_uid, $this->_db);
        mylog("liveid:{$live['liveid']} stream:{$live['stream']}   创建时间：" . $live['ctime'] . "-回调时间：" . date("Y-m-d H:i:s") . "-推流时间：" . date("Y-m-d H:i:s", $time), LOG_DIR . 'wsTimer.log');
        if ($live['status'] > LIVE)
        {
            return false;
        }
        $this->_liveID = $live['liveid'];
        $this->_streamName = $live['stream'];
        $time = isset($time) ? $time : time();
        //同步流状态
        //同步直播状态
        $rl = $this->_setLiveStart($time);
        $rs = $this->_setStreamStatus(STREAM_START, $time);
        //房间通知
        $this->_sendLiveStartMsg();

        return true;
    }

    public function sendClientMsg()
    {
        //获取当前直播
        $live = self::getLastLive($this->_uid, $this->_db);
        if ($live['status'] > LIVE)
        {
            return false;
        }
        $this->_liveID = $live['liveid'];
        $this->_streamName = $live['stream'];
        //房间通知
        $this->_sendLiveStartMsg();

        return true;
    }

    /**
     * 直播截图
     *
     * @param string $posterUrl 直播海报地址
     * @param int    $liveID    直播ID
     * @param object $db        数据库对象
     *
     * @return bool           操作是否成功
     */
    public static function livePosterCallBack($posterUrl, $liveID, $db)
    {
        $r = $db->where("liveID={$liveID}")->update(self::LIVE_TABLE, array('poster' => $posterUrl));

        return $r ? true : false;
    }

    /**
     * 直播超时
     *
     * @param $type     超时类型
     *
     * @return bool     是否处理成功
     */
    public function liveTimeOut($type)
    {
        
    }

    /**
     * 直播中断
     *
     * @return bool     操作是否成功
     */
    public function liveDisconnect($time = null)
    {
        $lastLive = self::getLastLive($this->_uid, $this->_db);
        mylog("liveid:{$lastLive['liveid']}  stream:{$lastLive['stream']}  创建时间：" . $lastLive['ctime'] . "-回调时间：" . date("Y-m-d H:i:s") . "-断流时间：" . date("Y-m-d H:i:s", $time), LOG_DIR . 'wsTimer.log');
        $this->_streamName = $lastLive['stream'];
        if ($lastLive['status'] != LIVE)
        {
            return true;
        }
        $time = $time ? $time : time();
        $streamStatus = $this->_getStreamStatus($lastLive['stream']);
        if ($streamStatus >= STREAN_DISCONNECT_CDN)
        {
            return true;
        }
        $r = $this->_setStreamStatus(STREAN_DISCONNECT_CDN, $time);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * 主播结束直播
     *
     * @return int    错误码
     */
    public function anchorStopLive($time = null)
    {
        $lastLive = self::getLastLive($this->_uid, $this->_db);
        $this->stopLiveRedis($lastLive);
        if ((int) $lastLive['status'] > LIVE)
        {
            return true;
        }
        $this->_liveID = $lastLive['liveid'];
        $this->_streamName = $lastLive['stream'];
        //断直播
        $lr = $this->_setLiveStop(self::STOP_TYPE_ANCHOR);
        //断流
        $time = $time ? $time : time();
        $sr = $this->_setStreamStatus(STREAM_DISCONNECT_USER, $time); //todo true
        //发房间结束消息
        $this->_sendLiveStopMsg(self::STOP_TYPE_ANCHOR);

        //切断网宿流
        /* $rtmpUrl            = "rtmp://{$this->_publishRtmpUrl}/$this->_streamName";
          $forbidStreamErrStr = $this->_stopStream( $rtmpUrl );
          //错误过滤 */
        mylog("主播停止直播{$this->_liveID}", LOG_DIR . 'Live.error.log');

        return true;
    }

    /**
     * 管理结束直播
     *
     * @return bool    错误码
     */
    public function adminStopLive($time = null)
    {
        $lastLive = self::getLastLive($this->_uid, $this->_db);
        $this->stopLiveRedis($lastLive);
        if (!isset($lastLive['status']) || (int) $lastLive['status'] > LIVE)
        {
            return true;
        }
        $this->_liveID = $lastLive['liveid'];
        $this->_streamName = $lastLive['stream'];
        //断直播
        $lr = $this->_setLiveStop(self::STOP_TYPE_ADMIN);
        //断流
        $time = $time ? $time : time();
        $sr = $this->_setStreamStatus(STREAM_ADMIN_STOP, $time); //todo true
        //发房间结束消息
        $this->_sendLiveStopMsg(self::STOP_TYPE_ADMIN);

        //切断网宿流
        $rtmpUrl = "rtmp://{$this->_publishRtmpUrl}/$this->_streamName";
        $forbidStreamErrStr = $this->_stopStream($rtmpUrl);
        //错误过滤*/
        mylog("管理停止直播{$this->_liveID}", LOG_DIR . 'Live.error.log');
        return true;
    }

    public function systemStopLive($time = null, $liveID = null)
    {
        $lastLive = self::getLastLive($this->_uid, $this->_db, null, $liveID);
        $this->stopLiveRedis($lastLive);
        if ((int) $lastLive['status'] > LIVE)
        {
            return true;
        }
        $this->_liveID = $lastLive['liveid'];
        $this->_streamName = $lastLive['stream'];
        //断直播
        $lr = $this->_setLiveStop(self::STOP_TYPE_TIMEOUT);
        //断流
        $time = $time ? $time : time();
        $sr = $this->_setStreamStatus(STREAM_DISCONNECT, $time); //todo true
        //发房间结束消息
        $this->_sendLiveStopMsg(self::STOP_TYPE_TIMEOUT);

        //切断网宿流
        /* $rtmpUrl            = "rtmp://{$this->_publishRtmpUrl}/$this->_streamName";
          $forbidStreamErrStr = $this->_stopStream( $rtmpUrl );
          //错误过滤 */
        mylog("超时停止直播{$this->_liveID}", LOG_DIR . 'Live.error.log');
        return true;
    }

    public function exceptionStop($liveStatus = null, $streamStatus = null, $liveID = null)
    {
        $lastLive = self::getLastLive($this->_uid, $this->_db, null, $liveID);
        $this->stopLiveRedis($lastLive);
        $curStreamStatus = self::getStreamInfoByStreamName($lastLive['stream'], $this->_db);
        if ((int) $lastLive['status'] > LIVE)
        {
            return true;
        }
        $this->_liveID = $lastLive['liveid'];
        $this->_streamName = $lastLive['stream'];
        //断直播
        if ($liveStatus)
        {
            $lr = self::setLiveStopStatusByLiveID($this->_liveID, $liveStatus, $this->_db);
        }
        if ((int) $curStreamStatus['status'] < STREAM_DISCONNECT && $streamStatus)
        {
            $sr = self::setStreamStopByStreamName($this->_streamName, $streamStatus, $this->_db, $liveStatus);
        }
        //发房间结束消息
        $this->_sendLiveStopMsg(self::STOP_TYPE_EXCEPTION);

        //切断网宿流
        /* $rtmpUrl            = "rtmp://{$this->_publishRtmpUrl}/$this->_streamName";
          $forbidStreamErrStr = $this->_stopStream( $rtmpUrl );
          //错误过滤 */
        mylog("异常停止直播{$this->_liveID}", LOG_DIR . 'Live.error.log');
        return true;
    }

    /**
     * 直播生成flv
     *
     * @return bool    操作是否成功
     */
    public static function liveToFlvCallBack($liveID, $db)
    {
        if (!$liveID || !$db)
        {
            return false;
        }
        $status = $db->field('status')->where("liveid={$liveID}")->select(self::LIVE_TABLE);
        if (isset($status[0]['status']) && $status[0]['status'] == LIVE_TO_FLV)
        {
            return true;
        }
        $data = array('status' => LIVE_TO_FLV, 'utime' => date('Y-m-d H:i:s'));
        $r = $db->where("liveid={$liveID} and status=" . LIVE_STOP)->update(self::LIVE_TABLE, $data);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * 直播生成录像
     *
     * @return bool    操作是否成功
     */
    public static function flvToVideoCallBack($liveID, $db)
    {
        if (!$liveID || !$db)
        {
            return false;
        }
        $status = $db->field('status')->where("liveid={$liveID}")->select(self::LIVE_TABLE);
        if (isset($status[0]['status']) && $status[0]['status'] == FLV_TO_VIDEO)
        {
            return true;
        }
        $data = array('status' => FLV_TO_VIDEO, 'utime' => date('Y-m-d H:i:s'));
        $r = $db->where("liveid={$liveID} and status=" . LIVE_TO_FLV)->update(self::LIVE_TABLE, $data);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * 录像截图
     *
     * @return bool    操作是否成功
     */
    public static function videoPosterCallBack($liveID, $db)
    {
        if (!$liveID || !$db)
        {
            return false;
        }
        $status = $db->field('status')->where("liveid={$liveID}")->select(self::LIVE_TABLE);
        if (isset($status[0]['status']) && $status[0]['status'] == VIDEO_TO_POSTER)
        {
            return true;
        }
        $data = array('status' => VIDEO_TO_POSTER, 'utime' => date('Y-m-d H:i:s'));
        $r = $db->where("liveid={$liveID} and status=" . FLV_TO_VIDEO)->update(self::LIVE_TABLE, $data);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * 录像回调超时
     *
     * @return bool        操作是否成功
     */
    public function videoTimeOut()
    {
        
    }

    /**
     * 直播活动完成
     *
     * @return bool        操作是否成功
     */
    public static function completeLive($liveID, $db)
    {
        if (!$liveID || !$db)
        {
            return false;
        }
        $live = $db->where("liveid={$liveID}")->select(self::LIVE_TABLE);
        if (isset($live[0]['status']) && $live[0]['status'] == LIVE_COMPLETE)
        {
            return true;
        }
        $data = array('status' => LIVE_COMPLETE, 'utime' => date('Y-m-d H:i:s'));
        $r = $db->where("liveid={$liveID} and status>=" . FLV_TO_VIDEO)->update(self::LIVE_TABLE, $data);
        if (!$r)
        {
            return false;
        }
        //保存录像信息
        mylog("添加录像中", LOG_DIR . 'Live.error.log');
        $vr = Video::addVideo($live[0], $db);
        //error todo
        mylog('发站内信', LOG_DIR . 'Live.error.log');
        $msg = array(
            'uid' => $live[0]['uid'],
            'title' => '系统消息',
            'content' => "您的直播视频\"{$live[0]['gamename']}-{$live[0]['title']}\"已生成，可以到我的空间发布哦～"
        );
        self::sendVideoCompleteMsg($msg, $db);

        return true;
    }

    /**
     * 获取流名称
     *
     * @param $streamID  直播流id
     *
     * @return string    直播流名称
     */
    private function _getStreamName($streamID)
    {
        $r = $this->_db->field('stream')->where("id={$streamID}")->select(self::STREAM_TABLE);

        return $r[0]['stream'];
    }

    /**
     * 获取最后一场直播
     *
     * @param      $uid     用户id
     * @param null $db      数据库对象
     *
     * @return array        最一场直播信息
     */
    public static function getLastLive($uid, $db = null, $type = null, $liveID = null)
    {
        if (!(int) $uid)
        {
            return false;
        }
        if (!$db)
        {
            $db = self::getDB();
        }
        if ($liveID)
        {
            $sql = 'select * from ' . self::LIVE_TABLE . ' where liveid=' . $liveID;
        } else
        {
            $sql = 'select * from ' . self::LIVE_TABLE . ' where uid=' . $uid . ' order by liveid desc limit 1';
        }
        $res = $db->query($sql, $type);
        $r = mysqli_fetch_assoc($res);
        //var_dump($r);
        //$r = $db->where("uid={$uid} order by ctime desc limit 1")->select(self::LIVE_TABLE);
        if (!isset($r) || !is_array($r) || !count($r))
        {
            return array();
        }

        return $r;
    }

    public static function getDB()
    {
        return new DBHelperi_huanpeng();
    }

    public static function getRedis()
    {
        return new RedisHelp();
    }

    public static function isLiving($uid, $db = null)
    {
        if (!(int) $uid)
        {
            return false;
        }
        if (!$db)
        {
            $db = self::getDB();
        }
        $live = self::getLastLive($uid, $db);
        if (isset($live['status']) && $live['status'] == LIVE)
        {
            return $live['liveid'];
        } else
        {
            return false;
        }
    }

    public static function checkLiveByLiveID($liveid, $db)
    {
        if (!$liveid || !$db)
        {
            return false;
        }
        $r = $db->field('status')->where("liveid={$liveid}")->select(self::LIVE_TABLE);
        if (isset($r[0]['status']) && $r[0]['status'] == LIVE)
        {
            return true;
        } else
        {
            return false;
        }
    }

    public static function checkAllFlvCallBack($streamList, $liveid, $db)
    {
        if (!$streamList || !$liveid || !$db)
        {
            return false;
        }
        mylog('streamList1:' . json_encode($streamList), LOG_DIR . 'Live.error.log');
        //获取直播流
        $r = $db->where("liveid={$liveid}")->select(self::STREAM_TABLE);
        if (!isset($r[0]['stream']))
        {
            return false;
        }
        //$streamListByLive = $r;
        mylog('streamList2:' . json_encode($r), LOG_DIR . 'Live.error.log');
        foreach ($r as $k => $stream)
        {
            if (in_array($stream['stream'], $streamList))
            {
                continue;
            }
            $stime = strtotime($stream['stime']);
            $etime = strtotime($stream['etime']);
            if (( $stime < 0 ) || ( $etime < 0 ) || ( $etime - $stime ) < 1)
            {
                continue;
            } else
            {
                mylog('no merge!', LOG_DIR . 'Live.error.log');

                return false;
            }
        }
        mylog('can merge!', LOG_DIR . 'Live.error.log');

        return true;
    }

    /**
     * 获取流状态
     *
     * @param $stream         直播流名称
     *
     * @return string          直播流状态
     */
    private function _getStreamStatus($stream)
    {
        $r = $this->_db->field('status')->where("stream='{$stream}'")->select(self::STREAM_TABLE);
        if (!isset($r[0]['status']))
        {
            return self::liveErrorLog();
        }

        return $r[0]['status'];
    }

    /**
     * 获取直播状态
     *
     * @param $liveID        直播id
     *
     * @return string        直播状态
     */
    private function _getLiveStatus($liveID)
    {
        $r = $this->_db->field('status')->where("liveid={$liveID}")->select(self::LIVE_TABLE);
        if (isset($r[0]['status']))
        {
            return self::liveErrorLog();
        }

        return $r[0]['status'];
    }

    /**
     * 获取鉴权加密串
     *
     * @return string        鉴权加密串
     */
    private function _getPublishRtmpWscSecret()
    {
        $data = array(
            'liveid' => $this->_liveID,
            'uid' => $this->_uid,
            'tm' => time()
        );
        $data['sign'] = CDNHelper::getPublishLiveSecret($data);
        ;

        return http_build_query($data);
    }

    /**
     * 获取拉流加密串
     *
     * @return string        拉流加密串
     */
    private function _getPlayRtmpWscSecret($stream)
    {
        return CDNHelper::getPlayLiveSecret($stream);
    }

    /**
     * 获取推流地址
     *
     * @return array        推留地址
     *                        ［rtmp://xxx.com,stream］
     */
    private function _getLivePublishRtmpUrl()
    {
        $wcsSecret = $this->_getPublishRtmpWscSecret();

        return array(
            'rtmpServer' => $this->_publishRtmpUrl,
            'stream' => $this->_streamName . "?$wcsSecret"
        );
    }

    /**
     * 获取拉流地址
     *
     * @return array        拉留地址
     *                        ［rtmp://xxx.com,stream］
     */
    public function getLivePlayRtmpUrl()
    {
        $lastLive = self::getLastLive($this->_uid, $this->_db);
        if (isset($lastLive['status']) && $lastLive['status'] > LIVE)
        {
            return false;
        }

        return array(
            'rtmpServer' => $this->_playRtmpUrl,
            'stream' => "{$lastLive['stream']}?" . self::_getPlayRtmpWscSecret($lastLive['stream'])
        );
    }

    /**
     * 创建直播流
     *
     * @return bool        操作是否成功
     */
    private function _createStream()
    {
        $utime = date('Y-m-d H:i:s', time());
        $stream = "Y-" . $this->_liveID . "-" . rand(1000000, 9999999);
        $this->_streamID = $this->_db->insert(self::STREAM_TABLE, array('liveid' => $this->_liveID
            , 'server' => $this->_publishRtmpUrl, 'stream' => $stream, 'utime' => $utime, 'status' => STREAM_CREATE));
        if (!$this->_streamID)
        {
            return false;
        }

        /* $this->_streamChangeLog( array(
          'liveid'  => $this->_liveID,
          'stream'  => $stream,
          'lstatus' => LIVE_CREATE,
          'sstatus' => STREAM_CREATE,
          'server'  => $this->_publishRtmpUrl,
          'ref'     => STREAM_CREATE_REF
          ) ); */

        return $this->_streamID;
    }

    /**
     * 直播流日志记录
     *
     * @param $status        直播流状态
     *
     * @return bool            操作是否成功
     */
    private function _streamChangeLog($data)
    {
        return $this->_db->insert(self::STREAM_LOG_TABLE, $data);
    }

    /**
     * 设置流状态
     *
     * @param      $status    直播流状态
     * @param null $type      设置类型
     *
     * @return bool            操作是否成功
     */
    private function _setStreamStatus($status, $time = null, $type = false)
    {
        $status = (int) $status;
        if (!$status)
        {
            return false;
        }
        $curStatus = $this->_db->field('status')->where("stream='{$this->_streamName}'")->select(self::STREAM_TABLE);
        $curStatus = $curStatus[0]['status'];
        if ((int) $curStatus >= $status || (int) $curStatus > STREAM_START)
        {
            return true;
        }
        $now = date('Y-m-d H:i:s', time());
        $data = array('status' => $status, 'utime' => $now);
        if ($time && $status == STREAM_START)
        {
            $data['stime'] = date('Y-m-d H:i:s', $time);
        } elseif ($time && $status > STREAM_START)
        {
            $data['etime'] = date('Y-m-d H:i:s', $time);
        }
        $r = $this->_db->where("stream='{$this->_streamName}'")->update(self::STREAM_TABLE, $data);
        if (!$r)
        {
            return false;
        }
        //断过的流不允许再推
        /* if($status>=STREAM_DISCONNECT)
          {
          self::stopWsPubStream($this->_streamName);
          } */
        /* self::_streamChangeLog( array(
          'liveid'  => $this->_liveID,
          'stream'  => $this->_streamName,
          'lstatus' => LIVE_CREATE,
          'sstatus' => STREAM_CREATE,
          'server'  => $this->_publishRtmpUrl,
          'ref'     => STREAM_START_REF
          ) ); */

        return true;
    }

    /**
     * 设置直播状态
     *
     * @param $status            直播状态
     *
     * @return bool             操作是否成功
     */
    private function _setLiveStatus($data)
    {
        $status = (int) $data['status'];
        $curStatus = $this->_getLiveStatus($this->_liveID);
        if ((int) $curStatus >= $status)
        {
            return true;
        }
        $r = $this->_db->where("liveid={$this->_liveID}")->update(self::LIVE_TABLE, $data);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function _setLiveStart($time)
    {
        $data = array('status' => LIVE, 'stime' => date('Y-m-d H:i:s', $time), 'utime' => date('Y-m-d H:i:s'));
        $r = $this->_db->where("liveid={$this->_liveID} and status<" . LIVE)->update(self::LIVE_TABLE, $data);
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function _setLiveStop($type = '0')
    {
        $data = array('status' => LIVE_STOP, 'etime' => date('Y-m-d H:i:s', time()), 'utime' => date('Y-m-d H:i:s'), 'stop_reason' => $type);
        $r = $this->_db->where("liveid={$this->_liveID}")->update(self::LIVE_TABLE, $data);
        mylog(json_encode($data), LOG_DIR . 'Live.error.log');
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * 设置直播流名称
     *
     * @return bool            操作是否成功
     */
    private function _setLiveStream()
    {
        $r = $this->_db->where("liveid={$this->_liveID}")->update(self::LIVE_TABLE, array('server' => $this->_publishRtmpUrl, 'stream' => $this->_streamName));
        if (!$r)
        {
            return false;
        }

        return true;
    }

    /**
     * 向房间发开播消息
     *
     * @return bool            操作是否成功
     */
    private function _sendLiveStartMsg()
    {
        if (!$this->_room)
        {
            $redis = new RedisHelp();
            $this->_room = new LiveRoom($this->_uid, $this->_db, $redis);
        }

        return $this->_room->start($this->_liveID);
    }

    /**
     * 向房间发开播消息
     *
     * @return bool            操作是否成功
     */
    private function _sendLiveStopMsg($type = '0')
    {
        if (!$this->_room)
        {
            $redis = new RedisHelp();
            $this->_room = new LiveRoom($this->_uid, $this->_db, $redis);
        }

        return $this->_room->stop($this->_liveID, $type, $this->liveStopReason[$type]);
    }

    /**
     * 录像生成通知
     *
     * @param $msg                        消息
     *
     * @return bool                    是否成功
     */
    public static function sendVideoCompleteMsg($msg, $db = null, $redis = null)
    {
        if (!$db)
        {
            $db = self::getDB();
        }
        if (!$redis)
        {
            $redis = self::getRedis();
        }
        $package = MsgPackage::getSiteMsgPackage($msg['uid'], $msg['title'], $msg['content'], MsgPackage::SITEMSG_TYPE_TO_USER);
        $siteMsg = new SiteMsgBiz($db, $redis);
        $r = $siteMsg->sendMsg($package);

        return true;
    }

    /**
     * 切断直播流
     *
     * @param null $url 推流地址
     *
     * @return  string        错误码返回
     */
    private function _stopStream($url, $CDNHelper = null)
    {
        if (!$CDNHelper)
        {
            $CDNHelper = new CDNHelper();
        }

        return $CDNHelper->stopCDNStream($url);
    }

    /**
     * 直播错误日志
     *
     * @param $erroCode        错误码
     *
     * @return  bool        操作是否成功
     */
    public static function liveErrorLog($errorCode)
    {
        $errorStr = '[' . getmypid() . '] [' . date("Y-m-d H:i:s", time()) . '] ' . "errorcode[{$errorCode}]" . "\n";
        file_put_contents(self::LOG_FILE, $errorStr, FILE_APPEND);

        return $errorCode;
    }

    /**
     * 直播信息过滤
     *
     * @param null $data 直播信息
     *
     * @return bool                操作是否成功
     */
    private function _liveDataFilter($data = NULL)
    {
        if (!is_array($data))
        {
            return false;
        }
        $filter = array('uid', 'server', 'gametid', 'gameid', 'gamename'
            , 'title', 'ip', 'port', 'quality', 'orientation'
            , 'deviceid', 'livetype', 'longitude', 'latitude');
        //$diff = array_diff_key($filter,array_keys($data));
        $data = $this->_liveDataComplete($data);
        sort($filter);
        $dataKeys = array_keys($data);
        sort($dataKeys);
        if ($filter != $dataKeys)
        {
            return false;
        }
        $data['server'] = $this->_realEscapeString($data['server']);
        $data['title'] = $this->_realEscapeString($data['title']);
        $data['gamename'] = $this->_realEscapeString($data['gamename']);

        return $data;
    }

    /**
     * 字符串转义
     *
     * @param      $str            待插入数据库的字符串
     * @param null $db             数据库对象
     *
     * @return bool                操作是否成功
     */
    private function _realEscapeString($str, $db = NULL)
    {
        if ($this->_db)
        {
            return $this->_db->realEscapeString($str);
        }
        if ($db)
        {
            return $db->realEscapeString($str);
        }

        return false;
    }

    /**
     * 直播信息补全
     *
     * @param $data                直播信息
     *
     * @return bool                操作是否成功
     */
    private function _liveDataComplete($data)
    {
        $data['uid'] = $this->_uid;
        $data['server'] = isset($data['server']) ? $data['server'] : $this->_publishRtmpUrl;
        $game = $this->_getGameInfo($data['gamename']);
        $data['gametid'] = $game['gametid'];
        $data['gameid'] = $game['gameid'];
        $data['ip'] = $this->_ip;
        $data['port'] = $this->_port;

        return $data;
    }

    /**
     * 获取游戏信息
     *
     * @param $gameName          游戏名称
     *
     * @return $array            游戏信息
     */
    private function _getGameInfo($gameName)
    {
        $r = $this->_db->field('gameid,gametid')->where("name='{$gameName}'")->select(self::GAME_TABLE);
        if (!isset($r[0]['gameid']) || !isset($r[0]['gametid']))
        {
            //return self::liveErrorLog(array(70016,'未获取到相关游戏'));
            return array('gameid' => self::OTHER_GAME, 'gametid' => '');
        }

        return $r[0];
    }

    /**
     * 获取直播时长
     *
     * @return string  直播时长
     */
    public static function getLiveTimeLength($liveid, $db)
    {
        if (!$liveid || !$db)
        {
            return false;
        }
        $liveTimeLength = 0;
        $streamList = $db->doSql("select TIMESTAMPDIFF(SECOND,stime,etime) as streamTimeLen from live where liveid={$liveid}");
        foreach ($streamList as $k => $v)
        {
            $liveTimeLength += (int) $v['streamTimeLen'];
        }

        return $liveTimeLength;
    }

    public static function checkLiveExistByUid($uid, $liveID, $db = null)
    {
        if (!$uid || !$liveID)
        {
            return false;
        }
        if (!$db)
        {
            $db = self::getDB();
        }
        $r = $db->field('liveid')->where("liveid={$liveID} and uid={$uid}")->select(self::LIVE_TABLE);

        return isset($r[0]['liveid']) ? true : false;
    }

    /**
     * @param $liveID
     * @param $db
     *
     * @return bool
     */
    public static function getUidByLiveStream($stream, $db)
    {
        //$r = $db->field( 'uid' )->where( "stream='$stream'" )->order( 'ctime desc' )->limit( 1 )->select( 'live' );
        $stream = $db->where("stream='{$stream}'")->select(self::STREAM_TABLE);
        $liveID = $stream[0]['liveid'];
        $r = $db->field('uid')->where("liveid=$liveID")->select(self::LIVE_TABLE);
        return isset($r[0]['uid']) ? $r[0]['uid'] : false;
    }

    public static function getLiveByStreamName($stream, $db)
    {
        if (!$stream || !$db)
        {
            return false;
        }
//		$live = $db->where( "stream='{$stream}'" )->select( 'live' );

        $stream = $db->where("stream='{$stream}'")->select(self::STREAM_TABLE);
        $liveID = empty($stream[0]['liveid'])?'':$stream[0]['liveid'];
		if(!$liveID)
		{
			return false;
		}
        $live = $db->where("liveid=$liveID")->select(self::LIVE_TABLE);

        return isset($live[0]['liveid']) ? $live[0] : false;
    }

    public static function getLiveIDByStreamName($stream, $db)
    {
        if (!$stream || !$db)
        {
            return false;
        }
        $live = $db->field('liveid')->where("stream='{$stream}'")->select(self::STREAM_TABLE);

        return isset($live[0]['liveid']) ? $live[0]['liveid'] : false;
    }

    public static function getLiveByLiveID($liveID, $db = null)
    {
        if (!$liveID)
        {
            return false;
        }
        if (!$db)
        {
            $db = self::getDB();
        }
        $live = $db->where("liveid={$liveID}")->select(self::LIVE_TABLE);

        return isset($live[0]['liveid']) ? $live[0] : false;
    }

    /*     * *****************************超时函数*********************** */

    public static function getLiveStatusByLiveID($liveID, $db)
    {
        $r = $db->field('status')->where("liveid={$liveID}")->select(self::LIVE_TABLE);

        return isset($r[0]['status']) ? $r[0]['status'] : false;
    }

    public static function getStreamInfoByStreamName($stream, $db)
    {
        $r = $db->field('status,utime')->where("stream='{$stream}'")->select(self::STREAM_TABLE);

        return isset($r[0]) ? $r[0] : false;
    }

    public static function setLiveStatusByLiveID($liveID, $status, $db)
    {
        $r = $db->where("liveid={$liveID}")->update(self::LIVE_TABLE, array('status' => $status, 'utime' => date('Y-m-d H:i:s')));

        return $r;
    }

    public static function setLiveStopStatusByLiveID($liveID, $status, $db)
    {
        if ($status == LIVE_TIMEOUT)
        {
            $etime = time() - STREAM_START_TIMEOUT;
        } else
        {
            $etime = time() - STREAM_DISCONNECT_TIMEOUT;
        }
        $r = $db->where("liveid={$liveID}")->update(self::LIVE_TABLE, array('status' => $status, 'utime' => date('Y-m-d H:i:s'), 'etime' => date('Y-m-d H:i:s', $etime), 'stop_reason' => self::STOP_TYPE_EXCEPTION));

        return $r;
    }

    public static function setStreamStopByStreamName($stream, $status, $db, $liveStatus)
    {
        if ($liveStatus == LIVE_TIMEOUT)
        {
            $etime = time() - STREAM_START_TIMEOUT;
        } else
        {
            $etime = time() - STREAM_DISCONNECT_TIMEOUT;
        }
        $r = $db->where("stream='{$stream}'")->update(self::STREAM_TABLE, array('status' => $status, 'utime' => date('Y-m-d H:i:s', time()), 'etime' => date('Y-m-d H:i:s', $etime)));

        return $r;
    }

    public static function setStreamInfoByStreamName($stream, $status, $db)
    {
        $r = $db->where("stream='{$stream}'")->update(self::STREAM_TABLE, array('status' => $status, 'utime' => date('Y-m-d H:i:s', time())));

        return $r;
    }

    public static function getTimeOutLive($start, $db)
    {
        if (!$db)
        {
            return false;
        }
        $r = $db->field('liveid,status,utime,stream,uid')->where("liveid>={$start} and status<" . LIVE_COMPLETE)->select(self::LIVE_TABLE);

        return isset($r[0]['liveid']) ? $r : false;
    }

    public static function stopWsPubStream($stream = null, $CDNHelper = null)
    {
        if (!$stream)
        {
            return true;
        }
        if (!$CDNHelper)
        {
            $CDNHelper = new CDNHelper();
        }
        $url = 'rtmp://' . $GLOBALS['env-def'][$GLOBALS['env']]['stream-pub'] . '/' . $stream;
        return $CDNHelper->stopCDNStream($url);
    }

    public static function checkPubStream($liveID, $db = null)
    {
        if (!$db)
        {
            $db = self::getDB();
        }
        $r = $db->field('stream')->where("liveid={$liveID}")->select(self::LIVE_TABLE);
        if (!$r)
        {
            return false;
        }
        $stream = $r[0]['stream'];
        $streamstatus = $db->field('status')->where("stream='{$stream}'")->select(self::STREAM_TABLE);
        if (!$streamstatus)
        {
            return false;
        }
        $streamstatus = $streamstatus[0]['status'];
        if ($streamstatus > STREAM_CREATE)
        {
            return false;
        }
        return true;
    }

    /**
     * 根据游戏id获取对应的游戏直播列表
     *
     * @param int    $gameId 游戏id
     *
     * @return mixed
     */
    public function getLiveListsByGid($gameId, $db)
    {
        if ($gameId)
        {
            $lives = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("gameid=$gameId  and status=" . LIVE)->select('live');
        } else
        {
            $lives = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where('status=' . LIVE)->select('live');
        }

        $gameLiveList = false;
        if ($lives)
        {
            foreach ($lives as $live)
            {
                $gameLiveList[$live['uid']] = $live;
            }
        }

        return $gameLiveList;
    }

    /**
     * 停止直播缓存修改
     * @param array $liveData
     * @return boolean
     */
    public function stopLiveRedis($liveData)
    {

        try
        {
            $gameId = GameService::getGameIdByGameName($liveData['gamename']);

            if (!$gameId)
            {
                $gameId = GameService::getGameIdByGameName('其他游戏');
            }


            $params = [];

            $params['uid'] = $liveData['uid'];
            $params['gameid'] = $gameId;

            $params['livestatus'][0]['liveid'] = $liveData['liveid'];
            $params['livestatus'][0]['status'] = LIVE_STOP;

            $params['gamelivecount']['gameid'] = $gameId;

            $event = new EventManager();

            $event->trigger(EventManager::ACTION_LIVE_STOP, $params);

            $event = null;
            return true;
        } catch (Exception $e)
        {
            return false;
        }
    }
    
    /**
     * 根据luid批量获取直播信息
     * @param array $luids
     * @return array
     */
    public function getLiveInfosByLuids($luids)
    {
        $db = self::getDB();
        $sql = "select liveid,poster,uid,gameid,gamename,title,orientation,stime,status from live where uid in ($luids) and `status`=" . LIVE . " order by liveid desc";
        $res = $db->doSql($sql);
        
        if(!$res)
        {
            return false;
        }
        $liveInfos = [];
        foreach ($res as $v)
        {
            $liveInfos[$v['uid']] = $v;
        }
        return $liveInfos;
    }
    
    /**
     * 根据liveids批量获取直播信息
     * @param array $liveids
     * @return array
     */
    public function getLiveInfosByLiveIds($liveIds)
    {
        $db = self::getDB();
        $sql = "select liveid,poster,uid,gameid,gamename,title,orientation,stime,status from live where liveid in ($liveIds) order by liveid desc";
        $res = $db->doSql($sql);
        
        if(!$res)
        {
            return false;
        }
        $liveInfos = [];
        foreach ($res as $v)
        {
            $liveInfos[$v['liveid']] = $v;
        }
        return $liveInfos;
    }
}
