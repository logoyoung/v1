<?php
namespace service\task;
use service\common\AbstractService;
use lib\Task;
use service\event\EventManager;

/**
 * 任务服务
 */

class TaskService extends AbstractService
{

    //任务uid不能为空
    const ERROR_TASK_UID  = -11001;
    //新手任务领取欢朋豆调用底层数据异常
    const ERROR_TASK_JOB  = -11002;
    //我的任务列表从底层数据没有获取到数据
    const ERROR_TASK_LIST = -11003;
    //用户uid
    private $_uid;
    //任务id
    private $_taskId;
    //设置任务类型
    private $_type;
    private $_taskDao;

    public static $errorMsg = [
        self::ERROR_TASK_UID  => '任务uid不能为空',
        self::ERROR_TASK_JOB  => '新手任务领取欢朋豆调用底层数据异常',
        self::ERROR_TASK_LIST => '从底层数据没有获取到我的任务列表数据',
    ];

    //设置用户uid
    public function setUid($uid)
    {
        $this->_uid     = $uid;
        $this->_taskId  = null;
        $this->_type    = null;
        $this->_taskDao = null;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    //设置任务id
    public function setTaskId($taskId)
    {
        $this->_taskId = $taskId;
        return $this;
    }

    public function getTaskId()
    {
        return $this->_taskId;
    }

    //设置任务类型
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    /**
     * 完成任务领取欢豆
     *
     * @param int $taskid 任务id
     *
     * @return bool  领取成功true  领取失败false
     */
    public function getBeanByTask()
    {
        if(!$this->getUid())
        {
            $code = self::ERROR_TASK_UID;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return false;
        }
        $count = $this->getTaskDao()->getBeanByTask($this->getTaskId());
        if($count === false)
        {
            $code = self::ERROR_TASK_JOB;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()};taskId{$this->getTaskId()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return false;
        }

        if($count === true)
        {
            $log  = "notice |该用户没有需要领取的任务;uid:{$this->getUid()};taskId{$this->getTaskId()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return true;
        }

        write_log("success |完成任务领取欢豆成功,uid:{$this->getUid()};taskId{$this->getTaskId()};|class:".__CLASS__.';func'.__FUNCTION__.';line:'.__LINE__.$this->getCaller());

        $event = new EventManager();
        $event->trigger(EventManager::ACTION_USER_MONEY_UPDATE,['uid' => $this->getUid()]);
        $event = null;

        return (int) $count;

    }

    /**
     * 我的任务列表
     *
     * @return array
     */
    public function getUserTaskList()
    {
        $list = $this->getTaskDao()->getUserTaskList();
        if(!$list)
        {
            $code = self::ERROR_TASK_LIST;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return false;
        }

        $list = array_map(function ($v){
            $tmp  = [];
            $bean = $v['bean'];
            unset($v['bean']);
            $tmp  = $v;
            $tmp['hpbean'] = $bean;
            return $tmp;
        }, $list);

        return $list;
    }

    public function getTaskDao()
    {
        if(!$this->_taskDao)
        {
            $this->_taskDao = new Task($this->getUid());
        }

        return $this->_taskDao;
    }

}