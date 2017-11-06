<?php

namespace Common\Model;

class AclUserModel extends BaseModel
{
    protected $record_write = true;
    protected $_validate = array(
        array('username', 'require', '用户名必填！'),
        array('realname', 'require', '姓名必填！'),
        array('username', '', '帐号名称已经存在！', 0, 'unique'),
    );
}
