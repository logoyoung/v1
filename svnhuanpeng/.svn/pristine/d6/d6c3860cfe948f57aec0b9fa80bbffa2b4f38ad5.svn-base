<?php
require __DIR__.'/../../include/init.php';
use system\DbHelper;

class t3 {

    public function t() {
        $data = [];
        try {
            $db     = DbHelper::getInstance('huanpeng');
            $table  = 'user_status';
            $type   = [10,20,30];

            for($i = 0 ; $i <= 2000000; $i++)
            {
                $param = [
                    'uid'     => time().mt_rand(100000000, 2100000000),
                    'type'    => $type[mt_rand(0, 2)],
                    'status'  => mt_rand(1, 2),
                    'scope'   => mt_rand(1, 10),
                    'etime'   => time() + mt_rand(1, 6000),
                ];
                $sql = 'insert into `user_status`(`uid`,`type`,`status`,`scope`,`etime`) values(:uid,:type,:status,:scope,:etime)';
                $s = $db->execute($sql,$param);
                var_dump($s);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
}

$t3 = new t3;
$t3->t();