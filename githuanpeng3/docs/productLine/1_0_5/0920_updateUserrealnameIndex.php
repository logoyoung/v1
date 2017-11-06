<?php
require_once (dirname(__DIR__) .DIRECTORY_SEPARATOR ."BaseSql.php");

class updateUserrealnameIndex  extends BaseSql{
    public $limitNum = 1;

    public function up() {

        $sqls = <<<EOF
ALTER TABLE `userrealname` ADD INDEX `idx_papersid_status`(`papersid`,`status`);
EOF;
        return $this->dbexec($sqls);
    }
}