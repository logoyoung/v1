<?php

/**
 * 用户注册类
 * date 2015-12-10 18:14pm
 * author yandong@6rooms.com
 * copyright@六间房 version0.0
 */
class Register {

    protected $_tbname = 'userstatic';
    private static $_mydb = null;

    //构造函数
    public function __construct() {
        if (is_null(self::$_mydb)) {
            self::$_mydb = new DBHelperi_huanpeng();
        }
    }

    /**
     * 用户注册
     * @param string $nick
     * @param string $password
     * @param string $cpassword
     * return json
     */
    public function reg($mobile, $nick, $password) {
        return $this->addUser($mobile, $nick, $password);
    }

    /**
     * 过滤用户名称&检测用户名长度
     * @param string $uername
     */
    public function userName($nick) {
        if (mb_strlen($nick, 'utf-8') < 3 || mb_strlen($nick, 'utf-8') > 10) {
            return false;
        } else {
            if (mb_strlen($nick, 'latin1') < 3 || mb_strlen($nick, 'latin1') > 30) {
                return false;
            } else {
                return true;
            }
        }
    }

    /*
     * 检测密码是不是一致
     * @paramer $password string
     * @paramer $cpassword string
     * return 一致返回true 不一致返回false 
     */

    public function checkPasswordIsOk($password, $cpassword) {
        if (isset($password) && isset($cpassword) && $password === $cpassword) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 添加用户 (这里可以将用户名保存到redis里面);
     * @param string $nick
     * @param string $passWord
     * @param object $db  数据库操作对象
     * @return type
     */
    public function addUser($mobile, $nick, $password) {
        $data = array(
            'username' => "$mobile",
            'password' => md5password($password),
            'nick' => $nick,
            'phone' => $mobile,
            'rip' => ip2long(fetch_real_ip($rport)),
            'rport' => $rport,
            'rtime' => get_datetime(),
            'encpass' => md5(md5($password . time())),
            'sex' => 1
        );
        $res = self::$_mydb->insert($this->_tbname, $data);
        if (!$res) {
            return false;
        } else {
            $activeDate = array(
                'uid' => $res,
                'lip' => ip2long(fetch_real_ip($lport)),
                'lport' => $lport,
                'ltime' => get_datetime()
            );
            $result = self::$_mydb->insert('useractive', $activeDate);
            if (!$result) {
                return false;
            } else {
                return array('uid' => $res, 'encpass' => $data['encpass']);
            }
        }
    }

}
