<?php

namespace Common\Model;

class UserrealnameModel extends PublicBaseModel
{
    //实名认证状态 0：未申请，1:待审核, 100:审核未通过, 101:审核通过
     public function getCheckstatus($index = false)
     {
         //$hash = [RN_NOT=>'未申请',RN_WAIT=>'待审核',RN_UNPASS=>'审核未通过',RN_PASS=>'审核通过'];
		 $hash = [RN_WAIT=>'待审核',RN_UNPASS=>'审核未通过',RN_PASS=>'审核通过'];
         return $index === false?$hash:$hash[$index];
     }
}
