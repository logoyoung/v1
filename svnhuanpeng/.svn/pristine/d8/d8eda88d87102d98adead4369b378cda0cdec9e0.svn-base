<?php
/**
 * 退单申请
 */
namespace Common\Model;

class DueOrderModel extends PublicBaseModel
{
	public  function getCheckstatus($index = false)
	{
		$hash = [120=>'申诉中',130=>'申诉通过',140=>'申诉驳回',1000=>'交易完成',1010=>'交易取消'];
		return $index === false?$hash:$hash[$index];
	}
	public  function getStatus($index = false)
	{
		$hash = ['check'=>'120','pass'=>'130','unpass'=>'140','complete'=>'1000','cancel'=>'1010'];
		return $index === false?$hash:$hash[$index];
	}
}
