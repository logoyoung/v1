<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/28
 * Time: 下午1:08
 */


class ReviewUser {
    const stat_free = 0;
    const stat_lock = 1;
    const stat_finish = 2;
    const db_cert = 'admin_certRealName';



    private $db;


    function __construct($db = null){
        if($db)
            $this->db = $db;
        else
            $this->db = new DBHelperi_admin();
    }

    function getLockTask($uid){
        $sql = "select certifyid from " . self::db_cert . " where uid = $uid and status = ". self::stat_lock;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['certifyid'];
    }

    function setLock($id, $uid){
        $sql = "update ". self::db_cert . " set uid = $uid , status = ".self::stat_lock." where certifyid = $id and status=".self::stat_free;
        $this->db->query($sql);

        return $this->db->affectedRows;
    }

    function getNewTask(){
        $sql = "select certifyid from " . self::db_cert . " where status = " .self::stat_free;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();
        return (int)$row['certifyid'];
    }

    function setFinish($id, $uid){
        $sql = "update " . self::db_cert . " set status = " .self::stat_finish . " where certifyid = $id and uid = $uid and status = " . self::stat_lock;
		$this->db->query($sql);

        return $this->db->affectedRows;
    }

    function getRealNameInfo($id){
        $sql = "select * from userrealname where id = $id";
        $res = $this->db->query($sql);

        $row = $res->fetch_assoc();

        return $row;
    }

}