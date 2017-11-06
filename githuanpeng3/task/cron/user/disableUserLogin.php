<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use service\user\UserDisableStatusService;

/**
 * 用户封禁
 */
class disableUserLogin
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

    public function getStatus()
    {
        fwrite(STDOUT, "请选择操作; 1解禁，2封禁;输入完毕请按回车键确认\n");
        $stdin  = fopen('php://stdin', 'r');
        $status = (int) trim(fgets($stdin));

        if(!$status || !in_array($status,UserDisableStatusService::$allStatus))
        {
            fwrite(STDOUT, " 无效的操作，1解禁，2封禁 \n");
            fclose($stdin);
            exit;
        }

        fclose($stdin);

        return (int) $status;
    }

    public function getEtime()
    {
        fwrite(STDOUT, "请输入封禁时间; 0永久;单位秒输入完毕请按回车键确认\n");
        $stdin  = fopen('php://stdin', 'r');
        $etime  = (int) trim(fgets($stdin));
        fclose($stdin);
        return $etime ? $etime : 0;
    }

    public function run()
    {

        $uid    = $this->getUid();
        $status = $this->getStatus();

        try {

            $disableService = new UserDisableStatusService();
            $disableService->setUid($uid);
            $disableService->setType(UserDisableStatusService::USER_DISABLE_TYPE_LOGIN);

            switch ($status)
            {

                case UserDisableStatusService::USER_DISABLE_STATUS_OFF:

                    $result = $disableService->deleteDisable();
                    if(!$result)
                    {
                        exit("解禁失败\n");
                    }

                    exit("解禁成功\n");

                case UserDisableStatusService::USER_DISABLE_STATUS_ON:
                    $etime  = $this->getEtime();
                    $disableService->setEtime($etime);
                    $result = $disableService->addDisable();
                    if(!$result)
                    {
                        exit("封禁失败\n");
                    }

                    exit("封禁成功\n");

                default:
                    exit("无效的操作，1解禁，2封禁\n");
            }


        } catch (Exception $e)
        {
            echo '失败:',$e->getCode(),"\n",$e->getMessage(),"\n";
            exit;
        }

    }
}

$obj = new disableUserLogin();
$obj->run();
