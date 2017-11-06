<?php

namespace Common\Model;

class GiftModel extends PublicBaseModel
{
    public function getSiteNotify($index = false)
    {
        $hash = [0=>'关闭',1=>'开启'];
        return $index === false?$hash:$hash[$index];
    }
	
	public function getStatus($index = false)
    {
        $hash = [0=>'正常',1=>'已删除'];
        return $index === false?$hash:$hash[$index];
    }
	
	public function getType($index = false)
    {
        $hash = [1=>'欢朋豆',2=>'欢朋币'];
        return $index === false?$hash:$hash[$index];
    }
}
