<?php

namespace Common\Model;

class WaitPassVideoModel extends BaseModel
{
    
    //录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除
    public function getCheckstatus($index = false)
    {
        $hash = [VIDEO_WAIT=>'待审核',VIDEO_UNPUBLISH=>'审核中',VIDEO=>'已发布 ',VIDEO_UNPASS=>'审核未通过',VIDEO_DEL=>'删除'];
        return $index === false?$hash:$hash[$index];
    }
    //录像状态 0:未锁定 1:锁定2:审核通过3:审核未通过
    public function getCheckstatus2($index = false)
    {
        //$hash = [0=>'未锁定',1=>'已锁定 ',2=>'审核通过',3=>'审核未通过'];
		$hash = [0=>'待审核', 2=>'审核通过',3=>'审核未通过'];
        return $index === false?$hash:$hash[$index];
    }
}
