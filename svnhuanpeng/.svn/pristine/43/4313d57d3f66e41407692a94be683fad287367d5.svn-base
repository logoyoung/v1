<?php

namespace Common\Model;

class VideoModel extends PublicBaseModel
{
    
    //录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除
    public function getCheckstatus($index = false)
    {
        $hash = [VIDEO_WAIT=>'待审核',VIDEO_UNPUBLISH=>'审核中',VIDEO=>'已发布 ',VIDEO_UNPASS=>'审核未通过',VIDEO_DEL=>'删除'];
        return $index === false?$hash:$hash[$index];
    }
}
