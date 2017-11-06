<?php

/**
 * 
 * @author logoyoung
 * 
 * 文件上传类
 * 支持多文件上传
 * 支持多种格式
 * 
 * 
 *  */
class UpLoad
{

    private $path = ''; // 上传路径

    private $allowtype = array(
        'jpeg',
        'jpg',
        'png',
        'gif'
    );
    // 默认允许上传文件类型，可以通过函数添加类型
    private $sourcefilename = array(); // 文件原始文件名

    private $maxsize = 2000000; // 上限2M

    private $rand = true; // 默认上传随机名

    private $errcode; // 错误代码

    private $mode = 0755; // 创建路径权限
                          
     private $dirwrite = false; // 默认不允许创建新目录
    private $randdir = true; // 默认允许建立随机目录

    /**
     *构造
     * @param string $path            
     * @param string $conf            
     * @return boolean
     */
    public function __construct($path, $conf = NULL)
    {
        if (! $path)
            return false;
        $this->path = (substr($path, -1,1)=='/')?$path:$path.'/';
        if (is_array($conf))
            $this->conf($conf);
        return true;
    }

   /**
    * 配置函数 设置上传配置
    * @param array $conf
    * @return boolean  */
    public function conf($conf)
    {
        if (! $conf)
            return false;
        $conf = array_change_key_case($conf, CASE_LOWER);
        if (array_diff_key($conf, get_class_vars(get_class($this)))) {
            $err = "errorParams@FileUpLoad::conf(you have invalid params for conf)";
//            mylog($err);
            $this->errcode = -6001; // 上传配置错误
            return false;
        }
        foreach ($conf as $k => $v) {
            $this->$k = $v;
        }
        return true;
    }
    /**
     * 创建目录
     * @param string $path
     * @return boolean  */
    public function mkIndexPath($path)
    {
        if (is_dir($path))
            return true;
        if (! $this->randdir) {
            $err = "errorRandDir@FileUpLoad::checkPath( not start randdir)";
           // mylog($err);
            $this->errcode = -6002; // 未开启目录写权限
            return false;
        }
        if (! mkdir($path, $this->mode, true)) { // 允许建立新目录
            $err = "errorPath@FileUpLoad::checkPath(the path can not be written or invalid path)";
           // mylog($err);
            $this->errcode = -6003; //目录不可写或路径错误 
            return false;
        }
        return true;
    }
    /**
     * 检测文件类型
     * @param string $file
     * @return boolean  */
    public function checkType($file)
    {
        if (! in_array(strtolower(pathinfo($file,PATHINFO_EXTENSION)), $this->allowtype)) {
            $err = "errorType@FileUpLoad::checkType(the type of file you upload was invalid)";
            //mylog($err);
            $this->errcode = -4019; // 上传文件类型错误
            return false;
        }
        return true;
    }
    /**
     * 检测文件大小
     * @param long $size
     * @return boolean  */
    public function checkSize($size){
        if($size>$this->maxsize){
            $err = "errorSize@FileUpLoad::checkSize(the size of file you uploaded was over the range)";
           // mylog($err);
            $this->errcode = -4015; // 上传文件超过限定大小
            return false;
        }
        return true;
    }
    /**
     * 执行上传
     * @param array $fileobject
     * @return boolean|array  */
    public function exec($fileobject)
    {
        $fileobject = $this->getFileObj($fileobject);
        $back = array();
        foreach ($fileobject as $key=>$singlefileobj){
            if(!$addr=$this->upSingleFile($singlefileobj))          
                return false;
            $back[] = $addr;
        }
        return $back;
    }
    /**
     * 重组上传数据
     * @param array $fileobject
     * @return boolean|array */
    public function getFileObj($fileobject)
    {
        if (! $fileobject || ! is_array($fileobject)) {
            $err = "errorUploadFile@FileUpLoad::getFileObj(the FILE  you upload was error)";
           // mylog($err);
            $this->errcode = -6004; // 未获取到上传文件
            return false;
        }
        if (! is_array($fileobject['name']))
            return array(
                $fileobject
            );
        $obj = array();
        foreach ($fileobject['name'] as $k => $v) {
            // $this->sourcefilename[] = $v;//保存原始文件名
            if(!$v) continue;
            $obj[$k]['name'] = $v;
            $obj[$k]['type'] = $fileobject['type'][$k];
            $obj[$k]['tmp_name'] = $fileobject['tmp_name'][$k];
            $obj[$k]['error'] = $fileobject['error'][$k];
            $obj[$k]['size'] = $fileobject['size'][$k];
        }
        return $obj;
    }
    /**
     * 上传单个文件
     * @param array $singlefileobj
     * @return boolean|string  */
    public function upSingleFile($singlefileobj)
    {
        //检查文件格式
        if(!$this->checkType($singlefileobj['name']))
            return false;
        //检查文件大小
        if(!$this->checkSize($singlefileobj['size']))
            return false;
        //获取文件名和索引路径
        $fileName = $this->getUpLoadFileName($singlefileobj['name'], $indexDir);
        //创建索引目录
        if(!$this->mkIndexPath($this->path.$indexDir))
            return false;
        if(!move_uploaded_file($singlefileobj['tmp_name'],$this->path.$indexDir.$fileName)){
            $err = "uploadFiled@FileUpLoad::upSingleFile(move file filed )";
           // mylog($err);
            $this->errcode = -6005; // 拷贝临时文件出错
            return false;
        }
        return $indexDir.$fileName;
    }
    /**
     * 获取上传文件名和索引得目录
     * @param string $fileName
     * @param string* $indexDir
     * @return boolean|string  */
    public function getUpLoadFileName($fileName, &$indexDir)
    {
        $indexDir = '';
        if (! $fileName) {
            $err = "errorFileName@FileUpLoad::getUpLoadFileName(invalid filename)";
           // mylog($err);
            $this->errcode = -4017; // 错误文件名
            return false;
        }
        $randFileName = md5($fileName . time() . rand(100000000, 999999999));
        if($this->rand)
            $fileName = $randFileName . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
        if ($this->randdir) 
            $indexDir = $randFileName[0] . '/' . $randFileName[1] . '/';
         return $fileName;   
        // $basesDir = (sub_str($this->path,-1,1)=='/')?$this->path:$this->path.'/';
    }
    /**
     * 获取错误代码
     * @return number  */
    public function getErrCode(){
        return $this->errcode;
    }
}
