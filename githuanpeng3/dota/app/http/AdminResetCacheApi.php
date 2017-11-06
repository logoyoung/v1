<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/9/15
 * Time: 11:05
 */

namespace dota\app\http;

use system\RedisHelper;
use service\due\DueCertService;
class AdminResetCacheApi
{
    private $_redis;
    private $_uid;
    const ERROR_GET_DATA = -995;
    public static $errorMsg =[
        self::ERROR_GET_DATA =>'操作数据发生意外',
    ];
    //参数初始化 status 状态 1 默认为开  -1 为关闭
    private function _init()
    {
        write_log('notice|param:'.hp_json_encode($_POST),'dota_roomRobotApi');
        $this->_uid   = isset($_POST['uid']) ? trim($_POST['uid']) : '';
    }
    public function resetGameListForLaunch()
    {
        write_log('notice|收到请求:resetGameListForLaunch','dota_AdminResetCacheApi');
        $gameListKey = $GLOBALS['env'].'_appRecommendGameList';
        $res = $this->getRedis()->del($gameListKey);
        if($res === false)
        {
            $code = self::ERROR_GET_DATA;
            $msg =  self::$errorMsg[$code];
            $log    = "error:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__;
            write_log($log);
            render_error_json($msg, $code);
        }
        render_json('success');

    }
    public function resetDueSkill()
    {
        write_log('notice|收到请求:resetDueSkill','dota_AdminResetCacheApi');
        $this->_init();
        $DueCertService =  new DueCertService();
        $DueCertService->setUid($this->_uid);
        $res = $DueCertService->resetDueSkillCache();
        if($res === false)
        {
            $code = self::ERROR_GET_DATA;
            $msg =  self::$errorMsg[$code];
            $log    = "error:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__;
            write_log($log);
            render_error_json($msg, $code);
        }
        render_json('success');
    }
    public function getRedis()
    {
        if (is_null($this->_redis))
        {
            $this->_redis = RedisHelper::getInstance("huanpeng");
        }
        return $this->_redis;
    }

}