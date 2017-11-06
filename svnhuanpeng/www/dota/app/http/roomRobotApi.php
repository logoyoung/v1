<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/30
 * Time: 15:04
 */

namespace dota\app\http;

use service\robot\RoomRobotService;
use service\common\ApiCommon;

class roomRobotApi
{
    //观众数设置状态打开
    const VIEWER_STATUS_ON  = 1;
    //观众数设置状态关闭
    const VIEWER_STATUS_OFF = -1;
    const VIEWER_STATUS_SELECT   = 11;
    //默认观众速率时间为0
    const VIEWER_SPEED_TIME = 0;
    const ERROR_GET_DATA = -995;
    public static $errorMsg =[
        self::ERROR_GET_DATA =>'操作数据发生意外',
        ];
     private $_id;
     private $_uid;
     private $_num;
     private $_time;
     private $_status;
     private $_page;
     private $_pageSize;

     //参数初始化 status 状态 1 默认为开  -1 为关闭
    private function _init()
    {
        write_log('notice|param:'.hp_json_encode($_POST),'dota_roomRobotApi');
        $this->_id   = isset($_POST['id']) ? trim($_POST['id']) : '';
        $this->_uid   = isset($_POST['uid']) ? trim($_POST['uid']) : '';
        $this->_num   = isset($_POST['num']) ? trim($_POST['num']) : '';
        $this->_time   = isset($_POST['time']) ? trim($_POST['time']) : self::VIEWER_SPEED_TIME;
        $this->_status   = isset($_POST['status']) ? trim($_POST['status']) :'';
        $this->_page   = isset($_POST['page']) ? trim($_POST['page']) : '';
        $this->_pageSize   = isset($_POST['pageSize']) ? trim($_POST['pageSize']) :'';
    }
    #查
    public function select()
    {
        $this->_init();
        $checkData['page'] = $this->_page;
        $checkData['pageSize'] = $this->_pageSize;
        //不能小于等于0
        $checkRule =['page'=>'2','pageSize'=>'2'] ;
        ApiCommon::checkParams($checkData,$checkRule);
        $roomRobotService = new RoomRobotService();
        $list = $roomRobotService->selectRoomRobotViewer( $checkData);
        if($list=== false)
        {
            $code = self::ERROR_GET_DATA;
            $msg = self::$errorMsg[$code];
            render_error_json($msg, $code);

        }
        render_json($list);
    }
    #搜索
    public function search()
    {
        $this->_init();
        $checkData['uid'] = $this->_uid;
        //uid不能为空
        $checkRule =['uid'=>'2'] ;
        ApiCommon::checkParams($checkData,$checkRule);
        $roomRobotService = new RoomRobotService();
        $list = $roomRobotService->searchRoomRobotViewer($checkData);
        if($list=== false)
        {
            $code = self::ERROR_GET_DATA;
            $msg = self::$errorMsg[$code];
            render_error_json($msg, $code);

        }
        render_json($list);
    }
    #增
    public function add()
    {
        write_log('notice|收到请求:add','dota_roomRobotApi');
        $this->_init();
        $checkData['uid'] = $this->_uid;
        $checkData['num'] = $this->_num;
        $checkData['time'] = $this->_time;
        $checkData['status'] = $this->_status;
        $checkRule =['uid'=>'2','num'=>'1','status'=>'1'] ;
        ApiCommon::checkParams($checkData,$checkRule);
        $roomRobotService = new RoomRobotService();
        $list = $roomRobotService->addRoomRobotViewer($checkData);
        if($list=== false)
        {
            $code = self::ERROR_GET_DATA;
            $msg = self::$errorMsg[$code];
            render_error_json($msg, $code);

        }
        render_json($list);
    }
    #改
    public function update()
    {
        write_log('notice|收到请求:update','dota_roomRobotApi');
        $this->_init();
        $checkData['id'] = $this->_id;
        $checkData['uid'] = $this->_uid;
        $checkData['num'] = $this->_num;
        $checkData['time'] = $this->_time;
        $checkData['status'] = $this->_status;
        $checkRule =['id'=>'2','uid'=>'2','num'=>'1','status'=>'1'] ;
        ApiCommon::checkParams($checkData,$checkRule);
        $roomRobotService = new RoomRobotService();
        $list = $roomRobotService->updateRoomRobotViewer($checkData);
        if($list=== false)
        {
            $code = self::ERROR_GET_DATA;
            $msg = self::$errorMsg[$code];
            render_error_json($msg, $code);

        }
        render_json($list);
    }
    public function batchUpdateStatus()
    {
        write_log('notice|收到请求:batchUpdateStatus','dota_roomRobotApi');
        $this->_init();
        $checkData['id'] =json_decode($this->_id,true);
        $checkData['status'] = $this->_status;
        $checkRule =['id'=>'1','status'=>'1'] ;
        ApiCommon::checkParams($checkData,$checkRule);
        $roomRobotService = new RoomRobotService();
        $list = $roomRobotService->updateRoomRobotStatusById($checkData);
        if($list=== false)
        {
            $code = self::ERROR_GET_DATA;
            $msg = self::$errorMsg[$code];
            render_error_json($msg, $code);

        }
        render_json($list);
    }
    #删
    public function batchdelete()
    {
        write_log('notice|收到请求:batchdelete','dota_roomRobotApi');
        $this->_init();
        $checkData['id'] = json_decode($this->_id,true);
        $checkRule =['id'=>'1'] ;
        ApiCommon::checkParams($checkData,$checkRule);
        $roomRobotService = new RoomRobotService();
        $list = $roomRobotService->deleteRoomRobotViewer($checkData);
        if($list=== false)
        {
            $code = self::ERROR_GET_DATA;
            $msg = self::$errorMsg[$code];
            render_error_json($msg, $code);

        }
        render_json($list);
    }
}