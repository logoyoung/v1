<?php
namespace service\user;
use Exception;
use service\user\helper\UserEventParam;
use lib\user\UserStatic;
use lib\user\UserActive;
use service\user\helper\UserRedis;
use service\event\EventAbstract;
use lib\user\UserDisableStatus;
use service\user\UserDisableStatusService;
use lib\user\UserRealName;
use lib\user\ZhimaCert;

class UserEvent extends EventAbstract
{

    private $_param;
    private $_action;
    private $_status;
    private $_userEventParam;
    private $_userRedis;
    private $_infoLog  = 'user_event_access';

    /**
     *
     * @param  [type] $action [description]
     * @param  array $param   uid 与phone 随意传一个就行，建义传uid, [ 'uid' => 69355, 'phone' => 13800138000]
     * @return [type]         [description]
     */
    public function trigger($action,$param)
    {
        $this->_status     = false;
        $this->_action     = (int) $action;
        $this->log("notice|收到事件请求 param:".hp_json_encode($param));
        $this->_initParam($param);
        $this->_userRedis  = new UserRedis();
        if(!$this->_userRedis->getRedis()->ping())
        {
            $this->log("error|redis服务异常，不作事件处理; param:".hp_json_encode($param));
            return false;
        }

        switch ($this->_action)
        {

            //用户注册
            case self::ACTION_USER_REGISTER:
            //登陆成功
            case self::ACTION_USER_LOGIN:
                $this->_checkUid();
                $this->_initUserStaticParam();
                $this->_initUserActiveParam();

                //更新用户存在状态
                $this->_updateUserExistStatusRedis();

                //更新encpass redis 缓存
                $this->_updateEncpassRedis();

                //更新密码redis缓存
                $this->_updatePasswordRedis();

                //更新 用户 封禁状态redis
                $this->_updateUserDisableLoginStatus();

                //更新手机号与uidredis缓存
                $this->_updateBindPhoneToUidRedis();

                //更新nick 与uid关系 redis 缓存
                $this->_updateBindNickToUid();

                //更新userstatic redis 缓存
                $this->_updateUserStaticRedis();

                //更新useractive redis 缓存
                $this->_updateUserActiveRedis();

                $this->_status     = true;
                break;

            //用户资料修改
            case self::ACTION_USER_INFO_UPDATE:

                $this->_checkUid();
                $this->_initUserStaticParam();
                $this->_initUserActiveParam();

                //更新用户存在状态
                $this->_updateUserExistStatusRedis();

                //更新encpass redis 缓存
                $this->_updateEncpassRedis();

                //更新密码redis缓存
                $this->_updatePasswordRedis();

                //更新 用户 封禁状态redis
                $this->_updateUserDisableLoginStatus();

                //更新手机号与uidredis缓存
                $this->_updateBindPhoneToUidRedis();

                //更新nick 与uid关系 redis 缓存
                $this->_updateBindNickToUid();

                //更新userstatic redis 缓存
                $this->_updateUserStaticRedis();

                //更新useractive redis 缓存
                $this->_updateUserActiveRedis();

                $this->_status     = true;
                break;

            //用户头相变更
            case self::ACTION_USER_UPDATE_HEAD:
                $this->_checkUid();
                $this->_initUserStaticParam();
                //更新userstatic redis 缓存
                $this->_updateUserStaticRedis();
                $this->_status     = true;
                break;

            //资金变动
            case self::ACTION_USER_MONEY_UPDATE:
                $this->_checkUid();
                $this->_initUserActiveParam();
                //更新useractive redis 缓存
                $this->_updateUserActiveRedis();
                $this->_status     = true;
                break;

            //用户消息更新
            case self::ACTION_USER_MSG_UPDATE:
                $this->_checkUid();
                $this->_initUserActiveParam();
                //更新useractive redis 缓存
                $this->_updateUserActiveRedis();
                $this->_status     = true;
                break;

            //认证信息变更
            case self::ACTION_USER_CERT_UPDATE:
            //芝麻认证通过
            case self::ACTION_ZHIMA_CERT_SUCC:
                $this->_checkUid();
                //更新实名认证信息
                $this->_updateUserRealNameRedis();
                 //芝麻认证通过
                $this->_updateUserZhimaStatusRedis();
                $this->_status     = true;
                break;

            //调整封禁用户
            case self::ACTION_USER_UPATE_DISABLE_LOGIN:
                $this->_checkUid();
                //更新 用户 封禁状态redis
                $this->_status = $this->_updateUserDisableLoginStatus();

                break;

            //添加禁言
            case self::ACTION_USER_ADD_SILENCE:
                $this->_checkUid();
                $this->_status = $this->_addUserSilencedStatus();
                break;

            //用户去除禁言
            case self::ACTION_USER_REMOVE_SILENCE:
                $this->_checkUid();
                $this->_status = $this->_deleteUserSilencedStatus();
                break;

            //封禁主播 直播
            case self::ACTION_ADD_DISABLE_LIVE:
                $this->_checkUid();
                $this->_status = $this->_addAnchorDisableLive();
                break;

            //解禁主播 直播
            case self::ACTION_DEL_DISABLE_LIVE:
                $this->_checkUid();
                $this->_status = $this->_deleteAnchorDisableLive();
                break;

            //重新构造缓存
            case self::ACTION_USER_RESET_CACHE:

                $this->_checkUid();
                $this->_initUserStaticParam();
                $this->_initUserActiveParam();

                //更新用户存在状态
                $this->_updateUserExistStatusRedis();

                //如果用户在数据库里不存在，不进行别的更新操作了
                if(!$this->_userEventParam->getExsit())
                {
                    $this->_status     = true;
                    break;
                }

                //更新encpass redis 缓存
                $this->_updateEncpassRedis();

                //更新密码redis缓存
                $this->_updatePasswordRedis();

                //更新手机号与uidredis缓存
                $this->_updateBindPhoneToUidRedis();

                //更新 用户 封禁状态redis
                $this->_updateUserDisableLoginStatus();

                //更新nick 与uid关系 redis 缓存
                $this->_updateBindNickToUid();

                //更新userstatic redis 缓存
                $this->_updateUserStaticRedis();

                //更新useractive redis 缓存
                $this->_updateUserActiveRedis();

                //重新构造禁言
                $this->_updateSilencedRedisData();

                //重新构造禁播数据
                $this->_updateAnchorDisableLiveRedis();

                //更新实名认证信息
                $this->_updateUserRealNameRedis();

                //芝麻认证通过
                $this->_updateUserZhimaStatusRedis();

                $this->_status     = true;
                break;

            default:
                return true;
        }

        return $this->_status;
    }

    private function _initParam($param)
    {
        $this->_userEventParam = new UserEventParam();

        if(isset($param['uid']))
        {
            $this->_userEventParam->setUid($param['uid']);
        }

        if(isset($param['phone']))
        {
            $this->_userEventParam->setPhone($param['phone']);
        }

        if(isset($param['disableType']))
        {
            $this->_userEventParam->setDisableType($param['disableType']);
        }

        if(isset($param['disableScope']))
        {
            $this->_userEventParam->setDisableScope($param['disableScope']);
        }

        if(isset($param['disableEtime']))
        {
            $this->_userEventParam->setDisableEtime($param['disableEtime']);
        }

    }

    private function _checkUid()
    {

        if($this->_userEventParam->getUid())
        {
            $this->_userRedis->setUid($this->_userEventParam->getUid());
            return true;
        }

        $phone = $this->_userEventParam->getPhone();
        if(!$phone)
        {
            $this->log("error|无效的手机号;phone:{$phone}");
            return false;
        }

        $uid = $this->_userRedis->setPhone($phone)->getUidByPhone();
        if($uid)
        {
            $this->_userEventParam->setUid($uid);
            return true;
        }
        //注意这里没有索引哦，需要加上才行
        $userData = $this->_getUserStaticDao()->getUidByPhone($phone);
        if(!isset($userData[$phone]) || !$userData[$phone])
        {
            $this->log("notice|手机号不存在;phone:{$phone}");
            return false;
        }

        $this->_userEventParam->setUid($userData[$phone]['uid']);
        $this->_userRedis->setUid($this->_userEventParam->getUid());
        return true;
    }

    private function _initUserStaticParam()
    {
        $userStaticDao  = $this->_getUserStaticDao();
        $uid            = $this->_userEventParam->getUid();
        $result         = $userStaticDao->getUserStaticData($uid);

        if($result === false)
        {
            return false;
        }

        if(!isset($result[$uid]))
        {
            $this->_userEventParam->setExist(0);
            return 0;
        }

        $userStaticData = $result[$uid];
        $this->_userEventParam->setExist(1);

        if(isset($userStaticData['phone']) && $userStaticData['phone'] )
        {
            $this->_userEventParam->setPhone($userStaticData['phone']);
        }

        if(isset($userStaticData['password']) && $userStaticData['password'])
        {
            $this->_userEventParam->setPassword($userStaticData['password']);
        }

        if(isset($userStaticData['encpass']) && $userStaticData['encpass'])
        {
            $this->_userEventParam->setEncpass($userStaticData['encpass']);
        }

        if(isset($userStaticData['nick']) && $userStaticData['nick'])
        {
            $this->_userEventParam->setNick($userStaticData['nick']);
        }

        $this->_userEventParam->setUserStaticData($userStaticData);

        return true;
    }

    private function _initUserActiveParam()
    {
        $userActiveDao  = $this->_getUserActiveDao();
        $uid   = $this->_userEventParam->getUid();
        $data  = $userActiveDao->getUserActiveData($uid);
        $data  = isset($data[$uid]) ? $data[$uid]: false;

        if($data === false)
        {
            $this->log("error|从数据库获取useractive 异常;uid:{$uid};");
            return false;
        }

        $this->_userEventParam->setUserActiveData($data);
    }

    private function _initUserDisableLoginParam()
    {
        $userDisableStatusDao = $this->_getUserDisbaleStatusDao();
        $uid    = $this->_userEventParam->getUid();
        $type   = UserDisableStatusService::USER_DISABLE_TYPE_LOGIN;
        $scope  = UserDisableStatusService::USER_DISABLE_SCOPE_ALL;
        $result = $userDisableStatusDao->getDisableStatusByUidTypeScope($uid,$type,$scope);
        if($result === false)
        {
            $this->log("error|从数据库获取user_disable_status 异常;uid:{$uid},type:{$type},scope:{$scope};");
            return false;
        }

        if($result)
        {
            $result = $result[0];
        }

        $this->_userEventParam->setUserDisableLoginStatusData((array) $result);

        return true;
    }

    //更新用户存在状态
    private function _updateUserExistStatusRedis()
    {
        if($this->_userEventParam->getExsit() === false)
        {
            return true;
        }

        $result = $this->_userRedis->updateUserExist($this->_userEventParam->getExsit());
        $log    = ($result === false) ? "error|更新用户存在redis状态异常" :"success|更新用户存在redis状态成功;";
        $log .= "|uid:{$this->_userEventParam->getUid()};exist:{$this->_userEventParam->getExsit()}";
        $this->log($log);
        return $result;
    }

    /**
     * 更新手机号与uid redis缓存
     * @return [type] [description]
     */
    private function _updateBindPhoneToUidRedis()
    {
        if($this->_userEventParam->getPhone() === false)
        {
            return true;
        }

        $this->_userRedis->setPhone($this->_userEventParam->getPhone());
        $result = $this->_userRedis->bindPhoneToUid();
        $log  = ($result === false) ? "error|更新手机号与uid redis关系失败" :"success|更新手机号与uid redis关系成功;";
        $log .= "|uid:{$this->_userEventParam->getUid()};phone:{$this->_userEventParam->getPhone()}";
        $this->log($log);

        return $result;
    }

    /**
     * 更新密码redis缓存
     * @return [type] [description]
     */
    public function _updatePasswordRedis()
    {
        if($this->_userEventParam->getPassword() === false)
        {
            return true;
        }

        $result = $this->_userRedis->updatePassword($this->_userEventParam->getPassword());
        $log  = ($result === false) ? "error|更新密码redis缓存失败" :"success|更新密码redis缓存成功;";
        $log .= "|uid:{$this->_userEventParam->getUid()}";
        $this->log($log);

        return $result;
    }

    /**
     * 更新encpass redis 缓存
     * @return [type] [description]
     */
    private function _updateEncpassRedis()
    {
        if($this->_userEventParam->getEncpass() === false)
        {
            return true;
        }

        $result = $this->_userRedis->updateEncpass($this->_userEventParam->getEncpass());
        $log  = ($result === false) ? "error|更新encpass redis缓存失败" :"success|更新encpass redis缓存成功;encpass:{$this->_userEventParam->getEncpass()}";
        $log .= "|uid:{$this->_userEventParam->getUid()}";
        $this->log($log);

        return $result;
    }

    //更新nick 与uid关系 redis 缓存
    public function _updateBindNickToUid()
    {
        if($this->_userEventParam->getNick() === false)
        {
            return true;
        }

        $result = $this->_userRedis->bindNickToUid($this->_userEventParam->getNick());
        $log  = ($result === false) ? "error|更新nick redis缓存失败" :"success|更新nick redis缓存成功;";
        $log .= "|uid:{$this->_userEventParam->getUid()};nick:{$this->_userEventParam->getNick()}";
        $this->log($log);

        return $result;
    }

    /**
     * 更新userstatic redis 缓存
     * @return boolean
     */
    private function _updateUserStaticRedis()
    {
        $staticData = $this->_userEventParam->getUserStaticData();

        if( $staticData === false)
        {
            return true;
        }

        unset($staticData['password'],$staticData['encpass']);
        $result = $this->_userRedis->setUserStaticData($staticData);
        $log    = ($result === false) ? "error|更新userstatic redis缓存失败" :"success|更新userstatic redis缓存成功;";
        $staticData = hp_json_encode($staticData);
        $log   .= "|uid:{$this->_userEventParam->getUid()}; staticData:{$staticData}";
        $this->log($log);

        return $result;
    }

    /**
     * 更新useractive redis 缓存
     * @return [type] [description]
     */
    private function _updateUserActiveRedis()
    {
        $activeData = $this->_userEventParam->getUserActiveData();
        if($activeData === false)
        {
            return true;
        }

        $result = $this->_userRedis->setUserActiveData($activeData);
        $log    = ($result === false) ? "error|更新useractive redis缓存失败" :"success|更新useractive redis缓存成功;";
        $activeData = hp_json_encode($activeData);
        $log   .= "|uid:{$this->_userEventParam->getUid()}; useractive:{$activeData}";
        $this->log($log);

        return $result;
    }

    /**
     *  更新 用户 封禁状态redis
     * @return boolean
     */
    private function _updateUserDisableLoginStatus()
    {
        if(!$this->_initUserDisableLoginParam())
        {
            return false;
        }

        $disableData = $this->_userEventParam->getUserDisableLoginStatusData();
        if($disableData === false)
        {
            return true;
        }

        if(!$disableData)
        {
            if($this->_userRedis->getUserDisableLoginStatusData() === true)
            {
                return true;
            }

            $result = $this->_userRedis->deleteUserDisableLogin();
            $log    = $result ? 'success|解禁用户,更新redis缓存成功' : 'error|解禁用户,更新redis缓存失败';
            $log   .= "|uid:{$this->_userEventParam->getUid()}";
            $this->log($log);

            return $result;
        }

        $result = $this->_userRedis->addUserDisableLogin($disableData);
        $log    = $result ? 'success|加禁用户,更新redis缓存成功' : 'error|加禁用户,更新redis缓存失败';
        $log   .= "|uid:{$this->_userEventParam->getUid()};param:".hp_json_encode($disableData);
        $this->log($log);

        return $result;
    }

    private function _addUserSilencedStatus()
    {
        $uid    = $this->_userEventParam->getUid();
        $type   = $this->_userEventParam->getDisableType();
        $scope  = $this->_userEventParam->getDisableScope();
        $etime  = $this->_userEventParam->getDisableEtime();
        if(!$uid || !$type || !$scope || !$etime)
        {
            return false;
        }

        $disableData = [
            'uid'    => $uid,
            'type'   => $type,
            'scope'  => $scope,
            'etime'  => $etime,
        ];

        $result = $this->_userRedis->addSilenced($disableData);
        $log    = $result ? 'success|加禁言用户,更新redis缓存成功' : 'error|加禁言用户,更新redis缓存失败';
        $log   .= "|uid:{$uid};param:".hp_json_encode($disableData);
        $this->log($log);

        return $result;
    }

    private function _deleteUserSilencedStatus()
    {
        $uid    = $this->_userEventParam->getUid();
        $type   = $this->_userEventParam->getDisableType();
        $status = UserDisableStatusService::USER_DISABLE_STATUS_OFF;
        $scope  = $this->_userEventParam->getDisableScope();

        if(!$uid || !$type || !$scope)
        {
            return false;
        }

        $result = $this->_userRedis->deleteSilencedByAnchorUid($scope);
        $log    = $result ? 'success|删除禁言用户,更新redis缓存成功' : 'error|删除禁言用户,更新redis缓存失败';
        $disableData = [
            'uid'    => $uid,
            'type'   => $type,
            'status' => $status,
            'scope'  => $scope,
        ];
        $log   .= "|uid:{$uid};param:".hp_json_encode($disableData);
        $this->log($log);

        return $result;
    }

    //重新构造禁言
    private function _updateSilencedRedisData()
    {
        $uid    = $this->_userEventParam->getUid();
        if(!$uid)
        {
            return false;
        }

        $disableDao  = $this->_getUserDisbaleStatusDao();
        $result      = $disableDao->getSilencedStatusByUidType($uid,UserDisableStatusService::USER_DISABLE_TYPE_SEND_MSG);
        if($result === false)
        {
            $this->log("error|uid:{$uid};重新构造用户直播间禁言,数据库异常");
            return false;
        }

        if(!$result)
        {
            //清除脏数据
            $r = $this->_userRedis->deleteSilencedByAnchorUid(null);
            $msg = $r ? "success|uid:{$uid}; 数据库里没有用户直播间禁言数据" : "error|uid:{$uid}; 数据库里没有用户直播间禁言数据,但是清除redis数据失败";
            $this->log($msg);
            return true;
        }

        $status = [];
        $this->_userRedis->deleteSilencedByAnchorUid(null);
        foreach ($result as $silencedData)
        {
            unset($silencedData['sid'],$silencedData['utime']);
            $status[] = $this->_userRedis->addSilenced($silencedData);
        }

        if(array_search(false, $status, true) === false)
        {
            $this->log("success|uid:{$uid};重新构造用户直播间禁言成功");
            return true;
        }

        $this->log("error|uid:{$uid};重新构造用户直播间禁言,redis异常");
        return false;
    }

    //添加禁播放
    private function _addAnchorDisableLive()
    {
        $uid    = $this->_userEventParam->getUid();
        $type   = $this->_userEventParam->getDisableType();
        $scope  = $this->_userEventParam->getDisableScope();
        $etime  = $this->_userEventParam->getDisableEtime();
        if(!$uid || !$type || !$scope || !$etime)
        {
            return false;
        }

        $liveDisableData = [ 'uid' => $uid,'type' => $type, 'scope' => $scope, 'etime' => $etime,];
        $result = $this->_userRedis->addDisableLive($liveDisableData);
        $log    = $result ? 'success|禁播,更新redis缓存成功' : 'error|禁播,更新redis缓存失败';
        $log   .= "|uid:{$uid};param:".hp_json_encode($liveDisableData);
        $this->log($log);
        return $result;
    }

    //删除禁播放
    private function _deleteAnchorDisableLive()
    {
        $uid    = $this->_userEventParam->getUid();
        $type   = $this->_userEventParam->getDisableType();
        if(!$uid || !$type )
        {
            return false;
        }

        $result = $this->_userRedis->deleteDisableLive();
        $log    = $result ? 'success|禁播,更新redis缓存成功' : 'error|禁播,更新redis缓存失败';
        $liveDisableData = [ 'uid' => $uid,'type' => $type,];
        $log   .= "|uid:{$uid};param:".hp_json_encode($liveDisableData);
        $this->log($log);
        return $result;
    }

    //重新构造禁播数据
    private function _updateAnchorDisableLiveRedis()
    {
        $uid = $this->_userEventParam->getUid();
        if(!$uid)
        {
            return false;
        }
        $disableDao = $this->_getUserDisbaleStatusDao();
        $disableLiveDbData = $disableDao->getDisableStatusByUidTypeScope($uid,UserDisableStatusService::USER_DISABLE_TYPE_LIVE,UserDisableStatusService::USER_DISABLE_SCOPE_ALL);
        if($disableLiveDbData === false)
        {
            $this->log("error|从数据库获取主播禁播数据异常");
            return false;
        }

        $disableLiveDbData = isset($disableLiveDbData[0]) ? $disableLiveDbData[0] : [];
        if(!$disableLiveDbData)
        {
            if($this->_userRedis->deleteDisableLive())
            {
                $this->log("success|构建禁播数据成功;uid:{$uid}");
                return true;
            }

            $this->log("error|构建禁播数据redis异常;uid:{$uid}");
            return false;
        }

        $liveDisableData = [
            'uid'   => $uid,
            'type'  => $disableLiveDbData['type'],
            'scope' => $disableLiveDbData['scope'],
            'etime' => $disableLiveDbData['etime'],
        ];

        $result = $this->_userRedis->addDisableLive($liveDisableData);
        if($result)
        {
            $this->log("success|构建禁播数据成功;uid:{$uid}");
            return true;
        }

        $this->log("error|构建禁播redis异常;uid:{$uid}");
        return false;
    }

    /**
     * 更新实名认证信息
     * @return [type] [description]
     */
    private function _updateUserRealNameRedis()
    {
        $uid   = $this->_userEventParam->getUid();
        $realNameDb = $this->_getUserRealNameDao();
        $data  = $realNameDb->getDataByUid($uid);
        if($data === false)
        {
            $this->log("error|从数据库获取userrealname 异常;uid:{$uid};line:".__LINE__);
            return false;
        }

        $data   = (isset($data[$uid]) && $data[$uid]) ? (array) $data[$uid] : [];
        $status = $data ? (int) $data['status'] : -1;
        //更认证状态 101
        if($this->_userRedis->updateCertStatus($status) === false)
        {
            $this->log("error|实名认证信;更新redis异常;userrealname;uid:{$uid};status:{$status};line:".__LINE__);
        }

        //更新认证数据
        if($this->_userRedis->setUserRealNameData($data) === false)
        {
            $this->log("error|实名认证信息;更新redis异常;userrealname;uid:{$uid};line:".__LINE__);
            return false;
        }

        $this->log("success|实名认证信息;更新redis成功;userrealname;uid:{$uid};status:{$status};param:".hp_json_encode($data)."line:".__LINE__);
        return true;
    }

    //更新用户芝麻认证信息redis
    private function _updateUserZhimaStatusRedis()
    {
        $uid   = $this->_userEventParam->getUid();
        $zmDb  = $this->_getZhiCertDao();
        $data  = $zmDb->getZhimaCertByUidStatus($uid,\service\zhima\CertService::STATUS_SUCC);
        if($data === false)
        {
            $this->log("error|更新芝认证信成功;zhima_cert数据库异常;uid:{$uid};line:".__LINE__);
            return false;
        }

        $status = $data ? 1 : 0;
        //更认证状态 101
        if($this->_userRedis->updateCertZhimaStatus($status) === false)
        {
            $this->log("error|更新redis芝认证信成功; redis异常;userrealname;uid:{$uid};status:{$status};line:".__LINE__);
            return false;
        }

        $this->log("success|更新redis 芝认证信成功;uid:{$uid};status:{$status};line:".__LINE__);
        return true;
    }

    private function _getUserStaticDao()
    {
        $userStaticDao  = new UserStatic();
        $userStaticDao->setMaster(true);
        return $userStaticDao;
    }

    private function _getUserActiveDao()
    {
        $userActiveDao = new UserActive();
        $userActiveDao->setMaster(true);
        return $userActiveDao;
    }

    private function _getUserDisbaleStatusDao()
    {
        $userDisableStatusDao = new UserDisableStatus();
        $userDisableStatusDao->setMaster(true);
        return $userDisableStatusDao;
    }

    private function _getUserRealNameDao()
    {
        $realNameDb = new UserRealName;
        $realNameDb->setMaster(true);
        return $realNameDb;
    }

    private function _getZhiCertDao()
    {
        $zmDb = new ZhimaCert;
        $zmDb->setMaster(true);
        return $zmDb;
    }

    public  function log($msg)
    {
        write_log($msg.'action'.$this->_action.';class:'.__CLASS__, $this->_infoLog);
    }
}