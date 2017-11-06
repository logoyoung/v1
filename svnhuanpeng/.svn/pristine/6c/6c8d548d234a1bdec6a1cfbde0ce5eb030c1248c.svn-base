<?php

namespace Common\Model;

class AdminRecommendLiveModel extends PublicBaseModel
{
    public function getStatus($index = false)
    {
        $hash = ['0'=>'待推荐','1'=>'已推荐'];
        return $index === false?$hash:$hash[$index];
    }
}
