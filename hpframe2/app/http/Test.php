<?php
namespace app\http;
//test db
use app\service\user\helper\UserstaticDbHelper;
//test redis
use app\service\user\helper\UserRedisHelper;
// test view
use system\View;

use system\InputHelper;

class Test {

    public function i()
    {
        render_json(['msg' => 'hi hpFrame','url' => InputHelper::getHostInfo()]);
    }

    public function db()
    {
        $uid  = InputHelper::get('uid') ? InputHelper::get('uid') : 1870;
        $userstaticDbHelper = new UserstaticDbHelper;
        $data = $userstaticDbHelper->getUserStaticData($uid);
        render_json($data);
    }

    public function rds()
    {
        $uid  = InputHelper::get('uid') ? InputHelper::get('uid') : 1870;
        $redisHelper = new UserRedisHelper;
        $redisHelper->setUid($uid);
        $redisHelper->setGetUserStatic(true);
        $data = $redisHelper->getUserData();
        render_json($data);
    }

    /**
     *  简单的 模板 布局、
     *
     * @return
     */
    public function layout()
    {
        $uid  = InputHelper::get('uid') ? InputHelper::get('uid') : 1870;
        $userstaticDbHelper = new UserstaticDbHelper;
        $data = $userstaticDbHelper->getUserStaticData($uid);

        //分配变量 （顺序随意）
        View::assign('head','欢朋直播');
        View::assign('user_data',$data[$uid]);
        view::layout('index/head','index/user','index/footer');
        View::display();
        //View::display('index/user', ['user_data',$data]);
    }
}