<?php
namespace service\common;

abstract class AbstractService
{
     protected  $_caller = '';

    /**
     * 设置服务调用方（方便记日志使用）
     * @param string |array
     */
    public function setCaller($caller)
    {
        $this->_caller = $caller;
        return $this;
    }

    /**
     * 获取服务使用方
     * @return string
     */
    public function getCaller()
    {
        $caller = is_string($this->_caller) ? $this->_caller : json_encode($this->_caller);
        return "| caller:{$caller}";
    }

    /**
     * 白名单判断
     * @param  int  $uid [description]
     * @return boolean      [description]
     */
    public function isWhiteUid($uid)
    {
        $uids = get_hp_config('system/whiteUid.'.get_hp_env());
        if(!is_array($uids))
        {
            return $uids;
        }

        return in_array($uid, $uids);
    }
}
