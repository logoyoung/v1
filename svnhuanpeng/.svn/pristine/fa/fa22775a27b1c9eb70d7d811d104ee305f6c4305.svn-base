<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/12
 * Time: 9:29
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\common\UploadImagesCommon;

class uploadAppealPic extends ApiCommon {

    const APPEAL_IMAGE_DIR = 'dueAppeal';

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    public function upload() {
        $uploadService = new UploadImagesCommon();
        $res = $uploadService->uploadImage($this->uid, self::APPEAL_IMAGE_DIR);
        return $res;
    }

    public function display() {
        //获取资质列表
        $list = $this->upload();
        render_json($list);
    }

}

$obj = new uploadAppealPic();
$obj->display();
