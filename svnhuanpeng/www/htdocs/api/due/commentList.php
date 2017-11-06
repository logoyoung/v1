<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:56
 */
include '../../../include/init.php';
use service\due\DueCertService;
/**
/**
 * 评论列表
 * Class commetList
 */
class commentList extends \service\common\ApiCommon
{
    public $skillId;
    public $pageSize;
    public $page;
    private function _init()
    {
       // $this->checkIsLogin(true);
        $this->skillId   = isset($_POST['skillId']) ? trim($_POST['skillId']) : '';
        $this->pageSize   = isset($_POST['size']) ? trim($_POST['size']) : '';
        $this->page   = isset($_POST['page']) ? trim($_POST['page']) : '';
        return true;
    }

    //获取评论列表
    public function getCommentList()
    {
        $res  = [];
        $data = [];
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $postData['skillId'] = $this->skillId;
        $postData['pageSize'] = $this->pageSize;
        $postData['page'] = $this->page;
        $arr =  $CertService->getCommentList($postData);
        //列表项
        foreach($arr as $key=>$value)
        {
            $data[$key]['uid'] = isset($value['uid']) ? $value['uid']:'';
            $data[$key]['nick'] = isset($value['nick']) ? $value['nick']:'';
            $data[$key]['pic'] = isset($value['pic']) ? $value['pic']:'';
            $data[$key]['star']= isset($value['star']) ? $value['star']:'';
            $data[$key]['comment']= isset($value['comment']) ? $value['comment']:'';
            $data[$key]['ctime'] = isset($value['ctime']) ? $value['ctime']:'';
            $data[$key]['tags'] =  isset($value['tags']) ? $value['tags']:[];
        }
        $res['list'] = $data;
        $res['total'] =  $CertService->getCommnetTotal($postData);
        return $res;
    }

    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->getCommentList();
        render_json($list);
    }

}
$obj = new commentList();
$obj->display();