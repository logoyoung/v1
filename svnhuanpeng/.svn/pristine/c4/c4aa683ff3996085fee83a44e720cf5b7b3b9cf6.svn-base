<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use service\user\UserDisableStatusService;
use service\user\UserAuthService;
/**
 * 校验主播是否被禁播
 */
class checkLive
{
    public function getUid()
    {
        fwrite(STDOUT, "请输入主播uid; 输入完毕请按回车键确认\n");
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

    public function run()
    {
        $uid = $this->getUid();

        try {

            $auth = new UserAuthService;
            $auth->setUid($uid);

            if($auth->checkAnchorLiveStatus() !== true)
            {
                echo "redis中校验 直播禁止\n";
                $auth->setFromDb(true);
                if($auth->checkAnchorLiveStatus() !== true)
                {
                    echo "db中校验用户直播禁止\n";
                } else
                {
                    echo "db中校验用户直播正常，请重新构造redis缓存\n";
                }

                print_r($auth->getResult());

            } else
            {
                echo "直播正常\n";
            }

        } catch (Exception $e) {
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";
        }
    }
}

$obj = new checkLive();
$obj->run();