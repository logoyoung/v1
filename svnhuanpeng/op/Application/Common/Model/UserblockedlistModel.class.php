<?php

namespace Common\Model;

class UserblockedlistModel extends PublicBaseModel
{
    public function getType($index = false)
	{
		$hash = ['1'=>'封号','2'=>'解除封号'];
		return $index === false?$hash:$hash[$index];
	}

}
