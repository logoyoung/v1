<?php
namespace app\http;
//test db
use app\service\user\helper\UserstaticDbHelper;
//test redis
use app\service\user\helper\UserRedisHelper;
// test view
use system\View;

class Test {

    public function i()
    {
        render_json(['hi hpFrame']);
    }

    public function db()
    {
        $uid  = isset($_GET['uid']) ? (int) $_GET['uid'] : 1870;
        $userstaticDbHelper = new UserstaticDbHelper;
        $data = $userstaticDbHelper->getUserStaticData($uid);
        render_json($data);
    }

    public function rds()
    {
        $uid  = isset($_GET['uid']) ? (int) $_GET['uid'] : 1870;
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
        $uid  = isset($_GET['uid']) ? (int) $_GET['uid'] : 1870;
        $userstaticDbHelper = new UserstaticDbHelper;
        $data = $userstaticDbHelper->getUserStaticData($uid);

        //分配变量 （顺序随意）
        View::assign('head','欢朋直播');
        View::assign('user_data',$data[$uid]);
        //头（顺序随意）
        View::includeHeader('index/head');
        //底（顺序随意）
        View::includeFooter('index/footer');
        //注意这个一定要写在最后，，，
        View::display('index/user');
        //View::display('index/user', ['user_data',$data]);
    }
}