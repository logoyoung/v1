<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use system\DbHelper;
use service\room\RoomManagerService;
use service\user\UserDisableStatusService;

/**
 * 导入老版本的直播间禁言数据到新版本
 *  (没有意外的情况下，线上只需要使用一次)
 * /usr/local/php7/bin/php /usr/local/huanpeng/task/cron/user/importOldSilencedData.php
 */
class importOldSilencedData
{
    const DB_CONF = 'huanpeng';

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    //注意：目前表里全部只有300多条数据，所以全取出来处理无需考虑性能问题
    public function getOldDbData()
    {

        $sql = 'SELECT * FROM `usersilence` WHERE `id` IN( SELECT max(`id`) FROM `usersilence` WHERE `type` = 1 AND `etime` > now() group by `uid`,`luid`) order by `id` desc';

        $sql = "SELECT * FROM `usersilence` WHERE `id` IN('1651','1655')";
        try {

            return $this->getDb()->query($sql);

        } catch (Exception $e)
        {
            exit("数据库异常:msg:{$e->getMessage()}");
        }
    }

    public function run()
    {
        $oldDbData = $this->getOldDbData();
        if(!$oldDbData)
        {
            exit('从数据库没有获取到老版本禁言数据');
        }

        write_log($oldDbData,'import_old_db_silenced_data');
        $tnum        = count($oldDbData);
        $roomids     = array_values(array_unique((array_filter((array_column($oldDbData,'roomid'))))));
        $managerService = new RoomManagerService;
        $managerService->setRoomid($roomids);
        $anchorUids  = $managerService->getUidByRoomid();
        $insertData  = [];

        foreach ($oldDbData as $v)
        {
            if($v['roomid'] == 0)
            {
                $v['scope']   = 0;
                $insertData[] = $v;
                continue;
            }

            if(isset($anchorUids[$v['roomid']]))
            {
                $v['scope']   = $anchorUids[$v['roomid']];
                $insertData[] = $v;
            }

        }

        if(!$insertData)
        {
            exit('insertData 为空，没有需要导入的数据');
        }

        $inum = count($insertData);
        $snum = 0;
        $enum = 0;
        write_log($insertData,'import_insert_silenced_data');

        $disableService = new UserDisableStatusService;

        foreach ($insertData as $d)
        {
            $disableService->setUid($d['luid']);
            $disableService->setType($disableService::USER_DISABLE_TYPE_SEND_MSG);
            $disableService->setStatus($disableService::USER_DISABLE_STATUS_ON);
            $disableService->setScope($d['scope']);
            $disableService->setEtime((strtotime($d['etime']) - time()));
            $disableService->setPlatform($d['fromto']);
            $disableService->setAcUid($d['uid']);
            $disableService->setDesc($d['reason']);

            if($disableService->addDisable())
            {
                $snum++;
                write_log('success|导入成功; data: '.hp_json_encode($d),'import_insert_silenced_data');
            } else
            {
                $enum++;
                write_log('error|导入成功; data: '.hp_json_encode($d),'import_insert_silenced_data');
            }
        }

        $unum = $tnum - $inum;
        exit("执行完成:\n 老版数据总数:{$tnum}; \n 无效(不需要导入）数据:{$unum}; \n需要导入数据:{$inum}; \n导入成功:{$snum}; \n导入失败:{$enum}\n");
    }

}

$obj = new importOldSilencedData;
$obj->run();