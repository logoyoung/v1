<?php

namespace Admin\Controller;
use HP\File;
use HP\Util;
class FileController extends BaseController
{
    public $pageSize=20;
    protected function _access()
    {
        return [
            'up'=>self::ACCESS_LOGIN,
            'upfile'=>self::ACCESS_LOGIN,
			'upinformation'=>self::ACCESS_LOGIN,
        	'upgift'=>self::ACCESS_LOGIN,
            'rename'=>self::ACCESS_LOGIN,
            'addwater'=>self::ACCESS_LOGIN,
            'del'=>['index'],
            'detail'=>['index'],
			'examine'=>self::ACCESS_LOGIN,
        ];
    }
    public function up(){
        if($_FILES){
            $type=\HP\File\File::PIC_OTHER;
            $obj = \HP\File\File::UploadPic(['typeDir'=>C('GAME_I_DIR'),'type'=>$type,'uuid'=>\HP\Op\Admin::getUid()]);
            $data = $obj->uploadOne();
            return $this->ajaxReturn($data);
        }
        $this->display();
    }
	public function examine(){
		if($_FILES){
			$type=\HP\File\File::PIC_OTHER;
			$obj = \HP\File\File::UploadPic(['typeDir'=>C('EXAMINE_DIR'),'type'=>$type,'uuid'=>\HP\Op\Admin::getUid()]);
			$data = $obj->uploadOne();
			return $this->ajaxReturn($data);
		}
		$this->display();
	}
    public function upfile(){
        if($_FILES){
            $type=\HP\File\File::FILE_APK;
            $obj = \HP\File\File::UploadFile(['saveName'=>$_FILES['file']['name'],'bucket'=>C('APK_DIR'),'type'=>$type,'uuid'=>\HP\Op\Admin::getUid()]);
            $data = $obj->uploadOne();
            return $this->ajaxReturn($data);
        }
        $this->display();
    }

	/**
	 * 资讯图片上传
	 */
	public function upinformation(){
		if($_FILES){
			$type=\HP\File\File::PIC_OTHER;
			$obj = \HP\File\File::UploadPic(['typeDir'=>C('INFORMATION_DIR'),'type'=>$type,'uuid'=>\HP\Op\Admin::getUid()]);
			$data = $obj->uploadOne();
			return $this->ajaxReturn($data);
		}
		$this->display();
	}
	
	/**
	 * 礼物图片上传
	 */
	public function upgift(){
		if($_FILES){
			$type=\HP\File\File::PIC_OTHER;
			$obj = \HP\File\File::UploadPic(['typeDir'=>C('GIFT_DIR'),'type'=>$type,'uuid'=>\HP\Op\Admin::getUid()]);
			$data = $obj->uploadOne();
			return $this->ajaxReturn($data);
		}
		$this->display();
	}

}
