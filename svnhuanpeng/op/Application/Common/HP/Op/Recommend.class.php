<?php
// +----------------------------------------------------------------------
// | 首页推荐类
// +----------------------------------------------------------------------
// | Author: zwq 
// +----------------------------------------------------------------------
namespace HP\Op;
class Recommend extends \HP\Cache\Proxy{
    
    
    /**获取已推荐数组
     * @param $client  1app推荐，2web推荐
     */
   static function getInfo($client=2){
       $uids = [];
       $dao = D('RecommendLive');
       $results = $dao->where(['client'=>$client])->find();
       if( $results && $results['list'] ){
           $uids = explode(',', $results['list']);
       }
       return $uids;
   }
    
}