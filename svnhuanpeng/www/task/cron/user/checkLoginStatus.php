<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use service\user\UserAuthService;
/**
 * 校验用户 encpass 小工具
 *
 */
class checkLoginStatus
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

    public function getEnc()
    {
        fwrite(STDOUT, "请输入encpass; 输入完毕请按回车键确认\n");
        $stdin = fopen('php://stdin', 'r');
        $enc   = trim(fgets($stdin));
        if(!$enc)
        {
            fwrite(STDOUT, "无效的encpass \n");
            fclose($stdin);
            exit;
        }

        fclose($stdin);

        return $enc;
    }

    public function run()
    {
        $uid = $this->getUid();
        $enc = $this->getEnc();
        $auth = new UserAuthService;
        $auth->setUid($uid);
        $auth->setEnc($enc);
        if($auth->checkLoginStatus() !== true)
        {
            echo "redis中校验 用户encpass无效\n";
            $auth->setFromDb(true);
            if($auth->checkLoginStatus() !== true)
            {
                echo "db中校验用户encpass无效\n";
            } else
            {
                echo "db中校验用户encpass成功\n";
            }

            print_r($auth->getResult());

        } else
        {
            echo "用户encpass 正常\n";
        }

    }
}

$obj = new checkLoginStatus();
$obj->run();