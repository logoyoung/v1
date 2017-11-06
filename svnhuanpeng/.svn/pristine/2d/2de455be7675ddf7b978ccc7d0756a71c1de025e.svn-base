<?php
namespace app\service\user\helper;
use system\RedisHelper;
use system\Crc;

class UserRedisHelper
{

    //用户redis池子 string
    const USER_REDIS_CONF  = 'huanpeng';

    //用户手机号对应的uid string
    const UID_PHONE_KEY    = 'HP_USER_PHONE_TO_UID_%s';

    //用户信息key hash
    const USER_DATA_KEY    = 'HP_USER_DATA_%s';

    //用户nick 对应的uid string
    const USER_NICK_KEY    = 'HP_USER_NICK_TO_UID_%s';

    //用户封禁key string
    const USER_DISABLE_LOGIN         = 'HP_USER_DISABLE_LOGIN_%s';

    //用户禁言全局key string
    const USER_DISABLE_SEND_MSG_ALL  = 'HP_USER_DISABLE_SEND_MSG_ALL_%s';

    //用户直播间禁言key hash
    const USER_ROOM_SILENCED         = 'HP_USER_ROOM_SILENCED_%s';

    //禁直播 string
    const USER_DISABLE_LIVE          = 'HP_USER_DISABLE_LIVE_%s';

    private $_uid;
    private $_phone;
    private $_enc;

    private $_errorLog     = 'user_redis_error';

    private $_getUserStatic = true;
    private $_getUserActive = false;
    private $_colunm  = [];
    private $_isExist = false;
    private $_nick;

    const USER_STATIC_NAME  = 'userstatic';
    const USER_ACTIVE_NAME  = 'useractive';
    const USER_PASS_NAME    = 'password';
    const USER_ENC_NAME     = 'encpass';
    const USER_IS_EXSITS    = 'exist';

    public function setUid($uid)
    {
        $this->_uid     = $uid;
        $this->_colunm  = [];
        return $this;
    }

    public function setPhone($phone)
    {
        $this->_phone = $phone;
        return $this;
    }

    public function setGetUserStatic($getUserStatic = true)
    {
        if($getUserStatic)
        {
            $this->_colunm[] = self::USER_STATIC_NAME;
        }
        return $this;
    }

    public function setGetUserActive($getUserActive = true)
    {
        if($getUserActive)
        {
            $this->_colunm[] = self::USER_ACTIVE_NAME;
        }

        return $this;
    }

    /**
     * 用户是否存在
     * @return boolean [description]
     */
    public function isExist()
    {

        if(!$this->_uid && !$this->_phone)
        {
            return false;
        }

        $key   =  $this->_uid ? $this->getUserDataKey($this->_uid) : $this->getPhoneToUidKey($this->_phone);

        if(!$this->getRedis()->exists($key))
        {
            return -1;
        }

        $status = $this->_getUserColunm($this->_uid,[self::USER_IS_EXSITS]);

        return (isset($status[self::USER_IS_EXSITS]) && (int) $status[self::USER_IS_EXSITS] == 1) ? true : false;

    }

    /**
     * 通过手机号获取uid
     * @return false | uid
     */
    public function getUidByPhone()
    {
        if (!$this->_phone)
        {
            return false;
        }

        $key = $this->getPhoneToUidKey($this->_phone);
        $this->_uid = $this->getRedis()->get($key);
        return $this->_uid;
    }

    /**
     * 通过nick 找uid
     * @return false |uid
     */
    public function getUidByNick($nick)
    {
        $key = $this->getNickToUidKey($nick);
        $this->_uid = $this->getRedis()->get($key);
        return $this->_uid;
    }

    public function getPassword()
    {
        $colunm[]   = self::USER_PASS_NAME;
        return $this->_getUserColunm($this->_uid,$colunm);
    }

    public function getEncpass()
    {
        $colunm[]   = self::USER_ENC_NAME;
        return $this->_getUserColunm($this->_uid,$colunm);
    }

    public function getUserData()
    {

        $colunm    = [];
        $userData  = [];
        $colunm    = array_merge($colunm, $this->_colunm);
        $userData['uid']  = $this->_uid;
        $result    = $this->_getUserColunm($this->_uid,$colunm);
        foreach ($result as $k => $v)
        {
            $userData[$k] = (in_array($k, $this->_colunm) && $v) ? json_decode($v, true) : $v;
        }
        $result = null;
        return $userData;

    }

    /**
     *  手机号与uid绑定 (修改)
     * @param  string $phone [description]
     * @param  int $uid   [description]
     * @return bool
     */
    public function bindPhoneToUid()
    {
        $key = $this->getPhoneToUidKey($this->_phone);
        $try = 2;

        do {

            $status = $this->getRedis()->set($key,$this->_uid);

            if($status)
            {
                return true;
            }
            usleep(5);
        } while ($try-- > 0);

        return false;
    }

    /**
     *  解除 手机号与uid 绑定关系
     * @return
     */
    public function unbindPhoneToUid()
    {
        $key = $this->getPhoneToUidKey($this->_phone);
        $try = 2;

        do {

            if(!$this->getRedis()->exists($key))
            {
                return true;
            }

            $status = $this->getRedis()->delete($key);

            if($status)
            {
                return true;
            }

            usleep(1);

        } while ($try-- > 0);

        return false;
    }

    /**
     * nick与uid绑定(修改)
     * @return boolean
     */
    public function bindNickToUid($nick)
    {
        $key = $this->getNickToUidKey($nick);
        $try = 2;

        do {

            $status = $this->getRedis()->set($key,$this->_uid);

            if($status)
            {
                return true;
            }
            usleep(5);
        } while ($try-- > 0);

        return false;
    }

    public function updatePassword($password)
    {
        $colunm[self::USER_PASS_NAME] = $password;
        return $this->_setUserColunm($this->_uid,$colunm);
    }

    public function updateEncpass($encpass)
    {
        $colunm[self::USER_ENC_NAME] = $encpass;
        return $this->_setUserColunm($this->_uid,$colunm);
    }

    /**
     * 解除 nick与uid绑定
     * @return boolean
     */
    public function unbindNickToUid()
    {
        $key = $this->getNickToUidKey($this->_nick);
        $try = 2;

        do {

            if(!$this->getRedis()->exists($key))
            {
                return true;
            }

            $status = $this->getRedis()->delete($key,$this->_uid);

            if($status)
            {
                return true;
            }
            usleep(500);
        } while ($try-- > 0);

        return false;
    }

    public function updateUserExist($status = 1)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $colunm[self::USER_IS_EXSITS] = (int) $status;

        return $this->_setUserColunm($this->_uid,$colunm);
    }

    public function setUserStaticData(array $data)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $colunm[self::USER_STATIC_NAME] = hp_json_encode(array_values_to_string($data));

        return $this->_setUserColunm($this->_uid,$colunm);
    }

    public function setUserActiveData(array $data)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $colunm[self::USER_ACTIVE_NAME] = hp_json_encode(array_values_to_string($data));

        return $this->_setUserColunm($this->_uid,$colunm);
    }

    public function addUserDisableLogin(array $data)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key  = $this->getUserDisbleLoginKey($this->_uid);
        $try  = 2;
        $data = hp_json_encode(array_values_to_string($data));

        do {

            $result = $this->getRedis()->set($key,$data);

            if($result)
            {
                return true;
            }
            usleep(500);
        } while ($try-- > 0);

        return false;
    }

    public function deleteUserDisableLogin()
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key  = $this->getUserDisbleLoginKey($this->_uid);
        $try  = 2;

        do {

            if(!$this->getRedis()->exists($key))
            {
                return true;
            }

            $result = $this->getRedis()->delete($key);

            if($result)
            {
                return true;
            }

            usleep(5);
        } while ($try-- > 0);

        return false;
    }

    public function getUserDisableLoginStatusData()
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key  = $this->getUserDisbleLoginKey($this->_uid);
        if(!$this->getRedis()->exists($key))
        {
            return true;
        }

        $result = $this->getRedis()->get($key);
        if($result)
        {
            return json_decode($result, true);
        }

        return true;
    }

    public function getSilencedByAnchorUid($anchorUid = 1)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key = $this->getSilencedKey($this->_uid);
        if(!$this->getRedis()->exists($key))
        {
            return true;
        }

        $result = $anchorUid !== null ? $this->getRedis()->hget($key,$anchorUid) : $this->getRedis()->hGetAll($key);
        if(is_array($result))
        {
            return array_map(function($v){ return json_decode($v, true); }, $result);
        }

        if($result)
        {
            return json_decode($result, true);
        }

        return true;
    }

    public function addSilenced(array $data)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key   = $this->getSilencedKey($this->_uid);
        $scope = isset($data['scope']) ? (int) $data['scope'] : 0 ;
        $data  = hp_json_encode(array_values_to_string($data));
        $try   = 2;

        do {

            $result = $this->getRedis()->hset($key, $scope, $data);
            if($result !== false)
            {
                return true;
            }

            usleep(5);
        } while ($try-- > 0);

        return false;

    }

    public function deleteSilencedByAnchorUid($anchorUid = null)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key = $this->getSilencedKey($this->_uid);
        if(!$this->getRedis()->exists($key))
        {
            return true;
        }

        $try  = 2;

        do {

            if($anchorUid === null)
            {
                $result = $this->getRedis()->delete($key);

            } else
            {
                $result = $this->getRedis()->hExists($key, $anchorUid) ? $this->getRedis()->hdel($key,$anchorUid) : true;
            }

            if($result)
            {
                return true;
            }

            usleep(5);
        } while ($try-- > 0);

        return false;
    }

    /**
     * 添加主播禁播状态
     * @param array $data [description]
     */
    public function addDisableLive(array $data)
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key   = $this->getDisableLiveKey($this->_uid);
        $data  = hp_json_encode(array_values_to_string($data));
        $try   = 2;

        do {

            $result = $this->getRedis()->set($key,$data);
            if($result !== false)
            {
                return true;
            }

            usleep(5);
        } while ($try-- > 0);

        return false;

    }

    /**
     * 删除主播禁播放状态
     * @return boolean
     */
    public function deleteDisableLive()
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key   = $this->getDisableLiveKey($this->_uid);
        if(!$this->getRedis()->exists($key))
        {
            return true;
        }

        do {

            $result = $this->getRedis()->delete($key);
            if($result !== false)
            {
                return true;
            }

            usleep(5);
        } while ($try-- > 0);

        return false;
    }

    /**
     * 获取主播禁播放状态
     * @return array | boolean
     */
    public function getDisableLiveStatus()
    {
        if(!$this->_uid)
        {
            return false;
        }

        $key   = $this->getDisableLiveKey($this->_uid);
        if(!$this->getRedis()->exists($key))
        {
            return true;
        }

        $result = $this->getRedis()->get($key);
        if($result)
        {
            return json_decode($result, true);
        }

        return true;
    }

    /**
     * 向用户数据结构写入数据
     * @param int $uid 用户uid
     * @param array  $val
     */
    private function _setUserColunm($uid,array $val)
    {
        $key = $this->getUserDataKey($uid);
        $try = 2;

        do {

            $status = $this->getRedis()->hMSet($key,$val);
            if($status)
            {
                return true;
            }

            usleep(5);

        } while ($try-- > 0);

        return false;
    }

    /**
     * 通过colunm 获取用户数据结构
     * @param  int $uid    [description]
     * @param  array  $colunm [description]
     * @return array
     */
    private function _getUserColunm($uid,array $colunm)
    {
        $key = $this->getUserDataKey($uid);
        return $this->getRedis()->hMGet($key,$colunm);
    }

    public function getPhoneToUidKey($phone)
    {
         return sprintf(self::UID_PHONE_KEY, $phone);
    }

    public function getUserDataKey($uid)
    {
        return sprintf(self::USER_DATA_KEY, $uid);
    }

    public function getNickToUidKey($nick)
    {
        return sprintf(self::USER_NICK_KEY,Crc::x_crc32($nick));
    }

    public function getUserDisbleLoginKey($uid)
    {
        return sprintf(self::USER_DISABLE_LOGIN, $uid);
    }

    public function getSilencedKey($uid)
    {
        return sprintf(self::USER_ROOM_SILENCED, $uid);
    }

    public function getDisableLiveKey($anchorUid)
    {
        return sprintf(self::USER_DISABLE_LIVE, $anchorUid);
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::USER_REDIS_CONF);
    }

}