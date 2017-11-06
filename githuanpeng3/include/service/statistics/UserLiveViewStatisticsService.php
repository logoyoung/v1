<?php
namespace service\statistics;

use service\common\AbstractService;
use service\statistics\helper\UserViewLengthRedis;
use lib\statistics\ViewLength;

/**
 * 用户观看直播时长统计服务
 * @author longgang <longgang@6.cn>
 * @date 2017-09-18 10:11:23
 * @version 1.0.0
 */
class UserLiveViewStatisticsService extends AbstractService
{

    //更新观看时长奖励状态异常
    const ERROR_UPDATE_VIEW_LENGTH_STATUS         = -20001;
    //uid不能为空
    const ERROR_UID = -20002;
    //传入观看时长异常
    const ERROR_VIEW_LENGTH = -20003;
    //添加用户uid到当天观看时长redis异常
    const ERROR_VIEW_LENGTH_UIDS_REDIS = -20004;
    //更新用户观看时长信息redis异常
    const ERROR_VIEW_LENGTH_DATA_REDIS = -20005;
    
    public static $errorMsg = [
        self::ERROR_UPDATE_VIEW_LENGTH_STATUS         => '更新观看时长奖励状态异常',
        self::ERROR_UID => 'uid不能为空',
        self::ERROR_VIEW_LENGTH => '传入观看时长异常',
        self::ERROR_VIEW_LENGTH_UIDS_REDIS => '添加用户uid到当天观看时长redis异常',
        self::ERROR_VIEW_LENGTH_DATA_REDIS => '更新用户观看时长信息redis异常',
    ];

    private $_params;
    private $_logName    = 'user_view_length_statistics';

    public function setParams($params)
    {
        $this->_params      = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function setUserLiveViewData()
    {
        if(!$this->_params['uid'])
        {
            $code = self::ERROR_UID;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->_params['uid']}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';caller:' . $this->getCaller();
            write_log($log);
            return false;
        }
        
        $data = hp_json_encode(array_values_to_string($this->getParams()));
        $this->myLog('received data:' . $data);
        
        if($this->_params['uid'] >= LIVEROOM_ANONYMOUS)
        {
            return true;
        }
        
        $date = date('Ymd');
        $userViewLengthRedis = new UserViewLengthRedis();
        $res = $userViewLengthRedis->setUserLiveViewLengthUids($date,$this->_params['uid']);
        
        if($res === false)
        {
            $code = self::ERROR_VIEW_LENGTH_UIDS_REDIS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->_params['uid']}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';caller:' . $this->getCaller();
            write_log($log);
            return false;
        }
        
        $params = $this->_operatingViewTime($date,$this->getParams());
        
        if(!$params)
        {
            $code = self::ERROR_VIEW_LENGTH;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->_params['uid']}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';caller:' . $this->getCaller();
            write_log($log);
            return false;
        }
        
        $res = $userViewLengthRedis->setUserLiveViewDataRedis($date,$this->_params['uid'],$params);
        if($res === false)
        {
            $code = self::ERROR_VIEW_LENGTH_DATA_REDIS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->_params['uid']}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';caller:' . $this->getCaller();
            write_log($log);
            return false;
        }
        return true;
    }
    
    private function _operatingViewTime($date,$data)
    {
        $param = [];
        $userViewLengthRedis = new UserViewLengthRedis();
                
        $param['utime'] = time();
        $param['viewTime'] = $data['realtime'];
                
        $viewLengthData = $userViewLengthRedis->getUserLiveViewData($date,$data['uid']);

        if($viewLengthData)
        {
            $viewLengthData = json_decode($viewLengthData,true);
            
            $nstime = $param['utime'] - $data['realtime'];
            $pstime = $viewLengthData['utime'] - $viewLengthData['viewTime'];
            
            if($viewLengthData['utime'] == $param['utime'])
            {
                return false;
            } elseif ($viewLengthData['utime'] >= $nstime && $viewLengthData['utime'] < $param['utime'])
            {
                $param['viewTime'] = $param['utime'] - $pstime; 
            } elseif ($nstime > $viewLengthData['utime'])
            {
                $param['viewTime'] = $viewLengthData['viewTime'] + $data['realtime']; 
            } else
            {
                return false;
            }
        } else
        {
            $viewLengthData = $this->getViewLengthDao()->getUserLiveViewLengthByUidDate($data['uid'], $date);
            if($viewLengthData)
            {
                $nstime = $param['utime'] - $data['realtime'];
                $pstime = $viewLengthData['utime'] - $viewLengthData['view_length'];

                if($viewLengthData['utime'] == $param['utime'])
                {
                    return false;
                } elseif ($viewLengthData['utime'] >= $nstime && $viewLengthData['utime'] < $param['utime'])
                {
                    $param['viewTime'] = $param['utime'] - $pstime; 
                } elseif ($nstime > $viewLengthData['utime'])
                {
                    $param['viewTime'] = $viewLengthData['view_length'] + $data['realtime']; 
                } else
                {
                    return false;
                }
            }
        }
        
        return $param;
    }

    public function updateUserLiveViewStatus(int $uid,int $reward_status,$record_date)
    {
        $viewLengthDao = $this->getViewLengthDao();
        $res = $viewLengthDao->updateRewardStatus($uid, $reward_status, $record_date);
        if(!$res)
        {
            $code = self::ERROR_UPDATE_VIEW_LENGTH_STATUS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';caller:' . $this->getCaller();
            write_log($log);
            return false;
        }
        return true;
    }
    
    public function getViewLengthDao()
    {
        return new ViewLength();
    }
    
    public function myLog($msg)
    {
        write_log($msg,$this->_logName);
    }
}