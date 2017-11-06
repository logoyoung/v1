<?php
ini_set('memory_limit', '1024M');
require __DIR__ . '/../../bootstrap/i.php';
use service\anchor\helper\RepairAnchorRedisData;
use service\room\helper\RepairRoomidRedisData;
use system\Timer;
use lib\anchor\Anchor;

class cronCheckAnchorRedis
{
    private $logName = 'cron_check_anchor_redis';
    private $size    = 500;

    public function run()
    {
        $this->log('start|校验anchor与roomid 用户redis与db一致性开始');
        $timer  = new Timer();
        $timer->start();
        $anchorDb     = new Anchor;
        $anchorRepair = new RepairAnchorRedisData;
        $roomidRepair = new RepairRoomidRedisData;
        $totalNum     = (int) $anchorDb->getUserTotalNum();
        if($totalNum <= 0 )
        {
            exit("error|获取anchor表总数异常,脚本停执行\n");
        }

        $this->log("into|anchor总数:{$totalNum}");
        $totalPage     = ceil($totalNum / $this->size);
        $eqNum         = 0;
        $neqNum        = 0;
        $resetSuccNum  = 0;
        $resetErrNum   = 0;

        for ($page = 1; $page <= $totalPage; $page++)
        {

            if(($page % 2) == 0)
            {
                sleep(1);
            }

            $anchorData = $anchorDb->getAnchorList($page, $this->size, ['uid']);
            if(!$anchorData)
            {
                break;
            }

            $uids = array_column($anchorData,'uid');
            if(!$uids)
            {
                $this->log("error|从userstatic表里没有获取到uid,整个校验停止,校验成功:{$resetSuccNum}个;line:".__LINE__);
                return false;
            }

            foreach ($uids as $uid)
            {

                $anchorRepair->setUid($uid);
                $roomidRepair->setUid($uid);
                $as  = $anchorRepair->checkDataStatus();
                $rs  = $roomidRepair->checkDataStatus();
                if($as && $rs)
                {
                    $eqNum++;
                    continue;
                }

                $neqNum++;
                $s1 = $anchorRepair->rebuild();
                $s2 = $roomidRepair->rebuild();
                if(!$s1 || !$s2)
                {
                    $this->log("error|uid:{$uid}; 执行修复异常,请查看查关修复逻辑代码;line:".__LINE__);
                    $resetErrNum++;
                    continue;
                }

                $anchorRepair->setUid($uid);
                $roomidRepair->setUid($uid);
                $as  = $anchorRepair->checkDataStatus();
                $rs  = $roomidRepair->checkDataStatus();

                if($as && $rs)
                {
                    $resetSuccNum++;
                    $this->log("success|uid:{$uid};重构造anchor 与roomid redis数据成功");
                } else
                {
                    $resetErrNum++;
                    $this->log("error|uid:{$uid}; 执行修复异常,修复后的redis数据与mysql不一致");
                }
            }

        }

        $timer->end();
        $t = $timer->getTime();
        $checkEq = $resetErrNum > 0 ? 'end| error' : 'end |success';
        $this->log("{$checkEq}| 校验完成; 耗时:{$t}s; 主播总数:{$totalNum} 条; 一致数量:{$eqNum} 条; 不一致数量:{$neqNum} 条;构造成功:{$resetSuccNum} 条; 构建失败: {$resetErrNum} 条");

        return true;
    }

    public function log($msg)
    {
        write_log($msg,$this->logName);
    }
}

$obj = new cronCheckAnchorRedis();
$obj->run();