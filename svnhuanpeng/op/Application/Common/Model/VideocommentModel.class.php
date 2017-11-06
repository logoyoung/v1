<?php

namespace Common\Model;

class VideocommentModel extends PublicBaseModel
{
    public function getCheckstatus($index = false)
	{
		$hash = [1=>'人工审核通过', 2=>'人工审核未通过', 3=>'机器审核通过', 4=>'机器审核未通过'];
		return $index === false?$hash:$hash[$index];
	}
}
