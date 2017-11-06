<?php
/**
 * 验证码类
 * @author logoyoung
 *  */
class Vcode{
	public $checkCode;//验证码

	private $width;//宽
    private $height;//高
    private $codeNum;//验证码数量
    private $disturbNum;//干扰点数量
    private $image;//
    private $code = "1234567890qwertyuiopasdfghjklzxcvbnm";//随机
    private $minFont = 10;//字体最小
    private $maxFont = 11;//字体最大
    private $imageType = array('jpg','jpeg','png','gif','bmp');//格式
    /**
     * 构造函数初始化参数
     * @param number $width
     * @param number $height
     * @param number $codenum
     * @return void  */
    function __construct($width=75,$height=30,$codenum=4){
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codenum;
        $disturbNum = floor($width*$height/15);//根据面积设置干扰
        if($disturbNum>240-$codenum)
            $this->disturbNum = 240-$codenum;
        else 
            $this->disturbNum = $disturbNum;
        $this->createCode();
        //$this->image = imagecreatetruecolor($width, $height);
        $this->createImage();
        $this->setCode();
        $this->setDisturb();
        
    }
    /**
     * 生成验证码  */
    private function createCode(){
        for($i=0;$i<$this->codeNum;$i++){
            $c = $this->code[rand(0,strlen($this->code)-1)];
            $this->checkCode[]= strtolower($c); 
            //echo $c,"--";
        }   
    }
    /**
     *  创建背景图片 */
    private function createImage(){
        $this->image = imagecreatetruecolor($this->width, $this->height);
        //$bgColor = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        $bgColor = imagecolorallocate($this->image, rand(230,240), rand(230,240), rand(230,240));//颜色浅
        imagefill($this->image, 0, 0, $bgColor);
    }
    /**
     * 添加干扰像素点  */
    private function setDisturb(){
        for($i=0;$i<$this->disturbNum;$i++){
            $color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
            imagesetpixel($this->image, rand(0,$this->width), rand(0,$this->height), $color);
        }
    }
    /**
     * 添加验证码
     * @return void  */
    private function setCode(){
        $len = 10;
        foreach ($this->checkCode as $val){
        $color = imagecolorallocate($this->image, rand(0,200), rand(0,200), rand(0,200));//颜色稍微深些
        if(rand(-1,1)>0)  
            $val = strtoupper($val);
        imagechar($this->image, rand($this->minFont,$this->maxFont), $len, $this->height/4, $val, $color);
        $len+=$this->width/$this->codeNum;
        }
    }
    /**
     * 输出图片
     * @param string $path
     * @return void  */
    public function outputImage($path=NULL){
        if(imagetypes()&IMG_GIF){
            header("Content-type:image/gif");
            imagegif($this->image,$path);
        }
        else if(imagetypes()&IMG_JPG){
            header("Content-type:image/jpg");
            imagejpeg($this->image,$path);
        }
        else if(imagetypes()&IMG_PNG){
            header("Content-type:image/png");
            imagepng($this->image,$path);
        }
        else if(imagetypes()&IMG_WBMP){
            header("Content-type:image/vnd.wap.bmp");
            imagewbmp($this->image,$path); 
        }
        else die("缺少GD库");
    }
    /**
     * 重写便于输出
     * @return string
     *   */
    function __toString(){
        $this->outputImage();
        return '';
    }
    /**
     * 释放资源
     * @return void  */
    function __destruct(){
        imagedestroy($this->image);
    }
}
/***************** 测试****************************/
    //$testCode = new vcode();
    //echo $testCode; 