<?php

namespace Common\Model;

class RedPacketModel extends PublicBaseModel
{
	public function getrpkstatus($index = false)
	{
		$hash = ['1'=>'生效中','0'=>'未生效'];
		return $index === false?$hash:$hash[$index];
	}
}
