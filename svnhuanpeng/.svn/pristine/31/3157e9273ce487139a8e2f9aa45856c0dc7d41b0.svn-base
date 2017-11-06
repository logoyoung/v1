<?php

namespace Common\Model;

class UserPicModel extends BaseModel
{

    
    //头像审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
    public function getCheckstatus($index = false)
    {
        $hash = [USER_PIC_WAIT=>'待审核',USER_PIC_PASS=>'人工审核通过',USER_PIC_UNPASS=>'人工审核未通过 ',USER_PIC_AUTO_PASS=>'机器审核通过',USER_PIC_AUTO_UNPASS=>'机器审核未通过'];
        return $index === false?$hash:$hash[$index];
    }

	public function getCheckstatus2($index = false)
	{
		//$hash = [USER_PIC_WAIT=>'待审核',USER_PIC_PASS=>'审核通过',USER_PIC_UNPASS=>'审核未通过 ',USER_PIC_AUTO_PASS=>'待审核',USER_PIC_AUTO_UNPASS=>'待审核'];
		$hash = [USER_PIC_AUTO_PASS=>'待审核',USER_PIC_PASS=>'通过',USER_PIC_UNPASS=>'未通过 '];
		return $index === false?$hash:$hash[$index];
	}
}
