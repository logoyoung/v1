<?php

namespace Common\Model;

class CompanyAnchorModel extends PublicBaseModel
{
    
    protected $_validate = array(
        array('cid', 'require', '请选择公司'),
        array('ctime', 'require', '请选择生效时间'),
        array('uid', 'require', 'uid不存在'),
    );
    
    public function getStatus($index = false)
    {
        $hash = ['0'=>'已签约','1'=>'未签约'];
        return $index === false?$hash:$hash[$index];
    }
}
