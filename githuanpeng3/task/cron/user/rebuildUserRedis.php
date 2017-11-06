<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';

use service\user\helper\RepairUserRedisData;

/**
 * 重新构建用户redis缓存
 */
class rebuildUserRedis
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
            die;
        }

        fclose($stdin);

        return $uid;
    }

    public function run()
    {
        $uid           = $this->getUid();
        $repairHelper  = new RepairUserRedisData();
        $repairHelper->setUid($uid);

        if($repairHelper->checkDataStatus() === true)
        {
            exit("数据库与redis数据一致，无需重建\n");
        }

        echo "执行uid:{$uid}重建缓存中，请稍后\n";

        if(!$repairHelper->setUid($uid)->rebuild())
        {
            exit("uid:{$uid} 重建redis缓存异常\n");
        }

        sleep(2);

        if($repairHelper->setUid($uid)->checkDataStatus() === true)
        {
            echo "uid:{$uid} 重建redis缓存成功\n";
        } else
        {
            echo "uid:{$uid} 重建redis缓存异常\n";
        }

    }

}

$obj = new rebuildUserRedis();
$obj->run();