<?php

namespace Think\Session\Driver;

class Redis {
    protected $lifeTime = 0;
    protected $sessExpireKey = 'HPsidexpire_';
    protected $sessionName = 'HPsid_';

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
    public function open($savePath, $sessName) {
        return true;
    }

    /**
     * 关闭Session 
     * @access public 
     */
    public function close() {
        return true;
    }

    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
    public function read($sessID) {
        return S($this->sessionName . $sessID);
    }

    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param String $sessData  
     */
    public function write($sessID, $sessData) {
        if(!$sessData) return true;
        //判断是否手动设置过过期时间
        if($this->lifeTime<1){
            $this->lifeTime = C('SESSION_EXPIRE',null,3600);
            if($expireSet = S($this->sessExpireKey . $sessID) and $expireSet>0){
                $this->lifeTime = $expireSet;
            }
        }
        return S($this->sessionName.$sessID,$sessData,$this->lifeTime);
    }

    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     */
    public function destroy($sessID) {
        return S($this->sessionName . $sessID, null);
    }

    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime 
     */
    public function gc($sessMaxLifeTime) {
        return true;
    }

}
