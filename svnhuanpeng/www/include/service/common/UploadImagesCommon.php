<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/14
 * Time: 14:42
 */

namespace service\common;

use lib\due\UpLoad;

class UploadImagesCommon {

    /**
     * 获取图片服务器url
     * $uid @用户uid
     * $imageSubDir @自定义图片子目录
     */
    public static function getImageDomainUrl() {
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $imgDomain = DOMAIN_PROTOCOL . $conf['domain-img'] . '/';
        return $imgDomain;
    }

    /**
     * 设置上传图片路径规则
     * $uid @用户uid
     * $imageSubDir @自定义图片子目录
     */
    public static function setImagePath($uid, $imageSubDir = 'temp') {
        $seed = 5000;
        $dir1 = intval($uid / $seed);
        $imgDirRule = $dir1 . '/' . $uid . '/';
        $imagePath = $imageSubDir . '/' . $imgDirRule;
        return $imagePath;
    }

    /**
     * 上传图片
     * $uid @用户的uid
     * $imageSubDir 文件存放的子目录
     */
    public static function uploadImage($uid, $imageSubDir) {
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $filePath = $conf['img-dir'] . '/' . self::setImagePath($uid, $imageSubDir);
        $upload = new UpLoad($filePath, array('dirwrite' => false));
        $dir = $upload->exec($_FILES['file']); //上传
        if ($errcode = $upload->getErrCode()) {
            $res['code'] = $errcode;
            $res['desc'] = errorDesc($errcode);
            render_error_json($res);
        }
        $imgUrl = self::getImageDomainUrl() . self::setImagePath($uid, $imageSubDir) . $dir[0];
        $res = ['img' => $imgUrl, 'imgName' => self::setImagePath($uid, $imageSubDir) . $dir[0]];
        return $res;
    }

}
