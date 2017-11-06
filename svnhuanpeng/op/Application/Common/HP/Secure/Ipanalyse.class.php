<?php
// +----------------------------------------------------------------------
// IP来源分析
// 监控IP访问
// +----------------------------------------------------------------------

namespace HP\Secure;

class Ipanalyse
{
    const TTL = 2592000;//30天

    /**
     * 参数说明:
     * [
     *      ip=>[
     *              referer=''
     *              uaid=>'',
     *          ]
     * ]
     */
    static public function save($data){
        if(empty($data) || !is_array($data))return;
        $get_date = get_date();
        foreach ($data as $k=>$v){
            $k = ip2long($k);
            $v['ip'] = $k;
            $v['at'] = $get_date;
            $addData[$k] = $v;
        }
        $dao = D('ipAnalyse');
        $res = $dao->where(['ip'=>['in',array_keys($addData)],'at'=>['gt',get_date(time()-self::TTL)]])->getField('ip',true);
        if($res){
            foreach ($res as $v){
                unset($addData[$v]);
            }
        }
        $addData and $dao->addAll(array_values($addData));
    }
}
