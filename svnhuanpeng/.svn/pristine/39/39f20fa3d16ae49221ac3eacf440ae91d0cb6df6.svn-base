<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use service\user\UserAuthService;
/**
 * 校验用户是否被禁言
 *
 */
class checkSilencedStatus
{
    public function getUid()
    {
        fwrite(STDOUT, "请输入uid; 输入完毕请按回车键确认\n");
        $stdin = fopen('php://stdin', 'r');
        $uid   = trim(fgets($stdin));
        if(!$uid || !is_numeric($uid))
        {
            fwrite(STDOUT, "无效的uid \n");
            fclose($stdin);
            exit;
        }

        fclose($stdin);

        return (int) $uid;
    }

    public function getScope()
    {
        fwrite(STDOUT, "请输入封禁范围，1全局，或者直播间主播uid; 输入完毕请按回车键确认\n");
        $stdin  = fopen('php://stdin', 'r');
        $scope  = (int) trim(fgets($stdin));
        fclose($stdin);
        if(!$scope)
        {
            fwrite(STDOUT, " 无效的封禁范围， 1全局，或者直播间主播uid\n");
            fclose($stdin);
            exit;
        }
        return $scope;
    }

    public function run()
    {
        $uid   = $this->getUid();
        $scope = $this->getScope();

        $auth = new UserAuthService;
        //用户uid
        $auth->setUid($uid);
        //主播uid
        $auth->setAnchorUid($scope);
        //获取直播间禁言状态
        if($auth->checkSilencedStatus() === false)
        {
            //获取错误结果集
            $result = $auth->getResult();
            //错误码
            $code   = $result['error_code'];
            //错误消息
            $msg    = $result['error_msg'];
            //解禁时间 （时间戳）
            $etime  = $result['silenced_etime'];

            print_r($result);

            return false;
        }

        exit("正常\n");
    }

}

$obj = new checkSilencedStatus;
$obj->run();