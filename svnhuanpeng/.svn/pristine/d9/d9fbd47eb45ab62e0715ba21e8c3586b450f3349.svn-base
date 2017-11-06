<?php

// +----------------------------------------------------------------------
// | Cache Proxy
// +----------------------------------------------------------------------

namespace HP\Cache;

abstract class Proxy
{
    const CACHE_UPDATE = 'updatecache';
    const CACHE_ONLY = 'onlyreadcache';
    //如果该元素不存在也缓存
    const CACHE_NULL = 'nullcache';
    const CACHE_NULL_SIGN = '#nil*';

    /**
     * 命中缓存并获取数据
     * @param type 缓存KEY
     * @param type 缓存时间
     * @param type String||function 获取数据的方法
     * @param array 其他参数 CACHE_UPDATE更新缓存 CACHE_ONLY只从缓存中读
     * @return 数据
     */
    public static function QueryCacheScalar($cacheKey,$cacheTime,$getFromDB,array $option=[],$param=[])
    {
        $option = array_merge([
            self::CACHE_UPDATE=>false||self::getDebug(),
            self::CACHE_ONLY=>false,
            self::CACHE_NULL=>true,
        ],$option);
        if(empty($cacheTime) || empty($cacheKey)){
            return null;
        }
        
        $option[self::CACHE_UPDATE] or $data=S($cacheKey);
        if(!$data and !$option[self::CACHE_ONLY]){
            if(is_scalar($getFromDB)){
                $data = call_user_func_array($getFromDB,[$param]);
            }elseif(is_object($getFromDB)){
                $data = $getFromDB($param);
            }
            S($cacheKey,$data,['expire'=>$cacheTime]);
        }
        return $data;
    }
    
    /**
     * 淘汰缓存方法 
     * 不建议使用 可以用更新缓存
     * 更新缓存 QueryCacheScalar方法传入updatecache参数来代替
     * @param type 缓存key
     */
    public static function flushCacheScalar($cacheKey)
    {
        return S($cacheKey,null);
    }

    /**
     * 命中缓存并获取数据
     * @param type 缓存前缀
     * @param type 缓存时间
     * @param type 缓存KEY
     * @param type String||function 从库里获取数据的方法
     * @param array 其他参数 CACHE_UPDATE更新缓存 CACHE_ONLY只从缓存中读
     * @return 数据
     */
    public static function QueryCache($cacheKeyPrefix,$cacheTime,$findBy,$getFromDB,array $option=[])
    {
        $option = array_merge([
            self::CACHE_UPDATE=>false||self::getDebug(),
            self::CACHE_ONLY=>false,
            self::CACHE_NULL=>true,
        ],$option);
        if(empty($cacheKeyPrefix) || empty($cacheTime) || empty($findBy)){
            return null;
        }
        if(is_scalar($findBy)){
            $findBy = [$findBy=>$findBy];
            $is_scalar = true;
        }elseif(is_array($findBy)){
            $findBy = array_combine($findBy,$findBy);
        }else{
            return null;
        }
        if($option[self::CACHE_UPDATE]){
            $miss = $findBy;
        }else{
            $miss = [];
            foreach ($findBy as $v){
                if($item=S($cacheKeyPrefix.$v)){
                    //表示此key为空
                    if($item===self::CACHE_NULL_SIGN){
                        unset($findBy[$v]);
                    }else{
                        $findBy[$v] = $item;
                    }
                }else{
                    $miss[$v] = $v;
                }
            }
        }
        
        if($miss and !$option[self::CACHE_ONLY]){
            if(is_scalar($getFromDB)){
                $data = call_user_func_array($getFromDB,[$miss]);
            }elseif(is_object($getFromDB)){
                $data = $getFromDB($miss);
            }
            if($data){
                foreach ($data as $key=>$item){
                    $findBy[$key] = $item;
                    S($cacheKeyPrefix.$key,$item,['expire'=>$cacheTime]);
                    unset($miss[$key]);
                }
            }
        }
        if($miss){
            foreach ($miss as $item){
                //缓存空开启时才获取
                $option[self::CACHE_NULL] && S($cacheKeyPrefix.$item,self::CACHE_NULL_SIGN,['expire'=>$cacheTime]);
                unset($findBy[$item]);
            }
        }
        return $is_scalar?reset($findBy):$findBy;
    }
    /**
     * 淘汰缓存方法 
     * 不建议使用 可以用更新缓存
     * 更新缓存 QueryCache方法传入updatecache参数来代替
     * @param type 缓存前缀
     * @param type 缓存key
     */
    public static function flushCache($cacheKeyPrefix,$keys)
    {
        if(empty($keys)){
            return null;
        }elseif(is_scalar($keys)){
            $keys = [$keys];
        }elseif(!is_array($keys)){
            return null;
        }
        foreach ($keys as $key){
            S($cacheKeyPrefix.$key,null);
        }
    }
    
    public static function getDebug()
    {
        return I('get.clearcache12555')=='12555';
    }
}