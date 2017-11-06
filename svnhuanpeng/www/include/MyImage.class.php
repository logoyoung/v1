<?php

/**
 * 缩放｜裁剪
 * @author logoyoung
 *  */
class MyImage
{

    /**
     */
    public $image; // 图片对象

    public $sourcefile; // 原始文件名

    public $sourcewidth; // 图片宽度

    public $sourceheight; // 高度

    public $sourcetype; // 图片格式

    public $errcode; // 错误代码
    //格式 
    public $type = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );
    public $logfun = 'mylog';//日志纪录
    /**
     * 构造函数
     * */
    public function __construct($fileName)
    {
        if (! $this->sourcetype = $this->getImageType($fileName))
            return false;
        $this->sourcefile = $fileName;
        list ($this->sourcewidth, $this->sourceheight) = getimagesize($fileName); // var_dump($this->sourcewidth);
        $this->image = $this->createImage($fileName); // var_dump($this->image);
        if (! $this->image)
            return false; // var_dump($this->image);
        return true;
    }
    /**
     * 析构函数
     *   */
    public function __destruct()
    {
        if ($this->image)
            imagedestroy($this->image);
    }
    /**
     * 记录日志
     * @param string $errStr
     * @param number $errCode
     * @param boolean $return
     * @return boolean  */
    public function log($errStr, $errCode, $return = false)
    {   if(function_exists($this->logfun))
            $this->logfun($errStr);          
        $this->errcode = $errCode;
        return $return;
    }
    /**
     * 获取图片类型
     * @param string $fileName
     * @return Ambigous <boolean, boolean>|string  */
    public function getImageType($fileName)
    {
        if (! file_exists($fileName))
            return $this->log('errfile@MyImage::getImageType(file not existed)', -6004);
        $type = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (! in_array($type, $this->type))
            return $this->log('errtype@MyImage::getImageType(file type error)', -4019);
        return $type;
    }
    /**
     * 创建画布
     * @param string $fileName
     * @return resource|boolean  */
    public function createImage($fileName = NULL)
    {
        if (! $fileName)
            return imagecreatetruecolor($this->sourcewidth, $this->sourceheight);
        $type = $this->getImageType($fileName);
        if (! $type)
            return false;
        if ($type == 'jpg'){
              $type = 'jpeg';
        }
          
        $fun = 'imagecreatefrom' . $type; // var_dump($fun($fileName));
        return $fun($fileName);
    }
    /**
     * 缩放图片
     * @param string $width   宽
     * @param string $height  高
     * @param string $saveFile 另存路径
     * @return Ambigous <boolean, boolean>|boolean  */
    public function thumb($width = NULL, $height = NULL, $saveFile = '')
    {
        $dstFile = $saveFile ? $saveFile : $this->sourcefile;
        $width = $width ? $width : $this->sourcewidth;
        $height = $height ? $height : $this->sourceheight;
        // 当不是等比例缩放进行比例调整
        $rate = max(array(
            $width / $this->sourcewidth,
            $height / $this->sourceheight
        ));
        $width = (int) ($this->sourcewidth * $rate);
        $height = (int) ($this->sourceheight * $rate);
        // echo $width;
        $dstImage = imagecreatetruecolor($width, $height); // 创建目标画布
                                                           // 复制
                                                           // var_dump($this->image);
        $r = imagecopyresampled($dstImage, $this->image, 0, 0, 0, 0, $width, $height, $this->sourcewidth, $this->sourceheight);
        if (! $r)
            return $this->log('errThumb@MyImage::thumb(failed when you copied the imageobject you created)', -6006);
        $leve = 9;
        if ($this->sourcetype == 'jpg'){
            $fun = 'imagejpeg';
            $level = 100;
        }
        else
            $fun = 'image' . $this->sourcetype; // var_dump($fun);                                         // 将画布生成图片
        $fun($dstImage, $dstFile, $leve);
        // 回收画布
        imagedestroy($dstImage);
        return true;
    }
    /**
     * 裁剪
     * @param number $x 起始坐标
     * @param number $y 起始坐标
     * @param string $width  裁剪宽
     * @param string $height 裁剪高
     * @param string $savaFile 另存路径
     * @return Ambigous <boolean, boolean>|boolean  */
    public function cut($x = 0, $y = 0, $width = NULL, $height = NULL, $savaFile = '')
    {
        $dstFile = $savaFile ? $savaFile : $this->sourcefile;
        $width = $width ? $width : $this->sourcewidth;
        $height = $height ? $height : $this->sourceheight;
        $dstImage = imagecreatetruecolor($width, $height); // var_dump($this->image);
        $r = imagecopyresampled($dstImage, $this->image, 0, 0, $x, $y, $this->sourcewidth, $this->sourceheight, $width, $height);
        if (! $r)
            return $this->log('errCut@MyImage::cut(failed when you copied the imageobject you created)', -6007);
        $leve = 9;
        if ($this->sourcetype == 'jpg'){
            $fun = 'imagejpeg';
            $level = 100;
        }
        else
            $fun = 'image' . $this->sourcetype; // var_dump($fun);
                                                  // 将画布生成图片
        $fun($dstImage, $dstFile, $leve);
        imagedestroy($dstImage);
        return true;
    }
    /**
     * 旋转
     * @param number $degrees//旋转角度
     * @param string $saveFile//另存路径 没有时默认原图路径
     * @return boolean|Ambigous <boolean, boolean>  */
    public function rotate($degrees, $saveFile=''){
        if(!$degrees)
            return true;
        $rotateImage = imagerotate($this->image, $degrees, 0); 
        if(!$rotateImage)
            return $this->log('errRotate@MyImage::rotate(failed when you rotaet)', -6008);
        if($this->sourcetype == 'jpg')
            $fun = 'imagejpeg';
        else 
            $fun = 'image'.$this->sourcetype;
        $targetFile = $saveFile?$saveFile:$this->sourcefile;
        
        return $fun($rotateImage,$targetFile);
    }
}