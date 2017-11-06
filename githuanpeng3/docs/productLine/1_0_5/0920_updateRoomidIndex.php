<?php
require_once (dirname(__DIR__) .DIRECTORY_SEPARATOR ."BaseSql.php");

class updateRoomidIndex  extends BaseSql{
    public $limitNum = 1;

    public function up() {

        $sqls = <<<EOF
ALTER TABLE `roomid` DROP INDEX `uid`;
ALTER TABLE `roomid` ADD UNIQUE KEY `uniq_uid_rid`(`uid`,`roomid`);
EOF;
        return $this->dbexec($sqls);
    }
}