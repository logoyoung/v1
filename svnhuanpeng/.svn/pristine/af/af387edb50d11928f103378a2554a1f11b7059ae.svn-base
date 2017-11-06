<?php
namespace service\room;
use service\common\AbstractService;
use lib\LiveRoom;
use lib\Live;
use service\user\UserDataService;

/**
 * 房间操作服务
 */

class LiveRoomService extends AbstractService
{

    //获取socket服务地异常
    const ERROR_CODE_SOCKET     = -9001;
    //获取最后一场数据库异常
    const ERROR_CODE_LAST_LIVE  = -9002;
    //app端默认
    const ONLINE_USER_SIZE      = 20;
    private $_luid;
    private $_liveRoomDao;
    private $_uid;

    public static $errorMsg = [
        self::ERROR_CODE_SOCKET    => '获取socket服务地异常',
        self::ERROR_CODE_LAST_LIVE => '获取最后一场数据库异常',
    ];

    /**
     * 主播uid
     * @param [type] $luid [description]
     */
    public function setLuid($luid)
    {
        $this->_luid        = $luid;
        $this->_liveRoomDao = null;
        $this->_uid         = null;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    /**
     * 获取天服务socket 服务id
     *
     * @return array
     */
    public function getSocketServer()
    {
        $conf       = $GLOBALS['env-def'][$GLOBALS['env']];
        $serverList = $conf['socket'];
        shuffle($serverList);
        if(!$serverList)
        {
            $code = self::ERROR_CODE_SOCKET;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
        }

        return ['serverList' => $serverList];
    }

    /**
     * 获取直播DB
     * @return
     */
    public function getLiveDb()
    {
        return Live::getDB();
    }

    /**
     * 获取观看人数 (虚拟+真实)
     * @return int
     */
    public function getLiveUserCountFictitious()
    {
        $liveRoomDao = $this->getLiveRoomDao();
        $num         = $liveRoomDao->getLiveRoomUserCountFictitious();
        return (int) $num;
    }

    /**
     * 批量获取房间观看人数 (虚拟+真实)
     * @return int
     */
    public function batchGetLiveUserCountFictitious()
    {
        $result      = [];
        $luids       = $this->getLuid();
        if(!is_array($luids) || empty($luids))
        {
            return $result;
        }

        foreach ($luids as $luid)
        {
            $liveRoom      = new LiveRoom($luid);
            $result[$luid] = $liveRoom->getLiveRoomUserCountFictitious();
        }

        return $result;
    }

    /**
     * 获取直播在线用户列表 (虚拟+真实)
     * @return array
     */
    public function getOnlineUserList()
    {
        $result = ['total' => 0, 'list'  => [],];
        $total  = $this->getLiveUserCountFictitious();
        if($total <= 0)
        {
             $log  = "error |获取观看人数异常;luid:{$this->getLuid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
             write_log($log);

            return $result;
        }

        $result['total'] = $total;
        $size   = self::ONLINE_USER_SIZE;
        $uids   = (array) $this->getLiveRoomDao()->getUserPicList($size);
        $c      = count($uids);

        //补虚假用户
        if( $c < $size && $c < $total)
        {
            $dc    = ($total < $size) ? abs($total - $c) : abs((int) $size - (int) $c);
            $vUids = $this->getLiveRoomDao()->getUserHeadList($dc);
            if(is_array($vUids) && !empty($vUids))
            {
                $uids  =  array_merge($uids, $vUids);
            } else
            {
                 $log  = "warning| 没有获取到虚拟数据;luid:{$this->getLuid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
                write_log($log);
            }
        }

        if(!$uids)
        {
            return $result;
        }

        $userDataService = new UserDataService();
        $userDataService->setCaller('class:'.__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $userDataService->setUid($uids);
        $usersInfo       = $userDataService->batchGetUserInfo();

        if(!$usersInfo)
        {
            return $result;
        }

        $list = [];
        foreach ($uids as $id)
        {
            if(isset($usersInfo[$id]))
            {
                $list[] = [ 'uid'  => $usersInfo[$id]['uid'], 'head' => $usersInfo[$id]['pic'] ];
            }
        }

        $usersInfo       = null;
        $result['list']  = $list;

        return $result;
    }

    public function getLiveRoomDao()
    {
        if(!$this->_liveRoomDao)
        {
            $this->_liveRoomDao = new LiveRoom($this->getLuid());
        }

        return $this->_liveRoomDao;
    }

}