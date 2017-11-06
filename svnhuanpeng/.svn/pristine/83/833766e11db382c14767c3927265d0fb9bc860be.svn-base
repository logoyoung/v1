<?php
// +----------------------------------------------------------------------
// | 全局参数过滤
// +----------------------------------------------------------------------

namespace Common\Behavior;

class FliterparamBehavior extends \Think\Behavior
{
    public function run(&$params)
    {
        \HP\Secure\Filter::exec();
    }
}
