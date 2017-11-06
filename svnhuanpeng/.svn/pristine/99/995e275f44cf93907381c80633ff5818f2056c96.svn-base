<?php

// +----------------------------------------------------------------------
//  自定义文件类
// +----------------------------------------------------------------------

namespace HP\File;

class File
{
    const PIC_NEWS = 11;
    const PIC_PLEDGE = 12;
    const PIC_PACT = 13;
    const PIC_OTHER = 14;
    const PIC_BORROW = 15;
    const PIC_CREDITSHOP = 16;
    const PIC_CASHSHOP = 17;
    const FILE_NEWS = 21;
    const FILE_APK = 31;
    
    private $sourcefilename = array(); // 文件原始文件名
    private $maxsize = 2000000; // 上限2M
    private $rand = true; // 默认上传随机名
    private $errcode; // 错误代码
    private $mode = 0755; // 创建路径权限
    private $dirwrite = false; // 默认不允许创建新目录
    private $randdir = true; // 默认允许建立随机目录
    
    static public function getTypeHash()
    {
        return [
            self::PIC_NEWS=>[
                'name'=>'新闻图片',
            ],
            self::FILE_NEWS=>[
                'name'=>'新闻文件',
            ],
            self::PIC_PLEDGE=>[
                'name'=>'产品图片',
            ],
            self::PIC_OTHER=>[
                'name'=>'其他图片',
            ],
            0=>[
                'name'=>'其他',
            ],
        ];
    }
    

    static public function getTypeFromUrl($url)
    {
        $type=0;
        if(stripos($url,'/news/detail')!==false){
            $type = self::PIC_NEWS;
        }elseif(stripos($url,'/creditshop/detail')!==false){
            $type = self::PIC_NEWS;
        }elseif(stripos($url,'/cashshop/detail')!==false){
            $type = self::PIC_NEWS;
        }
        return $type;
    }

    
    static public function getAllowPicExt()
    {
        return [
            'png', 'jpg', 'jpeg', 'gif', 'bmp',
        ];
    }
    
    static public function getAllowFileExt()
    {
        return [
            'png', 'jpg', 'jpeg', 'gif', 'bmp',
            'flv', 'swf', 'mkv', 'avi', 'rm', 'rmvb', 'mpeg', 'mpg',
            'ogg', 'ogv', 'mov', 'wmv', 'mp4', 'webm', 'mp3', 'wav', 'mid',
            'rar', 'zip', 'tar', 'gz', '7z', 'bz2', 'cab', 'iso',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'md', 'xml',
            'apk'
        ];
    }

    static public function uploadFile($config=[])
    {
        $conf = ['exts'=>self::getAllowFileExt()];
        is_array($config) and $conf=array_merge($conf,$config);
        return self::upload($conf);
    }
    static public function uploadPic($config=[])
    {
        $conf = ['exts'=>self::getAllowPicExt()];
        is_array($config) and $conf=array_merge($conf,$config);
        return self::upload($conf);
    }
    
    static public function upload($config=[])
    {
        
        $config && is_array($config) or $config=array();
        $config['bucket'] or $config['bucket'] = self::mkBucket($config['typeDir']);
        $config['savePath'] or $config['savePath'] = C('FILE_UPLOAD_PATH').$config['bucket'].DIRECTORY_SEPARATOR;
        $obj = new Upload($config);
        return $obj;
    }


    /**
     * 创建目录 zwq 2017年5月12日
     * @param string $path
     * @return boolean  */
    public function mkBucket($typeDir)
    {
        $indexDir = '';
        $randFileName = md5( time() . rand(100000000, 999999999));
        $indexDir = $randFileName[0] . DIRECTORY_SEPARATOR . $randFileName[1];
        $path = C('FILE_UPLOAD_PATH');
        if(!is_writable($path)){
            E('文件上传目录不可写!');
        }
        $typeDir = $typeDir?$typeDir.DIRECTORY_SEPARATOR:'';
        $path .= DIRECTORY_SEPARATOR.$typeDir.$indexDir;
        if(!is_dir($path)){
            if(mkdir($path,0755,true)){//初始化bucket
            }else{
                E('BUCKET初始化失败!');
            }
        }
        return DIRECTORY_SEPARATOR.$typeDir.$indexDir;
    }

    
    static public function getBucketConfig()
    {
        $start = C('FILE_BUCKET_START');
        $end = C('FILE_BUCKET_end');
        
        is_numeric($start) or $start=10;
        is_numeric($end) or $end=10;
        return compact('start','end');
    }

    /**
     * 获取Bucket编号
     */
    static public function getBucket()
    {
        $config = self::getBucketConfig();
        $cache_key = 'bucket_stat';
        $stat=S($cache_key);
        if(empty($stat)||IS_CLI){
            $bcCount = $config['end']-$config['start']+1;
            $stat = array_combine(range($config['start'],$config['end']),array_fill(0,$bcCount,0));
            $res = D('fileIndex')->field('bucket,count(*) as n')->group('bucket')->select();
            foreach ($res as $item){
                $stat[$item['bucket']] = $item['n'];
            }
            asort($stat);
            $stat = array_slice(array_keys($stat),0,3);
            S($cache_key,$stat,60);
        }
        $bucket = $stat[array_rand($stat)];
        self::initBucket($bucket);
        return $bucket;
    }
    static public function checkBucket($str)
    {
        return is_numeric($str) && strlen($str)==2;
    }
    static public function initBucket($str)
    {
        $path = C('FILE_UPLOAD_PATH');
        if(!is_writable($path)){
            E('文件上传目录不可写!');
        }
        $path .= DIRECTORY_SEPARATOR.$str;
        if(is_writable($path) || !self::checkBucket($str)){
            return;
        }
        if(!is_dir($path)){
            //初始化bucket
            if(mkdir($path,0777)){
                Dao::inittable($str);
            }else{
                E('BUCKET初始化失败!');
            }
        }
    }
    
    
    static public function delByFid($fid)
    {
        $obj = Read::getByFid($fid);
        if(empty($obj)){
            return ['code'=>'error','msg'=>'文件对象不存在!'];
        }
        $savePath = C('FILE_UPLOAD_PATH');
        if(in_array($obj['ext'],self::getAllowPicExt())){
            $dao = D('fileThumb');
            $sign = $dao->where(['fid'=>$fid])->getField('id,sign',true);
            if($sign){
                foreach ($sign as $k=>$v){
                    $file = $savePath.Read::getFilePathByHash($obj,$v);
                    if(file_exists($file)){
                        if(!unlink($file)){
                            return ['code'=>'error','msg'=>'图片副本删除失败!'];
                        }
                    }
                }
                if($sign){
                    $dao->where(['id'=>['in',array_keys($sign)]])->delete();
                }
            }
        }
        
        $file = $savePath.Read::getFilePathByHash($obj);
        if(file_exists($file)){
            if(!unlink($file)){
                return ['code'=>'error','msg'=>'文件删除失败!'];
            }
        }
        D('fileIndex')->where(['id'=>$fid])->delete();
        Read::flushByFid($fid);
        return ['code'=>'success','msg'=>'文件删除成功!'];
    }
}
