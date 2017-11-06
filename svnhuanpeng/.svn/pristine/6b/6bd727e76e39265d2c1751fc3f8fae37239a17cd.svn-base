<?php

namespace Admin\Controller;
use HP\File;
use HP\Util;
class UtilController extends BaseController
{
    protected function _access()
    {
        return self::ACCESS_LOGIN;
    }
    public function ueditor()
    {
        switch (I('get.action')) {
           case 'config':
                return $this->ueditor_conf();

            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                return $this->ueditor_upload_file();
            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
                return $this->ueditor_upload_pic();
            /* 列出文件 */
            case 'listfile':
            /* 列出图片 */
            case 'listimage':
                return $this->ueditor_list();

            /* 抓取远程文件 */
            case 'catchimage':
                break;

            default:
                E();
        }
    }
    
    private function ueditor_conf()
    {
        $conf=[
            /* 上传图片配置项 */
            "imageActionName"=> "uploadimage", /* 执行上传图片的action名称 */
            "imageFieldName"=> "upfile", /* 提交的图片表单名称 */
            "imageMaxSize"=> 10240000, /* 上传大小限制，单位B */
            "imageAllowFiles"=> [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上传图片格式显示 */
            "imageCompressEnable"=> true, /* 是否压缩图片,默认是true */
            "imageCompressBorder"=> 1600, /* 图片压缩最长边限制 */
            "imageInsertAlign"=> "none", /* 插入的图片浮动方式 */
            "imageUrlPrefix"=> "", /* 图片访问路径前缀 */
            "imagePathFormat"=> "", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            
            
            /* 涂鸦图片上传配置项 */
            "scrawlActionName"=> "uploadscrawl", /* 执行上传涂鸦的action名称 */
            "scrawlFieldName"=> "upfile", /* 提交的图片表单名称 */
            "scrawlPathFormat"=> "", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "scrawlMaxSize"=> 2048000, /* 上传大小限制，单位B */
            "scrawlUrlPrefix"=> "", /* 图片访问路径前缀 */
            "scrawlInsertAlign"=> "none",
            
            
            /* 列出指定目录下的图片 */
            "imageManagerActionName"=> "listimage", /* 执行图片管理的action名称 */
            "imageManagerListSize"=> 100, /* 每次列出文件数量 */
            "imageManagerUrlPrefix"=> "", /* 图片访问路径前缀 */
            "imageManagerInsertAlign"=> "none", /* 插入的图片浮动方式 */
            "imageManagerAllowFiles"=> [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件类型 */
            
            
            /* 上传文件配置 */
            "fileActionName"=> "uploadfile", /* controller里,执行上传视频的action名称 */
            "fileFieldName"=> "upfile", /* 提交的文件表单名称 */
            "filePathFormat"=> "", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "fileUrlPrefix"=> "", /* 文件访问路径前缀 */
            "fileMaxSize"=> 51200000, /* 上传大小限制，单位B，默认50MB */
            "fileAllowFiles"=> File\File::getAllowFileExt(), /* 上传文件格式显示 */
            
            
            /* 列出指定目录下的文件 */
            "fileManagerActionName"=> "listfile", /* 执行图片管理的action名称 */
            "fileManagerListSize"=> 100, /* 每次列出文件数量 */
            "fileManagerAllowFiles"=> File\File::getAllowFileExt(), /* 列出的文件类型 */
        ]; 
        return $this->ajaxReturn($conf);
    }
    
    private function ueditor_upload_pic()
    {

        $obj = \HP\File\File::UploadPic(['typeDir'=>C(INFORMATION_DIR),'type'=>\HP\File\File::PIC_OTHER,'uuid'=>\HP\Op\Admin::getUid()]);
        $data = $obj->uploadOne();
        $data['state'] = 'SUCCESS';
        $error = $obj->getError();
        $this->ajaxReturn($error?['state'=>$error]:$data);
    }
    private function ueditor_upload_file()
    {
		$obj = \HP\File\File::UploadPic(['typeDir'=>C(INFORMATION_DIR),'type'=>\HP\File\File::PIC_OTHER,'uuid'=>\HP\Op\Admin::getUid()]);
        $data = $obj->uploadOne();
        $data['state'] = 'SUCCESS';
        $error = $obj->getError();
        $this->ajaxReturn($error?['state'=>$error]:$data);
    }
    
    private function ueditor_list()
    {
        $type = File\File::getTypeFromUrl($_SERVER['HTTP_REFERER']);
        I('get.action')=='listfile' and $type+=10;
        $index = I('get.start',0);
        $size = I('get.size',20);
        $dao = D('fileIndex');
        $data = $dao->page($index,$size)->where('type=%d',$type)->order('id desc')->select();
        $list = array();
        if($data){
            foreach ($data as $k=>$v){
                $list[] = [
                    'url'=>File\Read::getPublicUrl($v['bucket'],$v['guid'].'.'.$v['ext']),
                    'name'=>date('m-d H:i:s',$v['utime']).'.'.$v['ext'],
                ];
            }
        }
        $this->ajaxReturn(['state'=>'SUCCESS','list'=>$list]);
    }
    
}
