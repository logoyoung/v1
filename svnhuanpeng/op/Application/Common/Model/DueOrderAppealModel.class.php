<?php
/**
 * 退单申请上传资料
 */
namespace Common\Model;

class DueOrderAppealModel extends PublicBaseModel
{
	public  function getCheckstatus($index = false)
	{
		$hash = [0=>'待审核',1=>'审核通过',2=>'审核未通过'];
		return $index === false?$hash:$hash[$index];
	}
	public  function getStatus($index = false)
	{
		$hash = ['check'=>'0','pass'=>'1','unpass'=>'2'];
		return $index === false?$hash:$hash[$index];
	}
}
