<?php
// +----------------------------------------------------------------------
// | Anchor Info
// +----------------------------------------------------------------------
// | Author: zwq
// +----------------------------------------------------------------------
namespace HP\Op;
use HP\Log\Log;
class Company extends \HP\Cache\Proxy{
    
    
    /**获取经纪公司id=>name 
     * @param $uids  用户id列表
     * @param $db
     */
    static function getCompanymap(){
        $db = D('Company');
        $map = $db->getField('id,name,type,status');
        return $map;
    }
    
    static function getCompangInfo($cids)
    {
        $db = D('company');
        if (is_array($cids)) {
            $cids = implode(',', $cids);
            $where['id'] = ['in',$cids];
        }
        $res = $db->field('id,name')->where($where)->getField('id,name,rate');
        return $res?$res:null;
    }
    
    static function getCompanyPeople($cids=null,$monthstart=null,$monthend=null)
    {
        $db = D('Anchor');
        if (is_array($cids)) {
            $cids = implode(',', $cids);
            $where['cid'] = ['in',$cids];
        }
        $res = $db
        ->where($where)
        ->group('cid')
        ->getField('cid,count(*) as  total');
        return $res?$res:array();
    }
    
    /**获取经纪公司收益
     * @param $uids
     * @param $month 月份  2017-02
     * @return array|bool
     */
    static function getCompanyIncome($cids=null,$monthstart=null,$monthend=null){

        $db = D('liveLength');
        if(is_array($cids)){
            $cids=implode(',', $cids);
            $where['cid'] =['in', $cids];
        }
        if($monthstart){
            $where['date'][] =['egt',$monthstart];
        }
        if($monthend){
            $where['date'][] =['elt',$monthend];
        }
        $res=$db->field("sum(bean) as bean,sum(coin) as coin")
        ->where($where)
        ->group('cid')
        ->getField('cid,sum(bean) as bean,sum(coin) as coin');
        return $res?$res:array();
    }

	function afterCompanyRateChange($data){
		$Dao=D('anchor');
//		$info=$Dao->field('uid')->where("cid=".$data['id'].'  and rate='.$data['brate'])->select();
		$info=$Dao->field('uid')->where("cid=".$data['id'])->select();
		if($info){
			$uids=array_column($info,'uid');
			addRateChangeRecord( array('uid'=>$data['id'],'before_rate'=>$data['brate'],'after_rate'=>$data['arate'],'adminid'=>$data['adminid'],'type'=>'2','desc'=>'经纪公司自身比率变动') );
			for($i=0,$k=count($uids);$i<$k;$i++){
				$list[$uids[$i]]=addRateChangeRecord( array('uid'=>$uids[$i],'before_rate'=>$data['brate'],'after_rate'=>$data['arate'],'adminid'=>$data['adminid'],'type'=>'2','desc'=>'经纪公司比率变动') );
			}
			$Dao->where("cid=".$data['id'].'  and uid in('.implode(',',$uids).')')->save(array('rate'=>$data['arate']));
			$res = publicRequist::outside_setRate($list,$data['arate'],'经纪公司比率变动');//通知财务系统
			if($res==1){
				updateNoticStatus(implode(',',$list));//是否通知到财务系统
				Log::statis(json_encode(array('list'=>$list,'res'=>json_encode($res))),'','companyRateSuccess');
			}else{
				Log::statis(json_encode(array('list'=>$list,'res'=>json_encode($res))),'','companyRateUnSuccess');
				unsuccessLogForFinanceBack('经纪公司比率改变 财务系统返回失败',array('financeBack'=>$res,'adminid'=>$data['adminid'],'rate'=>$data['arate'],'before'=>$data['brate'],'list'=>$list));
			}
			return $res;
		}
	}
	
	//获取非签约公司：工会和官方
	public static function getCompanyids02(){
	    $where['type'] = ['in',[0,2]];
	    $companyids = D('company')->where($where)->getField('id,name',true);
	    return $companyids;
	}
}