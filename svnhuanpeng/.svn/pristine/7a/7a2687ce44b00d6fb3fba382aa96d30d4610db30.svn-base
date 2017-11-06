<?php

namespace Common\Model;

class RecommendLiveModel extends PublicBaseModel
{
    public function getClient($index = false)
    {
        $hash = ['1'=>'app端','2'=>'web端'];
        return $index === false?$hash:$hash[$index];
    }
}
