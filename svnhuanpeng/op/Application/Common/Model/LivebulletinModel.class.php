<?php

namespace Common\Model;

class LivebulletinModel extends PublicBaseModel
{
    public function getCheckStatus($index = false)
    {
        $hash = ['0'=>'待审核','1'=>'已通过','2'=>'未通过'];
        return $index === false?$hash:$hash[$index];
    }
    
    public function getluid($index = false)
    {
        return '860,1930,2055,2065,2140,2780,3055,3100,3415,3445,3490,4100,8190,8645,9100,11735,13495,20505';
    }
    
}
