<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/27
 * Time: 下午4:49
 */
class AdminHelp
{
    private $uid;
    private $db;
    private $type;

    function __construct($uid, $type, $db = null)
    {
        if (!$uid || !$type)
            return false;

        $this->uid = $uid;
        if ($db)
            $this->db = $db;
        else
            $this->db = new DB();

        $this->type = $type;

        return true;
    }

    /**
     * check the adminer has login error
     *
     * @param $enc
     *
     * @return int if error return the error code else return 0
     */
    public function loginError($enc)
    {
        $sql = "select encpass, usertype from admin_user where uid = $this->uid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();


        if (!$row['encpass']) {
            return -1001;
        }
        if ($row['encpass'] != $enc) {
            return -1005;
        }

        if ($row['usertype'] != $this->type)
            return -1006;

        return 0;
    }

    /**
     * check the adminer is has $type
     * @param $type
     *
     * @return bool
     */
    public function checkTheType($type)
    {
        if (self::isHasTheType($this->uid, $type, $this->db))
            return true;

        return false;
    }

    /**
     * change adminer role in the Management background
     *
     * @param $type
     *
     * @return bool
     */
    public function  changeStatus($type)
    {
        $sql = "update set admin_user set usertype = $type";
        $this->db->query($sql);
        if ($this->db->affectedRows)
            return true;

        return false;
    }

    /**
     * login to the Management background
     *
     * @param      $email
     * @param      $passWord
     * @param int  $type
     * @param null $db
     *
     * @return int
     */
    static function toLoginError($email, $passWord, $type = 0, $db = null)
    {
        if (!$db)
            $db = new DBHelperi_admin();

        $sql = "select uid, password,username from admin_user where email = '$email'";
        $res = $db->query($sql);
        $row = $res->fetch_assoc();

        if (!$row['uid']) {
            return -1001;
        }

        if ($row['password'] != $passWord) {
            return -1002;
        }

        if (!self::isHasTheType($row['uid'], $type, $db)) {
            return -1003;
        }

        $enc = md5($row['uid'] . $email . $passWord . time());

        $sql = "update admin_user set encpass='$enc', usertype=$type where uid ={$row['uid']}";
        if ($db->query($sql)) {
            self::setLoginCookie($row['uid'], $enc, $type,$row['username']);
            return array(
                'uid' => $row['uid'],
                'encpass' => $enc,
                'type' => $type
            );
        }

        return -1004;

    }

    static function isHasTheType($uid, $type, $db = null)
    {
        if (!$db) $db = new DBHelperi();
        $sql = "select * from admin_user_right where uid = $uid and `type` = $type";
        $res = $db->query($sql);
        $row = $res->fetch_assoc();
        if ((int)$row['uid']) {
            return true;
        }

        return false;
    }


    static function setLoginCookie($uid, $enc, $type,$name)
    {
        setcookie('admin_uid', $uid, time() + 24*3600, '/admin2', '.huanpeng.com');
        setcookie('admin_enc', $enc, time() + 24*3600, '/admin2', '.huanpeng.com');
        setcookie('admin_type', $type, time() + 24*3600, '/admin2', '.huanpeng.com');
        setcookie('admin_name', $name, time() + 24*3600, '/admin2', '.huanpeng.com');
    }

}