<?php
namespace dota\app\http;
use service\event\EventManager;

/**
 *  内部 事件消息推荐接口
 *  @author xuyong
 */
class Event
{

    const ERROR_AC_EMPTY           = 701;
    const ERROR_AC_PARAM_INVALID   = 702;
    const ERROR_AC_SYSTEM          = 706;
    public static $errorMsg = [
        self::ERROR_AC_EMPTY         => 'ac参数不能为空',
        self::ERROR_AC_PARAM_INVALID => '缺少必要参数，uid与phone 二选一',
    ];

    private $_ac;
    private $_param;

    public function push()
    {

        write_log('notice|收到请求'.json_encode($_POST),'dota_event');

        $this->_ac             = isset($_POST['ac'])    ? (int) $_POST['ac']    : false;
        if(isset($_POST['uid']))
        {
            $this->_param['uid'] = is_array($_POST['uid']) ? $_POST['uid'] : explode(',',$_POST['uid']);
        } else
        {
            $this->_param['uid'] = false;
        }

        if(!$this->_ac)
        {
            $code = self::ERROR_AC_EMPTY;
            $msg  = self::$errorMsg[$code];
            write_log("error|error_code:{$code};error_msg:{$msg};param:".json_encode($_POST),'dota_event');
            render_error_json($msg,$code,2);
        }

        if(!$this->_param['uid'])
        {
            $code = self::ERROR_AC_PARAM_INVALID;
            $msg  = self::$errorMsg[$code];
            write_log("error|error_code:{$code};error_msg:{$msg};param:".json_encode($_POST),'dota_event');
            render_error_json($msg,$code,2);
        }

        $event  = new EventManager();

        foreach ($this->_param['uid'] as $uid)
        {
            $result[] = $event->trigger($this->_ac,['uid' => $uid]);
        }

        if(array_search(false, $result, true) !== false)
        {
            $code = self::ERROR_AC_SYSTEM;
            $msg  = self::$errorMsg[$code];
            write_log("error|error_code:{$code};error_msg:{$msg};param:".json_encode($_POST),'dota_event');
            render_error_json($msg,$code,2);
        }

        write_log('success|处理成功;param:'.json_encode($_POST),'dota_event');
        render_json('success');
    }

}