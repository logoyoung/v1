<?php
namespace dota\bootstrap;
use Exception;

class DotaAuth
{

    public function authorize($dispatcher)
    {
//        return true;
        if(!isset($_REQUEST['token']) || !$_REQUEST['token'])
        {
            throw new Exception('empty token', 701);
        }

        if(!token_check($_REQUEST,DOTA_AUTHORIZE_KEY))
        {
            throw new Exception('invalid token', 702);
        }

        return true;
    }
}