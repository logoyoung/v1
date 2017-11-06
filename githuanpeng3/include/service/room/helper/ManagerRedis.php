<?php
namespace service\room\helper;
use system\RedisHelper;
/**
 * 房间管理redis
 *
 *
 */

class ManagerRedis
{
    const ROOM_REDIS_CONF  = 'huanpeng';
    const ROOM_MANAGER     = 'HP_ROOM_MANAGER_%s';

    public function getRedis()
    {
        return RedisHelper::getInstance(self::ROOM_REDIS_CONF);
    }

    /**
     * 添加房管
     * @param int $anchorUid  主播uid
     * @param int $managerUid 房管uids
     * @pram int $t 添加时间
     */
    public function add($anchorUid, array $managerUid)
    {
        if(!$anchorUid || !$managerUid)
        {
            return false;
        }

        $key = $this->getManagerKeyByAnchorUid($anchorUid);
        $try = 2;

        do {

            $status = $this->getRedis()->hMSet($key,$managerUid);
            if($status !== false)
            {
                return true;
            }

            usleep(1);
        } while ($try-- > 0);

        return false;

    }

    /**
     * 取消房管
     * @param  int $anchorUid  主播uid
     * @param  int $managerUid 房管uid
     * @return [type]             [description]
     */
    public function deleteByAnchorUidManagerUid($anchorUid, $managerUid)
    {
        if(!$anchorUid || !$managerUid)
        {
            return false;
        }

        $key = $this->getManagerKeyByAnchorUid($anchorUid);
        $try = 2;

        do {

            $status = $this->getRedis()->hDel($key,$managerUid);
            if($status !== false)
            {
                return true;
            }

            usleep(1);
        } while ($try-- > 0);

        return false;
    }

    public function delete($anchorUid)
    {
        if(!$anchorUid)
        {
            return false;
        }

        $key = $this->getManagerKeyByAnchorUid($anchorUid);
        $try = 2;

        do {

            $status = $this->getRedis()->delete($key);
            if($status !== false)
            {
                return true;
            }

            usleep(1);
        } while ($try-- > 0);

        return false;
    }

    /**
     *  不否为房管
     * @param  [type]  $anchorUid  [description]
     * @param  [type]  $managerUid [description]
     * @return boolean             [description]
     */
    public function isExistsByAnchorUidManagerUid($anchorUid, $managerUid)
    {
        if(!$anchorUid || !$managerUid)
        {
            return false;
        }

        return $this->getRedis()->hExists($this->getManagerKeyByAnchorUid($anchorUid),$managerUid);
    }

    /**
     *  key 是否存在
     * @param  [type]  $anchorUid [description]
     * @return boolean            [description]
     */
    public function isExsits($anchorUid)
    {
        if(!$anchorUid)
        {
            return false;
        }

        return $this->getRedis()->exists($this->getManagerKeyByAnchorUid($anchorUid)) ? true : false;
    }

    /**
     * 获取房管数量
     * @param  int $anchorUid [description]
     * @return int           [description]
     */
    public function getTotalNum($anchorUid)
    {
        if(!$anchorUid)
        {
            return 0;
        }

        $key      = $this->getManagerKeyByAnchorUid($anchorUid);
        $totalNum = $this->getRedis()->hLen($key);
        if($totalNum !== false)
        {
            return (int) $totalNum;
        }

        return false;
    }

    /**
     * 通过主播uid获取房管列表
     * @param  int $anchorUid
     * @param  int $page
     * @param  int $size
     * @return array
     */
    public function getListByAnchorUid($anchorUid)
    {
        if(!$anchorUid)
        {
            return [];
        }

        $key = $this->getManagerKeyByAnchorUid($anchorUid);
        return $this->getRedis()->hGetAll($key);
    }

    public function getManagerKeyByAnchorUid($anchorUid)
    {
        return sprintf(self::ROOM_MANAGER, $anchorUid);
    }
}