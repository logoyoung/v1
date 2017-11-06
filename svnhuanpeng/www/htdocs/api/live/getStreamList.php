<?php

include '../../../include/init.php';

use service\live\LiveService;

class StreamList {

    const ERROR_CODE_LUID = -4013;
    public $luid;
    public static $errorMsg = [
        self::ERROR_CODE_LUID => '无效的luid',
    ];

    private function _init()
    {
        $this->luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;

        if(!$this->luid)
        {
            $code = self::ERROR_CODE_LUID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        return true;
    }

    public function getStreamList()
    {
        $service = new LiveService();
        $service->setCaller('file:'.__FILE__);
        $service->setLuid($this->luid);

        return $service->getStreamList();
    }

    public function display()
    {
        $this->_init();
        $list = $this->getStreamList();
        $list['stream']     = $list['stream']     ?  $list['stream']            : '' ;
        $list['streamList'] = $list['streamList'] ? (array) $list['streamList'] : [] ;
        render_json($list);
    }

}

$obj = new StreamList();
$obj->display();