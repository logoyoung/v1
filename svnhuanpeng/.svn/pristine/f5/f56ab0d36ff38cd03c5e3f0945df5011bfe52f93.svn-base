<?php

// +----------------------------------------------------------------------
//  文件读取类
// +----------------------------------------------------------------------

namespace HP\File;

class Read extends \HP\Cache\Proxy
{
    const CACHE_TIME = 86400;
    
    static public function guid2fid($ids)
    {
        return self::QueryCache(\HP\Cache\CacheKey::FILE_G2F,self::CACHE_TIME,$ids,__CLASS__.'::guid2fidFromDb');
    }
    static public function guid2fidFromDb($ids){
        return D('fileIndex')->where(['guid'=>['in',$ids]])->getField('guid,id');
    }
    
    static public function flushByFid($ids)
    {
        return self::QueryCache(\HP\Cache\CacheKey::FILE_FID,self::CACHE_TIME,$ids,__CLASS__.'::getByFidFromDb',[self::CACHE_UPDATE=>true]);
    }
    static public function getByFid($ids)
    {
        return self::QueryCache(\HP\Cache\CacheKey::FILE_FID,self::CACHE_TIME,$ids,__CLASS__.'::getByFidFromDb');
    }
    static public function getByFidFromDb($ids){
        $data = D('fileIndex')->where(['id'=>['in',$ids]])->select(['index'=>'id']);
        foreach ($data as $item){
            $buffer_bucket[$item['bucket']][] = $item['bid'];
            $buffer_hash[$item['bucket'].'-'.$item['bid']] = $item['id'];
        }
        
        foreach ($buffer_bucket as $k=>$v){
            $extData = Dao::gettable($k)->where(['id'=>['in',$v]])->field('id,name,size,watermark')->select(['index'=>'id']);
            foreach ($extData as $item){
                if($hasHkey = $buffer_hash[$k.'-'.$item['id']]){
                    $data[$hasHkey] and $data[$hasHkey] += $item;
                }
            }
        }
        return $data;
    }

    static public function getPublicUrl($bucket,$filename)
    {
        
        switch (C('FILE_PUBLIC_MODE')){
            default :
                $url = $GLOBALS['env-def'][$GLOBALS['env']]['img-url'].$bucket.'/'.$filename;
        }
        return $url;
    }
    
    
    static public function getFidByPulicUrl($url){
        return self::guid2fid(strrev(substr(strstr(strrev($url),'/',true),-36)));
    }

    static public function getPublicUrlByFid($fid,$sign=null)
    {
        $obj = self::getByFid($fid);
        return $obj?self::getPublicUrlByHash($obj,$sign):null;
    }
    static public function getPublicUrlByHash(array $obj,$sign=null)
    {
        $filename = $obj['guid'];
        if($sign){
            $filename = self::FileRealnameEncode($obj['guid'],$sign).$sign;
        }
        if($sign=='2' && $obj['watermark']=='0'){
            $filename = $obj['guid'];
        }
        return self::getPublicUrl($obj['bucket'],$filename.'.'.$obj['ext']);
    }
    static public function getFilePathByFid($fid,$sign=null)
    {
        $obj = self::getByFid($fid);
        return $obj?self::getFilePathByHash($obj,$sign):null;
    }
    static public function getFilePathByHash(array $obj,$sign=null)
    {
        $filename = $obj['guid'];
        if($sign){
            $filename = self::FileRealnameEncode($obj['guid'],$sign).$sign;
        }
        if($sign=='2' && $obj['watermark']=='0'){
            $filename = $obj['guid'];
        }
        return $obj['bucket'].'/'.$filename.'.'.$obj['ext'];
    }
    
    static public function FileRealnameEncode($guid,$sign){
        if(empty($sign))return $guid;
        
        $md5 = strtr($guid,['-'=>'']);
        $arr = str_split(strrev($md5));
        $limitStr = array_shift($arr);
        $limit = ($sign.ord($limitStr))%6;
        $arr = array_values($arr);
        //加密
        for($i=0;$i<12;$i++){
            $i2=13+$limit+$i;
            if(!($i%2)){
                $tmp = $arr[$i];
                $arr[$i] = $arr[$i2];
                $arr[$i2] = $tmp;
            }
        }
        $charid = join($arr,'').$limitStr;
        return substr($charid, 0, 8) . '-'
                    . substr($charid, 8, 4) . '-'
                    . substr($charid, 12, 4) . '-'
                    . substr($charid, 16, 4) . '-'
                    . substr($charid, 20, 12);
    }
    
    static public function FileRealnameDecode($guid,$sign){
        if(empty($sign))return $guid;
        
        $md5 = strtr($guid,['-'=>'']);
        $arr = str_split($md5);
        $limitStr = array_pop($arr);
        $limit = ($sign.ord($limitStr))%6;
        $arr = array_values($arr);
        //加密
        for($i=0;$i<12;$i++){
            $i2=13+$limit+$i;
            if(!($i%2)){
                $tmp = $arr[$i];
                $arr[$i] = $arr[$i2];
                $arr[$i2] = $tmp;
            }
        }
        $charid = strrev(join($arr,'')).$limitStr;
        return substr($charid, 0, 8) . '-'
                    . substr($charid, 8, 4) . '-'
                    . substr($charid, 12, 4) . '-'
                    . substr($charid, 16, 4) . '-'
                    . substr($charid, 20, 12);
    }
}
