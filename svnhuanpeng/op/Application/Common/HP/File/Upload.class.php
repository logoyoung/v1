<?php
// +----------------------------------------------------------------------
//  自定义上传
// +----------------------------------------------------------------------
namespace HP\File;
class Upload {
    /**
     * 默认上传配置
     * @var array
     */
    private $config = array(
        'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'savePath'      =>  '', //保存路径
        'typeDir'      =>  '', //类别路径 zwq add
        'bucket'      =>  '', //随机生成目录 add
        'saveName'      =>  '', //上传文件命名
        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        'replace'       =>  true, //存在同名是否覆盖
        'type'          =>  0, //用于保存的文件业务类型
        'msg'           =>  '', //用于保存的文件其他信息
    );

    /**
     * 上传错误信息
     * @var string
     */
    private $error = ''; //上传错误信息

    /**
     * 构造方法，用于构造上传实例
     * @param array  $config 配置
     */
    public function __construct($config){
        /* 获取配置 */
        $this->config   =   array_merge($this->config,$config);
    }
    /**
     * 使用 $this->name 获取配置
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name) {
        return $this->config[$name];
    }
    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 上传单个文件
     * @param  array  $file 文件数组
     * @return array        上传成功后的文件信息
     */
    public function uploadOne($file=null){
        if($file || is_null($file)){
            $info = $this->upload($file);
            is_array($info) and $info = current($info);
        }else{
            $info = '上传失败';
        }
        return $info;
    }

    /**
     * 上传文件
     * @param 文件信息数组 $files ，通常是 $_FILES数组
     */
    public function upload($files=null) {
        if(empty($files)){
            $files  =   $_FILES;
        }
        if(empty($files)){
            $this->error = '没有上传的文件！';
            return false;
        }

        /* 逐个检测并上传文件 */
        $info    =  array();
        if(function_exists('finfo_open')){
            $finfo   =  finfo_open ( FILEINFO_MIME_TYPE );
        }
        // 对上传文件数组信息处理
        $files   =  $this->dealFiles($files);  
        foreach ($files as $key => $file) {
            $file['name']  = strip_tags($file['name']);
            if(!isset($file['key']))   $file['key']    =   $key;
            /* 通过扩展获取文件类型，可解决FLASH上传$FILES数组返回文件类型错误的问题 */
            if(isset($finfo)){
                $file['type']   =   finfo_file ( $finfo ,  $file['tmp_name'] );
            }

            /* 获取上传文件后缀，允许上传无后缀文件 */
            $file['ext']    =   pathinfo($file['name'], PATHINFO_EXTENSION);

            /* 文件上传检测 */
            if (!$this->check($file)){
                continue;
            }

            /* 获取文件hash */
            $file['md5'] = md5_file($file['tmp_name']);
            /* 生成保存文件名 */
            if (!$this->saveName) {
                $file['guid'] = $this->getName();
                $this->saveName = $file['guid'];
                if($this->saveExt){
                    $this->saveName .= '.'.$this->saveExt;
                }elseif($file['ext']){
                    $this->saveName .= '.'.$file['ext'];
                }
            }
            /* 对图像文件进行严格检测 */
            $ext = strtolower($file['ext']);
            if(in_array($ext, array('gif','jpg','jpeg','bmp','png','swf'))) {
                $imginfo = getimagesize($file['tmp_name']);
                if(empty($imginfo) || ($ext == 'gif' && empty($imginfo['bits']))){
                    $this->error = '非法图像文件！';
                    continue;
                }
            }
            $filename = $this->savePath . $this->saveName;
            /* 不覆盖同名文件 */ 
            if (!$this->replace && is_file($filename)) {
                $this->error = '存在同名文件' . $filename;
            }
            /* 移动文件 */
            if (move_uploaded_file($file['tmp_name'], $filename)) {
                $file['referer'] = strval($_SERVER['HTTP_REFERER']);
                $file['type'] = $this->type;
                $file['msg'] = $this->msg;
                $file['uuid'] = $this->uuid;
                $file['utime'] = $_SERVER['REQUEST_TIME'];
                $file['bucket'] = $this->bucket;
                $file['ip'] = get_client_ip(1);
                $file['picpath'] = $this->bucket.DIRECTORY_SEPARATOR.$this->saveName;
                $file['url'] = Read::getPublicUrl($this->bucket, $this->saveName);
                $info[] = $file;
            }else{
                $this->error = '文件上传保存错误！';
            }
        }
        if(isset($finfo)){
            finfo_close($finfo);
        }
        return $this->error?false:$info;
    }
    
    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files  上传的文件变量
     * @return array
     */
    private function dealFiles($files) {
        $fileArray  = array();
        $n          = 0;
        foreach ($files as $key=>$file){
            if(is_array($file['name'])) {
                $keys       =   array_keys($file);
                $count      =   count($file['name']);
                for ($i=0; $i<$count; $i++) {
                    $fileArray[$n]['key'] = $key;
                    foreach ($keys as $_key){
                        $fileArray[$n][$_key] = $file[$_key][$i];
                    }
                    $n++;
                }
            }else{
               $fileArray = $files;
               break;
            }
        }
       return $fileArray;
    }

    /**
     * 检查上传的文件
     * @param array $file 文件信息
     */
    private function check($file) {
        /* 文件上传失败，捕获错误代码 */
        if ($file['error']) {
            $this->error($file['error']);
            return false;
        }

        /* 无效上传 */
        if (empty($file['name'])){
            $this->error = '未知上传错误！';
        }

        /* 检查是否合法上传 */
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }

        /* 检查文件大小 */
        if (!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }

        /* 检查文件Mime类型 */
        //TODO:FLASH上传的文件获取到的mime类型都为application/octet-stream
        if (!$this->checkMime($file['type'])) {
            $this->error = '上传文件MIME类型不允许！';
            return false;
        }

        /* 检查文件后缀 */
        if (!$this->checkExt($file['ext'])) {
            $this->error = '上传文件后缀不允许';
            return false;
        }

        /* 通过检测 */
        return true;
    }


    /**
     * 获取错误代码信息
     * @param string $errorNo  错误号
     */
    private function error($errorNo) {
        switch ($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！';
                break;
            case 3:
                $this->error = '文件只有部分被上传！';
                break;
            case 4:
                $this->error = '没有文件被上传！';
                break;
            case 6:
                $this->error = '找不到临时文件夹！';
                break;
            case 7:
                $this->error = '文件写入失败！';
                break;
            default:
                $this->error = '未知上传错误！';
        }
    }

    /**
     * 检查文件大小是否合法
     * @param integer $size 数据
     */
    private function checkSize($size) {
        return !($size > $this->maxSize) || (0 == $this->maxSize);
    }

    /**
     * 检查上传的文件MIME类型是否合法
     * @param string $mime 数据
     */
    private function checkMime($mime) {
        return empty($this->config['mimes']) ? true : in_array(strtolower($mime), $this->mimes);
    }

    /**
     * 检查上传的文件后缀是否合法
     * @param string $ext 后缀
     */
    private function checkExt($ext) {
        return empty($this->config['exts']) ? true : in_array(strtolower($ext), $this->exts);
    }

    private function getName()
    {
        return strtolower(\HP\Util\StringTool::guid());
    }

}
