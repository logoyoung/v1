<?php
namespace service\anchor;
use Exception;
use service\event\EventAbstract;
use lib\anchor\Anchor;
use service\anchor\helper\AnchorEventParam;
use service\anchor\helper\AnchorRedis;

class AnchorEvent extends EventAbstract
{

    private $_action;
    private $_status;
    private $_anchorEventParam;
    private $_infoLog  = 'room_event_access';

    public function trigger($action,$param)
    {
        $this->_status  = false;
        $this->_action  = (int) $action;
        $this->_anchorEventParam = new AnchorEventParam();

        switch ($this->_action)
        {

            //主播审核通过
            case self::ACTION_ANCHOR_CHECK_SUCC:
            case self::ACTION_ANCHOR_RESET_CACHE:
            case self::ACTION_ANCHOR_DATA_UPDATE:

                if(!$this->_initAnchorParam($param))
                {
                    $this->_infoLog('warnig|更新主播redis缓存;缺少必要参数;event:'.__CLASS__);
                    return false;
                }

                $this->_status = $this->_updateAnchorRedis();
                break;

            //芝麻认证通过
            case self::ACTION_ZHIMA_CERT_SUCC:

                $this->_infoLog('notce|收到芝麻认证通过更新anchor表信息请求;param:'.hp_json_encode($param).'event:'.__CLASS__);
                if(!$this->_initAnchorParam($param))
                {
                    $this->_infoLog('warnig|芝麻认证通过更新anchor,缺少必要参数;event:'.__CLASS__);
                    return false;
                }
                $this->_status = $this->_updateAnchorRedis();
                break;

            default:
                return true;
        }

        return $this->_status;
    }

    private function _initAnchorParam($param)
    {
        if(!isset($param['uid']) || !$param['uid'])
        {
            return false;
        }

        $this->_anchorEventParam->setUid($param['uid']);
        return true;
    }

    private function _updateAnchorRedis()
    {
        $uid = $this->_anchorEventParam->getUid();
        if(!$uid)
        {
            return false;
        }

        $anchorRedis = new AnchorRedis();
        if(!$anchorRedis->getRedis()->ping())
        {
            $this->_errorLog("error|redis服务异常，不作事件处理uid:{$uid }");
            return false;
        }

        $anchorDao  = $this->getAnchorDb();
        $anchorData = $anchorDao->getAnchorDataByUid($uid);
        if($anchorData === false)
        {
            $this->_errorLog("error|uid:{$uid},更新主播redis缓存,数据库异常,anchor");
            return false;
        }

        $anchorData  = isset($anchorData[$uid]) ? $anchorData[$uid] : [];
        if(!$anchorData)
        {
            $this->_infoLog("info|uid:{$uid},更新主播redis缓存,主播不存在,anchor");
            if($anchorRedis->setExist($uid, 0))
            {
                $this->_infoLog("info|uid:{$uid}, 更新主播redis缓存,主播不存在,anchor,redis 数据种为空操作成功");
                return true;
            }

            $this->_errorLog("info|uid:{$uid}, 更新主播redis缓存,主播不存在,anchor 数据种为空操作失败");
            return false;
        }

        if(!$anchorRedis->setExist($uid, 1))
        {
            $this->_errorLog("error|uid:{$uid}, 更新主播redis缓存 存在状态失败");
            return false;
        }

        $this->_infoLog("info|uid:{$uid}, 主播更新redis 存在状态成功");

        if(!$anchorRedis->setCertStatus($uid, $anchorData['cert_status']))
        {
            $this->_errorLog("error|uid:{$uid}, 更新主播redis缓存校验状态失改");
            return false;
        }

        $this->_infoLog("info|uid:{$uid},cert_status:{$anchorData['cert_status']} 更新主播redis缓存,anchor,更新redis 校验状态成功");

        if(!$anchorRedis->setAnchorData($uid,$anchorData))
        {
            $this->_errorLog("error|uid:{$uid}, 更新主播redis缓存数据失败");
            return false;
        }

        $this->_infoLog("info|uid:{$uid}, 更新主播redis数据成功");
        $this->_infoLog("success|uid:{$uid}, 更新主播整个事件全部处理成功;anchorData:".hp_json_encode($anchorData));
        return true;
    }

    public function getAnchorDb()
    {
        $anchorDao = new Anchor();
        $anchorDao->setMaster(true);
        return $anchorDao;
    }

    private function _infoLog($msg)
    {
        write_log($msg.'|class:'.__CLASS__, $this->_infoLog);
    }

    private function _errorLog($msg)
    {
        write_log($msg.'|class:'.__CLASS__, $this->_infoLog);
    }
}