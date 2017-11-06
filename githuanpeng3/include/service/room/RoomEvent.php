<?php
namespace service\room;
use Exception;
use service\event\EventAbstract;
use service\room\helper\RoomidEventParam;
use lib\room\Roomid;
use service\room\helper\RoomidRedis;
use service\room\helper\ManagerRedis;
use lib\room\Roommanager;
use lib\room\Gift;
use service\gift\helper\GiftRedis;
use service\room\helper\PromotionsRoomRedis;
use service\room\RoomManagerService;

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
        $this->_roomidEventParam = new RoomidEventParam();

        switch ($this->_action)
        {

            //主播审核通过
            case self::ACTION_ANCHOR_CHECK_SUCC:
            case self::ACTION_ANCHOR_RESET_CACHE:
            case self::ACTION_ROOMID_DATA_UPDATE:
                if(!$this->_initRoomidParam($param))
                {
                    $this->_infoLog('warnig|处理主播审核通过，roomidEvent;缺少必要参数');
                    return false;
                }

                $this->_status = $this->_updateRoomidRedis();
                break;

            //芝麻认证通过
            case self::ACTION_ZHIMA_CERT_SUCC:
                $this->_infoLog('notce|收到芝麻认证通过更新roomid表信息请求;param:'.hp_json_encode($param).'event:'.__CLASS__);
                if(!$this->_initRoomidParam($param))
                {
                    $this->_infoLog('warnig|芝麻认证通过，roomidEvent;缺少必要参数;class:'.__CLASS__.'line:'.__LINE__);
                    return false;
                }
                $this->_status = $this->_updateRoomidRedis();

                break;

            //添加房管
            case self::ACTION_ADD_ROOM_MANAGER:

                $this->_infoLog('notce|添加房管请求;param:'.hp_json_encode($param).'event:'.__CLASS__);
                $anchorUid  = isset($param['anchorUid'])  ? (int) $param['anchorUid']  : 0;
                $managerUid = isset($param['managerUid']) ? (int) $param['managerUid'] : 0;
                $ctime      = isset($param['ctime'])      ? $param['ctime']            : 0;

                if(!$anchorUid || !$managerUid)
                {
                    $this->_infoLog('error|添加房管请求;缺少必要参数;param:'.hp_json_encode($param).'event:'.__CLASS__);
                    return false;
                }
                //更新redis
                $this->_status = $this->_addRoomManager($anchorUid, $managerUid,$ctime);
                break;

            //取消房管
            case self::ACTION_DELETE_ROOM_MANAGER:

                $this->_infoLog('notce|取消房管请求;param:'.hp_json_encode($param).'event:'.__CLASS__);
                $anchorUid  = isset($param['anchorUid'])  ? (int) $param['anchorUid']  : 0;
                $managerUid = isset($param['managerUid']) ? (int) $param['managerUid'] : 0;
                if(!$anchorUid || !$managerUid)
                {
                    $this->_infoLog('error|收到取消房管请求;缺少必要参数;param:'.hp_json_encode($param).'event:'.__CLASS__);
                    return false;
                }
                //更新redis
                $this->_status = $this->_deleteRoomManager($anchorUid, $managerUid);
                break;

            //reset manager redis cache
            case self::ACTION_RESET_ROOM_MANAGER:

                $this->_infoLog('notce|收到重设管redis缓存请求;param:'.hp_json_encode($param).'event:'.__CLASS__);
                $anchorUid  = isset($param['anchorUid'])  ? (int) $param['anchorUid']  : 0;
                if(!$anchorUid)
                {
                    $this->_infoLog('error|收到重设管redis缓存请求;缺少必要参数;param:'.hp_json_encode($param).'event:'.__CLASS__);
                    return false;
                }
                $this->_status = $this->_resetManagerRedisCache($anchorUid);
                break;

            //礼品信息变更
            case self::ACTION_GITF_UPDATE:

                $this->_status = $this->_updateGiftRedis();

                try {

                    $service = new \service\room\RoomGiftService();
                    $service->update();

                } catch (Exception $e) {
                    $this->_infoLog("error|收到重置gift redis缓存请求; RoomGiftService;异常:{$e->getMessage()}line:".__LINE__.'class:'.__CLASS__);
                }

                break;

            //直播间活动信息变更
            case self::ACTION_ROOM_PROMOTION_UPDATE:
                $this->_status = $this->_updatePromotionRedis($param);
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

    /**
     * 添加房管
     * [_addRoomManager description]
     * @param [type] $anchorUid  [description]
     * @param [type] $managerUid [description]
     * @param [type] $ctime      [description]
     */
    private function _addRoomManager($anchorUid, $managerUid,$ctime)
    {
        $managerRedis = $this->getRoomManagerRedis();
        $logMsg       = "anchorUid:{$anchorUid};managerUid:{$managerUid}";
        if(!$managerRedis->getRedis()->ping())
        {
            $this->_infoLog("error|添加房管redis连接异常;不作更新;{$logMsg};;line:".__LINE__);
            return false;
        }

        //更新redi缓存
        if($managerRedis->add($anchorUid,[$managerUid => $ctime]))
        {
            $this->_infoLog("warning|添加房管理更新redis异常;{$logMsg};;line:".__LINE__);
        }

        $this->_infoLog("success|添加房间管理更新房管redis成功;{$logMsg};line:".__LINE__);

        return true;
    }

    /**
     * 取消房间管
     * @param  [type] $anchorUid  [description]
     * @param  [type] $managerUid [description]
     * @return [type]             [description]
     */
    private function _deleteRoomManager($anchorUid, $managerUid)
    {
        $managerRedis = $this->getRoomManagerRedis();
        $logMsg       = "anchorUid:{$anchorUid};managerUid:{$managerUid}";
        if(!$managerRedis->getRedis()->ping())
        {
            $this->_infoLog("error|取消房管redis连接异常;不作更新{$logMsg};;line:".__LINE__);
            return false;
        }

        //更新redi缓存
        if($managerRedis->deleteByAnchorUidManagerUid($anchorUid,$managerUid))
        {
            $this->_infoLog("warning|取消房管理更新redis异常;{$logMsg};;line:".__LINE__);
        }

        $this->_infoLog("success|取消房间管理更新房管redis成功;{$logMsg};line:".__LINE__);

        return true;
    }

    private function _resetManagerRedisCache($anchorUid)
    {
        $managerRedis = $this->getRoomManagerRedis();
        $logMsg       = "anchorUid:{$anchorUid}";
        if(!$managerRedis->getRedis()->ping())
        {
            $this->_infoLog("error|重设房管redis缓存;redis连接异常;不作更新;{$logMsg};line:".__LINE__);
            return false;
        }

        $managerDb     = $this->getRoomManagerDb();
        $managerDbData = $managerDb->getDataByAnchorUid($anchorUid);
        if($managerDbData === false)
        {
            $this->_infoLog("error|重设房管;mysql连接异常;不作更新;{$logMsg};line:".__LINE__);
            return false;
        }

        $redisData = [0 => time()];
        $managerRedis->delete($anchorUid);
        if($managerDbData)
        {
            foreach ($managerDbData as $v)
            {
                $redisData[$v['uid']] = strtotime($v['ctime']);
            }
        }

        if($managerRedis->add($anchorUid,$redisData))
        {
            $this->_infoLog("success|重设房管redis缓存成功;{$logMsg};line:".__LINE__);
            return true;
        }

        $this->_infoLog("error|重设房管redis缓存失败;{$logMsg};line:".__LINE__);

        return false;
    }

    /**
     * 重置gift redis缓存
     * @return [type] [description]
     */
    private function _updateGiftRedis()
    {
        $this->_infoLog("notice|收到重置gift redis缓存请求;line:".__LINE__);
        $redis = $this->getGiftRedis();
        if(!$redis->getRedis()->ping())
        {
            $this->_infoLog("error|重置gift redis缓存;redis连接异常;不作更新;line:".__LINE__);
            return false;
        }

        $giftDb = $this->getGiftDb();
        $data   = $giftDb->getAllData();
        if($data === false)
        {
            $this->_infoLog("error|重置gift redis缓存;数据异常;不作更新;line:".__LINE__);
            return false;
        }

        if(!$data)
        {
            $this->_infoLog("error|重置gift redis缓存;数据库里没有相关数据;不作更新;line:".__LINE__);
            return false;
        }

        if($redis->setGiftData($data))
        {
            $this->_infoLog("success|重置gift redis缓存成功;line:".__LINE__);
            return true;
        }

        $this->_infoLog("error|重置gift redis缓存;写入数据失败;line:".__LINE__);
        return false;
    }

    /**
     * 直播间活动信息变更
     * @param  array  $result [description]
     * @return [type]         [description]
     */
    private function _updatePromotionRedis(array $result)
    {
        if(!$result)
        {
            $result = RoomManagerService::getRoomPromotionsFromDb();
        }

        $this->_infoLog("notice|直播间活动信息变更，更新redis缓存请求;param:".hp_json_encode($result).';line:'.__LINE__);
        $promotionRedis = new PromotionsRoomRedis;
        $redisStatus    = $promotionRedis->getRedis()->ping() ? true : false;
        if(!$redisStatus)
        {
           $this->_infoLog("error|直播间活动信息变更redis异常，line:".__LINE__);
           return false;
        }

        if($promotionRedis->setPromotionAd($result))
        {
            $this->_infoLog("success|直播间活动信息变更，更新redis缓存成功，line:".__LINE__);
            return true;
        }

        $this->_infoLog("error|直播间活动信息变更，更新redis缓存异常，line:".__LINE__);
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

    public function getRoomManagerRedis()
    {
        return new ManagerRedis;
    }

    public function getRoomManagerDb()
    {
        $db = new Roommanager;
        $db->setMaster(true);
        return $db;
    }

    public function getGiftDb()
    {
        $db = new Gift;
        $db->setMaster(true);
        return $db;
    }

    public function getGiftRedis()
    {
        return new GiftRedis;
    }
}