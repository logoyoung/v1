<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/23
 * Time: 15:00
 */
include '../../../include/init.php';

use service\weixin\WeiXinEnterpriseService;

class resetAccessToken
{
    public function getAccessToken()
    {
        $weixin = new WeiXinEnterpriseService();
        $weixin->setFromApi(true);
        $res = $weixin->getAccessToken();
        return $res;

    }
    public function action()
    {
        $res = $this->getAccessToken();
        if($res)
        {
            render_json($res);
        }else
        {
            $content = ' reset AccessToken failed please check log';
            render_json($content);
        }
    }
}
$action = new resetAccessToken();
$action->action();