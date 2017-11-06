<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use lib\user\UserStatic;
use lib\user\UserActive;
use system\Timer;
use service\user\helper\RepairUserRedisData;

/**
 * 校验用户redis与db一致性，如发现不一致的以db为准并自动修复
 *
 */
class checkRedisData
{

    private $logName = 'cron_check_user_redis';
    private $size    = 500;

    public function run()
    {
        $this->log('start|校验用户redis与db一致性开始');
        $timer  = new Timer();
        $timer->start();
        $userStaticDao    = new UserStatic();
        $userActiveDao    = new UserActive();
        $totalNum         = $userStaticDao->getUserTotalNum();
        if($totalNum == 0)
        {
            $this->log('error|从数据库获取用户总数异常');
            die(-1);
        }

        $this->log("notice|共计:{$totalNum} 个用户需要校验");

        $totalPage     = ceil($totalNum / $this->size);
        $eqNum         = 0;
        $neqNum        = 0;
        $resetSuccNum  = 0;
        $resetErrNum   = 0;
        $repairHelper  = new RepairUserRedisData();

        for ($page = 1; $page <= $totalPage; $page++)
        {
            $staticData = $userStaticDao->getUserStaticList($page,$this->size);

            if(!$staticData)
            {
                break;
            }

            $uids = array_column($staticData,'uid');
            if(!$uids)
            {
                $this->log("error|从userstatic表里没有获取到uid,整个校验停止,校验成功:{$resetSuccNum}个");
                return false;
            }

            $activeData = $userActiveDao->getUserActiveData($uids);
            if($activeData === false)
            {
                $this->log("error|获取useractive数据异常,整个校验停止");
                return false;
            }

            if( count($activeData) != count($uids))
            {
                $this->log("error|获取useractive数据与userstatic表不一致,整个校验停止 校验成功:{$resetSuccNum}");
                return false;
            }

            foreach ($staticData as $v)
            {

                $repairHelper->setUid($v['uid']);
                $repairHelper->setLogName($this->logName);
                $repairHelper->setUserStaticDbData($v);
                $repairHelper->setUserActiveDbData($activeData[$v['uid']]);
                if($repairHelper->checkDataStatus() === true)
                {
                    $eqNum++;
                    continue;
                }
                $neqNum++;
                //修复异常数据
                if($repairHelper->rebuild())
                {
                    $resetSuccNum++;
                } else
                {
                    $resetErrNum++;
                    $this->log("error| 构建 redis 缓存失败 uid:{$v['uid']}");
                }

            }

            unset($staticData,$activeData);
            if(($page % 2) == 0)
            {
                sleep(1);
            }
        }

        $timer->end();
        $t = $timer->getTime();
        $checkEq = $resetErrNum > 0 ? 'end| error' : 'end |success';
        $this->log("{$checkEq}| 校验完成; 耗时:{$t}s; 用户总数:{$totalNum} 条; 一致数量:{$eqNum} 条; 不一致数量:{$neqNum} 条;构造成功:{$resetSuccNum} 条; 构建失败: {$resetErrNum} 条");

        return true;
    }

    public function log($msg)
    {
        write_log($msg,$this->logName);
    }
}

$obj = new checkRedisData();
$obj->run();