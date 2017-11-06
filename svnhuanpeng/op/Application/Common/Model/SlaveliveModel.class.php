<?php

namespace Common\Model;

class SlaveliveModel extends PublicBaseModel
{
	public  function getCheckstatus($index = false)
	{
		$hash = [0=>'未审核', 1=>'审核通过', 2=>'审核未通过'];
		return $index === false?$hash:$hash[$index];
	}
	public  function getLivestatus($index = false)
	{
		$hash = ['100'=>'正在直播','200'=>'已经结束'];
		return $index === false?$hash:$hash[$index];
	}
}
