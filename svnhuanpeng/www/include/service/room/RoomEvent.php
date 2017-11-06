<?php
namespace service\room;
use Exception;
use service\event\EventAbstract;
use service\room\helper\RoomidEventParam;
use lib\room\Roomid;
use service\room\helper\RoomidRedis;

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/21
 * Time: 下午10:54
 */

class RoomEvent extends EventAbstract
{
    private $_param;
    private $_action;
    private $_status;
    private $_roomidRedis;
    private $_roomidEventParam;
    private $_infoLog  = 'room_event_access';

    public function trigger($action,$param)
    {
        $this->_status  = false;
        $this->_action  = (int) $action;

        switch ($this->_action)
        {

            //主播审核通过
            case self::ACTION_ANCHOR_CHECK_SUCC:
            case self::ACTION_ANCHOR_RESET_CACHE:
            case self::ACTION_ROOMID_DATA_UPDATE:
                $this->_roomidEventParam = new RoomidEventParam();
                if(!$this->_initRoomidParam($param))
                {
                    $this->_infoLog('warnig|处理主播审核通过，roomidEvent;缺少必要参数');
                    return false;
                }

                $this->_status = $this->_updateRoomidRedis();
                break;

            default:
                return true;
        }

        return $this->_status;
    }

    /**
     * 初始化房间roomid相关参数
     * @param  array $param [description]
     * @return boolean
     */
    private function _initRoomidParam($param)
    {

        if(isset($param['uid']) && $param['uid'])
        {
            $this->_roomidEventParam->setUid($param['uid']);
        }

        if(isset($param['roomid']) && $param['roomid'])
        {
            $this->_roomidEventParam->setRoomid($param['roomid']);
        }

        if(!$this->_roomidEventParam->getUid() && !$this->_roomidEventParam->getRoomid())
        {
            return false;
        }
        $this->_infoLog('info|收到处理,处理主播审核通过;roomidEvent;param:'.hp_json_encode($this->_roomidEventParam->getParam()));
        return true;
    }

    /**
     * 更新roomid redis信息
     * @return boolean
     */
    private function _updateRoomidRedis()
    {
        $roomidDao   = $this->getRoomidDb();
        $roomidRedis = new RoomidRedis();
        if(!$roomidRedis->getRedis()->ping())
        {
            $this->_errorLog("error|redis服务异常，不作事件处理uid:{$this->_roomidEventParam->getUid()}");
            return false;
        }

        $result      = false;
        if($this->_roomidEventParam->getUid())
        {
            $uid    = $this->_roomidEventParam->getUid();
            $roomid = $roomidDao->getRoomidByUid($uid);
            if($roomid === false)
            {
                $this->_errorLog("error|uid:{$uid}; roomidEvent; 处理主播审核通过，数据库异常");
                return false;
            }

        } else
        {
            $roomid = $this->_roomidEventParam->getRoomid();
            $uid    = $roomidDao->getUidByRoomid($roomid);
            if($uid === false)
            {
                $this->_errorLog("error|roomid:{$roomid}; roomidEvent;处理主播审核通过，数据库异常;roomidEvent");
                return false;
            }

        }

        $result[] = $roomidRedis->setRoomidToUid($roomid,$uid);
        $result[] = $roomidRedis->setUidToRoomid($uid,$roomid);
        if(array_search(false,$result,true) === false)
        {
            $this->_infoLog("success|处理主播审核通过,roomidEvent; uid:{$uid},roomid:{$roomid},更新redis数据成功");
            return true;
        }

        $this->_errorLog("error|处理主播审核通过,更新redis数据失败roomidEvent; uid:{$uid},roomid:{$roomid}");
        return false;
    }

    private function _infoLog($msg)
    {
        write_log($msg.'|class:'.__CLASS__, $this->_infoLog);
    }

    private function _errorLog($msg)
    {
        write_log($msg.'|class:'.__CLASS__, $this->_infoLog);
    }

    public function getRoomidDb()
    {
        $roomidDao = new Roomid();
        $roomidDao->setMaster(true);
        return $roomidDao;
    }
}