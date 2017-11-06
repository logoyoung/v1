<?php
namespace service\live;
use service\common\AbstractService;
use lib\Video;
use service\user\UserDataService;
use lib\User;

/**
 * 录相服务
 * @author xuyong <[<xuyong@6.cn>]>
 * @date  2017-4-25
 */

class VideoService extends AbstractService
{

    private $_uid;
    private $_page;
    private $_size;
    private $_videoDb;
    private $_videoId;
    private $_vfile;
    //封面图
    private $_poster;
    private $_videoDao;
    private $_videoListTotalNum = 0;
    const PC_DEFAULT_VIDEO_SIZE = 20;
    //从数据库获取视频列表异常
    const ERROR_VIDEO_LIST = -13001;

    public static $errorMsg = [
        self::ERROR_VIDEO_LIST => '从数据库获取视频列表异常',
    ];

    public function setUid($uid)
    {

        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->_page ? $this->_page : 1;
    }


    public function setVideoId($videoId)
    {
        if(is_array($videoId))
        {
            $videoId = array_unique($videoId);
        }

        $this->_videoId = $videoId;
        return $this;
    }

    public function getVideoId()
    {
        return $this->_videoId;
    }


    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size ? $this->_size : 8;
    }

    public function setVfile($vfile)
    {
        $this->_vfile = $vfile;
        return $this;
    }

    public function getVifile()
    {
        return $this->_vfile;
    }

    /**
     * 封面图
     * @param [type] $poster [description]
     */
    public function setPoster($poster)
    {
        $this->_poster = $poster;
        return $this;
    }

    public function getPoster()
    {
        return $this->_poster;
    }

    /**
     *  获取录像列表
     * @return array
     */
    public function getVideoList()
    {
        $videoList = Video::getVideoListByUid($this->getUid(),self::PC_DEFAULT_VIDEO_SIZE,$this->getVideoDb());
        if(!$videoList){
            $code = self::ERROR_VIDEO_LIST;
            $msg  = self::$errorMsg[$code];
            $log  = "warning |error_code:{$code};msg:{$msg};luid:{$this->getUid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return false;
        }

        $result    = [];
        $this->_videoListTotalNum = count($videoList);
        $p         = $this->getPage();
        $page      = returnPage($this->_videoListTotalNum, $this->getSize(), $p);
        $result    = array_slice($videoList, ($page - 1) * $this->getSize(), $this->getSize());
        $uids      = array_column($result,'uid') ;
        $videoIds  = array_column($result,'videoid') ;

        $userService = new UserDataService();
        $userService->setCaller('class:'.__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__);
        $userService->setUid($uids);
        $userService->setUserInfoDetail(User::USER_INFO_BASE);
        //批量获取用户信息
        $usersInfo = $userService->batchGetUserInfo();

        $this->setVideoId($videoIds);
        //批量获取录像评论总数
        $commentTotalNum = $this->getVideoIdsCommentTotalNum();

        $arr  = [];
        foreach ($result as $k => $video)
        {
            $arr[$k]['videoID']    = $video['videoid'];
            $arr[$k]['gameID']     = $video['gameid'];
            $arr[$k]['gameTypeID'] = $video['gametid'];
            $arr[$k]['gameName']   = $video['gamename'];
            $arr[$k]['viewCount']  = $video['viewcount'];
            $arr[$k]['uid']        = $video['uid'];
            $arr[$k]['nick']       = isset($usersInfo[$video['uid']]['nick']) ? $usersInfo[$video['uid']]['nick'] : '';
            $arr[$k]['head']       = isset($usersInfo[$video['uid']]['pic'])  ? $usersInfo[$video['uid']]['pic'] : DEFAULT_PIC;
            $arr[$k]['title']      = $video['title'];
            $arr[$k]['videoTimeLength'] = $video['length'];
            $arr[$k]['videoUploadDate'] = isset($video['ctime']) ?  strtotime($video['ctime']) : '';
            $this->setVideoId($video['videoid']);
            $this->setVfile($video['vfile']);
            //是否是教学视频
            $arr[$k]['videoUrl']   = $this->isTeachVideoId() ? $this->getTeachVideoUrl() : ($this->getVideoUrl() ?:'');
            $arr[$k]['orientation'] = $video['orientation'];

            //是否有封面图
            if($video['poster'])
            {
                $this->setPoster($video['poster']);
                $arr[$k]['poster'] = $this->isTeachVideoId() ? $this->getTeachVideoPosterUrl() : $this->getVideoPosterUrl();
                $arr[$k]['ispic']  = '1';
            } else
            {
                //默认封面图
                $arr[$k]['poster'] = CROSS;
                $arr[$k]['ispic']  = '0';
            }
            //获取视频收藏人数
            $arr[$k]['collectCount'] = $this->getVideoCollectionNum();
            //评论总数
            $arr[$k]['commentCount'] = isset($commentTotalNum[$video['videoid']]) ? $commentTotalNum[$video['videoid']] : 0;
            //评分
            $arr[$k]['rate']         = $this->getVideoRate();

        }

        return $arr;
    }


    /**
     * 获取录相总数
     * @return int
     */
    public function getVideoListTotalNum()
    {
        return $this->_videoListTotalNum;
    }

    /**
     * 获取教学视频 url
     * @return string
     */
    public function getTeachVideoUrl()
    {
        $conf = self::getConf();
        return $conf['huan-video'] .'/'. $this->getVifile();
    }

    /**
     * 获取普通视频url
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->getVifile() ? sfile($this->getVifile()) :'';
    }

    /**
     * 判断是否是教学视频videoid
     * @return boolean [description]
     */
    public function isTeachVideoId()
    {
        $techVideoIds = $this->getTeachVideoIds();
        return in_array($this->getVideoId(), $techVideoIds);
    }

    /**
     * 获取教学视频封面图
     * @return string
     */
    public function getTeachVideoPosterUrl()
    {
        $conf = self::getConf();
        return DOMAIN_PROTOCOL . $conf['domain-img'] . '/'. $this->getPoster();
    }

    /**
     * 获取普通视频封面图
     * @return string
     */
    public function getVideoPosterUrl()
    {
        return sposter($this->getPoster());
    }

    /**
     * 获取所有教学视频videoid
     * @return array
     */
    public function getTeachVideoIds()
    {
        return explode(',',HUANPENG_VIDEO);
    }

    /**
     * 获取视频收藏人数
     * @return int
     */
    public function getVideoCollectionNum()
    {
        $num = Video::getVideoFollowCount($this->getVideoId(),$this->getVideoDb());
        if($num === false)
        {
            //log
        }

        return $num;
    }

    /**
     * 获取视频评分
     * @return float
     */
    public function getVideoRate()
    {
        $rate = Video::getVideoRate($this->getVideoId(), $this->getVideoDb());
        if($rate === false )
        {
            //log
        }

        return round((float) $rate, 4);
    }

    /**
     * 获取一个视频评论总数
     * @return int
     */
    public function getVideoIdCommentTotalNum()
    {
        $result = $this->getVideoIdsCommentTotalNum();
        if(!$result)
        {
            //log
        }

        return isset($result[$this->getVideoId()]) ? (int) $result[$this->getVideoId()] : 0;
    }

    /**
     * 批量获取录像评论总数
     * @return array
     */
    public function getVideoIdsCommentTotalNum()
    {
        $result = Video::getVideoCommentCountByVideoId($this->getVideoId(),$this->getVideoDb());
        if(!$result)
        {
            //log
        }
        return $result;
    }

    public function getVideoDao()
    {
        if(!$this->_videoDao)
        {
            $this->_videoDao = new Video();
        }
        return $this->_videoDao;
    }

    public function getVideoDb()
    {
        if(!$this->_videoDb)
        {
            $this->_videoDb = Video::getDB();
        }

        return $this->_videoDb;
    }

    public static function getConf()
    {
        return $GLOBALS['env-def'][$GLOBALS['env']];
    }
}