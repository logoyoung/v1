<?php
namespace service\anchor\helper;
use system\RedisHelper;

class AnchorRedis
{
    //用户redis池子 string
    const ANCHOR_REDIS_CONF  = 'huanpeng';

    const ANCHOR_DATA_KEY    = 'HP_ANCHOR_DATA_%s';

    const ANCHOR_IS_EXIST    = 'is_exist';

    const ANCHOR_IS_CERT     = 'is_cert';

    const ANCHOR_DATA_COL    = 'anchor';

    public function getRedis()
    {
        return RedisHelper::getInstance(self::ANCHOR_REDIS_CONF);
    }

    public function getAnchorDataKey($uid)
    {
        return sprintf(self::ANCHOR_DATA_KEY, $uid);
    }

    /**
     * 主播是否存在
     * @return boolean [description]
     */
    public function isExist($uid)
    {

        if(!$uid)
        {
            return false;
        }

        $key   =  $this->getAnchorDataKey($uid);

        if(!$this->getRedis()->exists($key))
        {
            return -1;
        }

        $status = $this->_getAnchorColunm($uid,[self::ANCHOR_IS_EXIST]);

        return isset($status[self::ANCHOR_IS_EXIST]) ? (int) $status[self::ANCHOR_IS_EXIST] : false;

    }

    public function setExist($uid, $status = 1)
    {
        if(!$uid)
        {
            return false;
        }

        $colunm[self::ANCHOR_IS_EXIST] = $status;
        return $this->_setAnchorColunm($uid,$colunm);
    }

    public function setCertStatus($uid, $status = 1)
    {
        if(!$uid)
        {
            return false;
        }

        $colunm[self::ANCHOR_IS_CERT] = $status;
        return $this->_setAnchorColunm($uid,$colunm);
    }

    public function getCertStatus($uid)
    {
        if(!$uid)
        {
            return false;
        }

        $status = $this->_getAnchorColunm($uid,[self::ANCHOR_IS_CERT]);
        return isset($status[self::ANCHOR_IS_CERT]) ? (int) $status[self::ANCHOR_IS_CERT] : 0;
    }

    public function setAnchorData($uid,$data)
    {
        if(!$uid)
        {
            return false;
        }

        $colunm[self::ANCHOR_DATA_COL] = hp_json_encode(array_values_to_string($data));
        return $this->_setAnchorColunm($uid,$colunm);
    }

    public function getAnchorData($uid)
    {
        if(!$uid)
        {
            return false;
        }

        $colunm = [self::ANCHOR_DATA_COL];
        $data   = $this->_getAnchorColunm($uid,$colunm);
        if($data === false)
        {
            return false;
        }

        if(isset($data[self::ANCHOR_DATA_COL]) && $data[self::ANCHOR_DATA_COL] )
        {
            $data = json_decode($data[self::ANCHOR_DATA_COL],true);
            return $data;
        }

        return [];
    }

    /**
     * 通过colunm 获取数据结构
     * @param  int $uid    [description]
     * @param  array  $colunm [description]
     * @return array
     */
    private function _getAnchorColunm($uid,array $colunm)
    {
        $key = $this->getAnchorDataKey($uid);
        return $this->getRedis()->hMGet($key,$colunm);
    }

    /**
     * 数据结构写入数据
     * @param int $uid 用户uid
     * @param array  $val
     */
    private function _setAnchorColunm($uid,array $val)
    {
        $key = $this->getAnchorDataKey($uid);
        $try = 2;

        do {

            $status = $this->getRedis()->hMSet($key,$val);
            if($status)
            {
                return true;
            }

            usleep(1);

        } while ($try-- > 0);

        return false;
    }

}