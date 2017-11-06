<?php
namespace bootstrap;
use Exception;

class Authrize
{
    public function handle($dispatcher)
    {
        return true;
        $router    = strtolower($dispatcher->getMatchRouter());
        $authList  = get_config('system/authrize_conf');
        $whiteApi  = (isset($authList['white_api']) &&  $authList['white_api']) ?  $authList['white_api'] : [];
        if($whiteApi)
        {
            $whiteApi = array_map('strtolower', $whiteApi);
        }

        if($whiteApi && in_array($router,$whiteApi))
        {
            return true;
        }

        throw new Exception('请重新登录', 403);
    }
}