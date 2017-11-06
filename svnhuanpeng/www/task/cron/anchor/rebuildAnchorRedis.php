<?php
ini_set('memory_limit', '1024M');
require __DIR__ . '/../../bootstrap/i.php';
use service\anchor\helper\RepairAnchorRedisData;
use service\room\helper\RepairRoomidRedisData;

/**
 *
 * 修复主播数据 （anchor与roomid表）
 *
 */
class rebuildAnchorRedis
{
    public function getUid()
    {
        fwrite(STDOUT, "请输入主播uid; 输入完毕请按回车键确认\n");
        $stdin = fopen('php://stdin', 'r');
        $uid   = trim(fgets($stdin));
        if (!$uid || !is_numeric($uid)) {
            fwrite(STDOUT, "无效的uid \n");
            fclose($stdin);
            exit;
        }

        fclose($stdin);

        return $uid;
    }

    public function run()
    {
        $uid = $this->getUid();
        $anchorRepair = new RepairAnchorRedisData;
        $anchorRepair->setUid($uid);
        $roomidRepair = new RepairRoomidRedisData;
        $roomidRepair->setUid($uid);
        $as  = $anchorRepair->checkDataStatus();
        $rs  = $roomidRepair->checkDataStatus();
        if($as && $rs)
        {
            exit("uid:{$uid}; mysql 与 redis 数据一致 无需修复 \n");
        }

        if(!$as)
        {
            echo "anchor mysql数据与redis 不一致\n";
        }

        if(!$rs)
        {
            echo "roomid mysql数据与redis 不一致\n";
        }

        echo "执行修复中,请稍后……\n";

        if(!$anchorRepair->rebuild() || !$roomidRepair->rebuild())
        {
            exit("uid:{$uid}; 执行修复异常,请查看查关修复逻辑代码\n");
        }

        sleep(1);

        $anchorRepair->setUid($uid);
        $roomidRepair->setUid($uid);
        $as  = $anchorRepair->checkDataStatus();
        $rs  = $roomidRepair->checkDataStatus();
        if($as && $rs)
        {
            exit("uid:{$uid}; 修复成功 \n");
        }

        exit("uid:{$uid}; 执行修复异常,修复后的redis数据与mysql不一致\n");

    }
}

$obj = new rebuildAnchorRedis();
$obj->run();
