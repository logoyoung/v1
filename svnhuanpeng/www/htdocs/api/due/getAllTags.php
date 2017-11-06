<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/17
 * Time: 9:59
 */
include '../../../include/init.php';
use service\due\DueTagsService;
class getAllTags
{
    public function getTags()
    {
        $res = [];
        $tagSertive = new DueTagsService();
        $data = $tagSertive->getAllTags();
        foreach($data as $key=>$value)
        {
            $res[$key]['id'] = $value['id'];
            $res[$key]['tag'] = $value['tag'];
        }
        return $res;
    }
    public function display()
    {
        $list['list']= $this->getTags();
        render_json($list);
    }
}
$obj = new getAllTags();
$obj->display();