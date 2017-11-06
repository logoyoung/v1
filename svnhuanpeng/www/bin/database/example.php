<?php

class example {

    private $tool;

    public function __construct($tool) {
        $this->setTools($tool);
    }

    public function setTools($tool) {
        $this->tool = $tool;
    }

    public function getTools() {
        return $this->tool;
    }

    public function getDb() {
        return $this->getTools()->getdb();
    }

    /**
     * 待执行方法
     * @return boolean
     */
    public function up() {
        /**
         * @todo 执行需要执行的sql
         */
        $sql = "";
        
        $res = $this->getDb()->execute($sql);
        
        if ($res) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
