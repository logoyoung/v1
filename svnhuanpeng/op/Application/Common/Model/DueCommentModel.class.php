<?php

namespace Common\Model;

class DueCommentModel extends PublicBaseModel
{

	public  function getCheckstatus($index = false)
	{
		$hash = [-1=>'待审核',2=>'审核通过',4=>'审核未通过'];
		return $index === false?$hash:$hash[$index];
	}
	public  function getCheckstatus2($index = false)
	{
		$hash = ['wait'=>'-1','pass'=>'2','unpass'=>'4'];
		return $index === false?$hash:$hash[$index];
	}

}
