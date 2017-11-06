<?php

namespace service\live;

use service\event\EventAbstract;
use service\live\helper\LiveRedis;
use service\game\helper\GameRedis;

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/21
 * Time: 下午10:55
 */
class LiveEvent extends EventAbstract
{

    private $_param;
    private $_liveRedis;
    private $_gameRedis;
    private $_status;

    public function trigger($action, $param)
    {
        $this->_status = false;
        $this->_liveRedis = new LiveRedis();
        $this->_gameRedis = new GameRedis();
        $this->_initParam($param);

        switch ($action)
        {
            case self::ACTION_LIVE_START:
                //列表中加入新的liveid
                $this->_addLiveidToLiveList();

                //在游戏直播列表中加入新的liveid
                $this->_addLiveidToGameLiveList();

                //添加新的主播的最近直播id关系
                $this->_addUidToLiveId();

                //添加livestatus
                $this->_updateLiveStatus();

                //添加或更新liveinfo
                $this->_updateLiveInfo();

                //增加游戏直播数量
                $this->_updateGameLiveCount(1);

                $this->_status = true;

                break;
            case self::ACTION_UPDATE_LIVE_INFO:
                //更新liveinfo
                $this->_updateLiveInfo();
                break;
            case self::ACTION_LIVE_STOP:
                //列表中移除liveid
                $this->_removeLiveidFromLiveList();

                //从游戏直播列表移除liveid
                $this->_removeLiveidFromGameLiveList();

                //修改livestatus
                $this->_updateLiveStatus();

                //减少游戏直播数量
                $this->_updateGameLiveCount(-1);

                $this->_status = true;

                break;
            default :
                return true;
        }
        return $this->_status;
    }

    private function _initParam($param)
    {
        $this->_param['livelist'] = isset($param['livelist']) ? $param['livelist'] : false;
        $this->_param['livestatus'] = isset($param['livestatus']) ? $param['livestatus'] : false;
        $this->_param['liveinfo'] = isset($param['liveinfo']) ? $param['liveinfo'] : false;
        $this->_param['liveid'] = isset($param['liveid']) ? $param['liveid'] : false;
        $this->_param['gameid'] = isset($param['gameid']) ? $param['gameid'] : false;
        $this->_param['uid'] = isset($param['uid']) ? $param['uid'] : false;
        $this->_param['gamelivecount'] = isset($param['gamelivecount']) ? $param['gamelivecount'] : false;
        $this->_param['gamelivelist'] = isset($param['gamelivelist']) ? $param['gamelivelist'] : false;        
    }

    //列表中加入新的liveid
    private function _addLiveidToLiveList()
    {
        if ($this->_param['livelist'])
        {
            foreach ($this->_param['livelist'] as $type => $v)
            {
                $this->_liveRedis->setLiveList($type, $v['score'], $v['uid']);
            }
        }
        return true;
    }

    //游戏直播列表中加入新的liveid
    private function _addLiveidToGameLiveList()
    {
        if ($this->_param['gamelivelist'])
        {
            foreach ($this->_param['gamelivelist'] as $type => $v)
            {
                $this->_gameRedis->setGameLiveList($type, $v['gameid'], $v['score'], $v['uid']);
            }
        }
        return true;
    }

    //添加或更新livestatus
    private function _updateLiveStatus()
    {
        return $this->_liveRedis->setLiveStatus($this->_param['livestatus']);
    }

    //添加或更新liveinfo
    private function _updateLiveInfo()
    {
        return $this->_liveRedis->setLiveInfo($this->_param['liveinfo']);
    }

    //列表中移除liveid
    private function _removeLiveidFromLiveList()
    {
        return $this->_liveRedis->removeUid($this->_param['uid']);
    }

    //游戏直播列表中移除liveid
    private function _removeLiveidFromGameLiveList()
    {
        return $this->_gameRedis->removeUid($this->_param['uid'], $this->_param['gameid']);
    }

    //更新游戏直播数
    private function _updateGameLiveCount($incr)
    {
        return $this->_gameRedis->updateGameLiveCount($this->_param['gamelivecount']['gameid'], $incr);
    }

    //添加新的主播的最近直播id关系
    private function _addUidToLiveId()
    {
        return $this->_liveRedis->setUidToLiveid($this->_param['uid'], $this->_param['liveid']);
    }

}
