<?php

namespace Common\Model;

class AnchorapplycompanyModel extends PublicBaseModel
{
    public  function getCheckstatus($index = false)
	{
		$hash = [
            0=>'公司未审核', 
            1=>'主播取消', 
            2=>'公司审核通过',
            3=>'公司审核未通过',
            4=>'官方审核通过',
            5=>'官方审核未通过',
            6=>'已解约',
            100=>'异常状态'
		];
		return $index === false?$hash:$hash[$index];
	}

    public  function getLiveStyle($index = false)
    {
        $hash = [
            1=>'露脸',
            2=>'不露脸',
        ];
        return $index === false?$hash:$hash[$index];
    }
}
