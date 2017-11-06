<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/16
 * Time: 11:45
 */
namespace  Wcs\PersistentFops;
use Wcs;
use Wcs\Config;
use Wcs\PersistentFops\Fops;

class Fops_huanpeng extends Fops {

    const CMD_MERGE = 'avconcat/mp4/mode/1/moovToFront/1/';
    const CMD_TRANSCODE = 'avthumb/mp4/moovToFront/1';
    const CMD_POSTER = 'vframe/jpg/offset/';
    const POSTER_WIDTH = 1900;
    const POSTER_HEIGHT = 1069;
    const FORCE = 0;
    const SAPARET = 0;

    const VIDEO_EXT = 'mp4';
    const POSTER_EXT = 'jpg';
    const T_MERGE = 'MERGE';
    const T_POSTER = 'POSTER';
    const T_TRANSCODE = 'TRANSCODE';


    private $bucket = null;
    private $liveid = null;
    private $mergeKeys = null;
    private $duration = 0;
    private $primaryKey = '';

    private $saveDir = array(
        'DEV'=>array('v'=>'dev/v/','i'=>'dev/i/'),
        'PRO'=>array('v'=>'pro/v/','i'=>'pro/i/')
    );

    public function __construct($auth, $bucket)
    {
        parent::__construct($auth, $bucket);
    }
    public function merge($data,$notifyUrl){
        return $this->process($data,$notifyUrl,self::T_MERGE);
    }

    public function transcode($data,$notifyUrl){
        return $this->process($data,$notifyUrl,self::T_TRANSCODE);
    }

    public function poster($data,$notifyUrl){
        return $this->process($data,$notifyUrl,self::T_POSTER);
    }

    public function process($data,$notifyUrl,$type){
        $this->setInfo($data);
        $fops = $this->getFops($type);//var_dump($fops);
        //$keysBase64 = $this->getBase64Keys($this->keys);
        $ret = $this->exec($fops,$this->primaryKey,$notifyUrl);
        return $ret;
    }
    private function getBase64Keys($data){
        if(!$data)
            return false;
        if(is_string($data)){
            //$v = explode(':',$data);
            //$v = isset($v[1])?$v[1]:$v[0];
            $v = $this->getRealKey($data);
            return \Wcs\url_safe_base64_encode($v);
        }
        $keysBase64 = array();
        foreach ($data as $k=>$v){
            //$v = explode(':',$v);
            $v = $this->getRealKey($v);
            $keysBase64[$k] =  \Wcs\url_safe_base64_encode($v);
        }
        $keysBase64 = implode('/',$keysBase64);
        return $keysBase64;
    }

    private function getFops($ext){
        if($ext==self::T_MERGE)
            return self::CMD_MERGE.$this->mergeKeys.$this->getSaveFileName(self::VIDEO_EXT);
        else if($ext==self::T_POSTER) {
            $t = (int)($this->duration/2).'/w/'.self::POSTER_WIDTH.'/h/'.self::POSTER_HEIGHT;
            return self::CMD_POSTER . $t . $this->getSaveFileName(self::POSTER_EXT);
        }
        else if($ext==self::T_TRANSCODE)
            return self::CMD_TRANSCODE.$this->getSaveFileName(self::VIDEO_EXT);
        else
            return false;
    }
    private function getSaveFileName($ext){
        $i = ($ext == self::VIDEO_EXT)?'v':'i';
        $file = $this->bucket.":".$this->saveDir[$GLOBALS['env']][$i].$this->liveid.".".$ext;
        var_dump($file);
        return "|saveas/".\Wcs\url_safe_base64_encode($file);
    }

    private function setInfo($data){
        if(!$data || !is_array($data)||!$data['keys'])
            return false;
        $this->bucket = $data['bucket'];
        $this->liveid = $data['liveid'];
        $data['duration'] = (isset($data['duration'])&&$data['duration'])?(int)$data['duration']:$this->duration;
        $this->duration = $data['duration'];

        if(count($data['keys'])==1) {
            $realKey = $this->getRealKey($data['keys'][0]);
            $this->primaryKey = \Wcs\url_safe_base64_encode($realKey);//base64
            return true;
        }
        $realKey = $this->getRealKey(array_shift($data['keys']));
        $this->primaryKey = \Wcs\url_safe_base64_encode($realKey);//base64
        $this->mergeKeys = $this->getBase64Keys($data['keys']);
        return true;
    }

    private function getRealKey($str){
        $key = explode(':',$str);
        $key = isset($key[1])?$key[1]:$key[0];
        return $key;
    }
}