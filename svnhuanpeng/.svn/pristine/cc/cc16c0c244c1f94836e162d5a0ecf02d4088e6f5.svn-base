<?php
// +----------------------------------------------------------------------
// | 性能检测
// +----------------------------------------------------------------------

namespace Common\Behavior;
class ProfileBehavior extends \Think\Behavior
{
    public function run(&$params)
    {
        header('Via:d1'.N('db_query').',d2'.N('db_write'));
    }
}