<?php
// +----------------------------------------------------------------------
// | 工资 Info
// +----------------------------------------------------------------------
namespace HP\Op;
class Wages extends \HP\Cache\Proxy{
    
    const LOG_TYPE_OP_0 = 0;//系统
    const LOG_TYPE_OP_1 = 1;//运营初审
    const LOG_TYPE_OP_2 = 2;//运营复审
    const LOG_TYPE_FINANCE_CHECK = 3;//财务审核
    const LOG_TYPE_FINANCE = 4;//财务付款
    const LOG_TYPE_PAY = 4;//财务汇款
    const LOG_TYPE_EDIT = 5;//设置银行卡
    const LOG_TYPE_INIT = 10;//提交复审
    const LOG_TYPE_COMIT_MANAGER = 11;//提交复审
    const LOG_TYPE_COMIT_FINANCE = 12;//提交财务审核
    const LOG_TYPE_COMIT_PAY = 13;//设置为待付款
    const LOG_STATUS_WATE = 0;//待处理
    const LOG_STATUS_PASS = 1;//通过
    const LOG_STATUS_UNPASS = 2;//拒绝
    
    const STATUS_OP = ['0'=>"待初审",'1'=>"初审通过",'2'=>"初审拒绝"];
    const STATUS_OPMANAGER = ['1'=>"复审通过",'2'=>"复审拒绝",'3'=>"待复审"];
    const TYPE_COMPANY = ['1'=>"平台签约",'2'=>"公司签约"];
    const STATUS_OP_H = ['default'=>"0",'pass'=>"1",'unpass'=>"2"];
    const STATUS_FINANCE_CHECK = ['1'=>"财务审核通过",'2'=>"财务审核拒绝",'3'=>"待财务审核"];
    const STATUS_FINANCE = ['1'=>"汇款成功",'2'=>"汇款失败",'3'=>"待汇款"];
    const STATUS_LOG = ['1'=>"通过",'2'=>"拒绝"];
    const TYPE_LOG = ['1'=>"运营初审",'2'=>"运营复审",'3'=>"财务审核",'4'=>"财务汇款",'5'=>"设置银行卡",'10'=>"系统生成数据",'11'=>"提交复审",'12'=>"提交财务审核",'13'=>"设置为待付款"];
    const STATUS_FINANEC_H = ['default'=>"0",'success'=>"1",'fail'=>"2"];
    const IS_BLACK = ['0'=>"否",'1'=>"是"];
    static function getWages($stime=null,$uids=null){
    $anchor600=[
        2290=>1,
        3055=>1,
        3700=>1,
        3630=>1,
        4820=>1,
        3490=>1,
        2625=>1,
        4380=>1,
        3430=>1,
        3415=>1,
        4465=>1,
        4245=>1,
        4675=>1,
        7930=>1,
        3635=>1,
        8370=>1,
        9060=>1,
        8815=>1,
        8445=>1,
        11885=>1,
        12050=>1,
        8705=>1,
        14445=>1,
        12665=>1,
        11735=>1,
        18075=>1,
        18735=>1,
        24745=>1,
        23365=>1,
        5215=>1,
        18955=>1,
        13165=>1,
        4250=>1,
        4410=>1,
        8045=>1,
        3570=>1,
        8875=>1,
        4565=>1,
        17395=>1,
        4590=>1,
        18375=>1,
        15445=>1,
        3170=>1,
        35030=>1,
        28015=>1,
        4260=>1,
        3675=>1,
        157059=>1,
        5285=>1,
        2790=>1,
        4630=>1,
        16925=>1,
        17555=>1,
        14130=>1,
        23360=>1,
        25245=>1,
        25295=>1,
        13640=>1,
        27700=>1,
        27535=>1,
        21580=>1,
        26410=>1,
        26985=>1,
        29800=>1,
        3740=>1,
        8450=>1,
        4590=>1,
        33635=>1,
        33655=>1,
        29930=>1,
        35005=>1
    ];
        $companys = Company::getCompanyids02();
        $gonghui = array_keys($companys);
        /* $gonghui = [
            15,207,203,25,243
        ]; */
        
        //黑名单
        $anchorBlackDao = D("AnchorBlacklist");
        $anchorBlackinfos = $anchorBlackDao->getField("luid,ctime");
        
        $stime?$stime = $stime: $stime = date("Y-m-01",strtotime(date("Y-m-01"))-86400);
        $etime?$etime = $etime: $etime = date("Y-m-t",strtotime($stime));
        $month = date('Y-m',strtotime($stime));
        //1.礼物收益 ========================================
        $withdrawDao = new \Common\Model\WithdrawModel('exchange_detail',$stime);
        $where['status'] = 2;
        $uids && $where['uid'] =['in',$uids];
        $exchanges = $withdrawDao->where($where)->select();//查出本月所有
        unset($where);
        foreach ($exchanges as $exchange){
            if( $exchange['type'] == 5 ){
                $uid = $exchange['uid'];
                $userinfos[$uid]['uid'] = $uid;
                $exchange['type']==4 && $userinfos[$uid]['bean'] = $exchange['number'];//金豆转换为金币
                $exchange['type']==5 && $userinfos[$uid]['coin_count'] = $exchange['number'];//金币转人民币
                $userinfos[$uid]['tid'] = $exchange['otid'];//订单id
                $anchorBlackinfo = $anchorBlackinfos[$uid];//是否禁播
                if($anchorBlackinfo) {
                    $userinfos[$uid]['is_black']=1;
                    $userinfos[$uid]['wages_base']=0;
                }
            }else{
                continue;
            }
        }
        //2.底薪收益 ==================================
        $lengthDao = M('liveLength');
        $uids && $where['uid'] =['in',$uids];
        //直播开播时间：
        //$livestartinfos = $lengthDao->where($where)->group("uid")->getField("uid,min(date)");
        $livestartinfos = D("company_anchor")->where($where)->group("uid")->getField("uid,min(ctime)");//首次签约日期（2017年8月1日）
        unset($where);
        //直播有效天数：
        $where["length"] = ["egt",3600];
        $where['date'] = [['egt',$stime],['elt',$etime]];
        $uids && $where['uid'] =['in',$uids];
        $livedaysinfos = $lengthDao->where($where)->group("uid")->getField("uid,count(*)");
        //直播时长
        unset($where['length']);
        $lengthinfos = $lengthDao->where($where)->group("uid")->getField("uid,sum(length)");
        unset($where);
        
        //公司信息
        $companyDao = D("company");
        $companyinfos = $companyDao->getField("id,rate,txtrate,bankid,cardid,ownername,papersid,bankaddress,name,id");
        //用户信息
        $userDao = D("anchor");
        $anchorinfos = $userDao->getField("uid,cid");
        $userDao = D("userrealname");
        $realnameinfos = $userDao->getField("uid,name,papersid");
        $userDao = D("userstatic");
        $staticinfos = $userDao->getField("uid,nick");
        $userDao = M("bankCard");
        $bankcardinfos = $userDao->where(["status"=>0])->getField("uid,name,bankid,address,phone,cardid,accountbank");
        $userDao = M("bank");
        $bankinfos = $userDao->getField("id,name");
        
        
        //主播表主播集合
        $anchorDao = M("anchor");
        $uids && $where['uid'] =['in',$uids];
        $anchors = $anchorDao->where($where)->select();
        foreach ($anchors as $anchor){
            $uid = $anchor['uid'];
            $cid = $anchor['cid'];
            $anchorBlackinfo = $anchorBlackinfos[$uid];//是否禁播
            $livestartinfo = $livestartinfos[$uid];//首播日期
            $lengthinfo = $lengthinfos[$uid];//直播时长
            $livedaysinfo = $livedaysinfos[$uid];//有效天数
            if( $livestartinfo && $cid ){//计算底薪
                $startmonth = date("Y-m",strtotime($livestartinfo));
                //非当月开播 ：满96小时，有效时长23天，底薪600元
                if($lengthinfo >= 96*3600 &&  $livedaysinfo >=23) {
                    $userinfos[$uid]['wages_base'] = 600;
                }elseif($startmonth == $month ){//当月开播：满46小时，有效是时长11天，底薪300元
                    if($lengthinfo >= 46*3600 &&  $livedaysinfo >=11) {
                        $userinfos[$uid]['wages_base'] = 300;
                    }else{
                        if(!$uids) continue;//指定uid，需要插入表的数据
                    }
                }else{//忽略没有底薪的主播
                    if(!$uids) continue;
                }
            }else{//没有开播
                if(!$uids) continue;
            }
            
            if( in_array_case($cid, $gonghui) ){//工会底薪特殊处理
                $userinfos[$uid]['wages_base'] == 600 && $userinfos[$uid]['wages_base'] = 500;
                $userinfos[$uid]['wages_base'] == 300 && $userinfos[$uid]['wages_base'] = 250;
            }
            
            if($anchorBlackinfo) {
                $userinfos[$uid]['is_black']=1;
                $userinfos[$uid]['wages_base']=0;
            }
            $livestartinfo && $userinfos[$uid]['live_start'] = $livestartinfo;
            $lengthinfo && $userinfos[$uid]['live_length'] = $lengthinfo;
            $livedaysinfo && $userinfos[$uid]['live_day'] = $livedaysinfo;
        }
        
        foreach ($userinfos as $uid=>&$userinfo){
            $userinfo['uid'] = $uid;
            $realnameinfos[$uid] && $userinfos[$uid]['card'] = $realnameinfos[$uid]['papersid'];
            if($anchor600[$uid]){//老数据特殊处理
                $userinfo['wages_base'] == 500 && $userinfo['wages_base'] = 600;
                $userinfo['wages_base'] == 250 && $userinfo['wages_base'] = 300;
            }
            $userinfo['coin_count'] > $userinfo['bean'] && $userinfo['coin'] = $userinfo['coin_count'] - $userinfo['bean'];
            $userinfo['coin_count'] && $userinfo['wages_gift'] = $userinfo['coin_count'];
            $userinfo['wages_base'] && $userinfo['wages_base_edit'] = $userinfo['wages_base'];
            $userinfo['wages_gift'] && $userinfo['wages_gift_edit'] = $userinfo['wages_gift'];
            $userinfo['month'] = $month;
            $userinfo['live_length'] && $userinfo['live_length'] = secondFormatH($userinfo['live_length']);
            $companyid = $anchorinfos[$uid];
            $companyinfo = $companyinfos[$companyid];
            $companyinfo && $userinfos[$uid]['company_id'] = $companyinfo['id'];
            $companyinfo && $userinfos[$uid]['company_name'] = $companyinfo['name'];
            $companyinfo && $userinfos[$uid]['company_rate'] = $companyinfo['rate'];
            $companyinfo && $userinfos[$uid]['company_tax'] = $companyinfo['txtrate'];
            $companyinfo && $userinfos[$uid]['company_realname'] = $companyinfo['ownername'];
            $companyinfo && $userinfos[$uid]['company_card'] = $companyinfo['papersid'];
            $companyinfo && $userinfos[$uid]['company_bank_card'] = $companyinfo['cardid'];
            $companyinfo && $userinfos[$uid]['company_bank_id'] = $companyinfo['bankid'];
            $companyinfo && $userinfos[$uid]['company_bankaddress'] = $companyinfo['bankaddress'];
            $bankinfos[$companyinfo['bankid']] && $userinfos[$uid]['company_bank_name'] = $bankinfos[$companyinfo['bankid']];
            $realnameinfos[$uid] && $userinfos[$uid]['realname'] = $realnameinfos[$uid]['name'];
            $staticinfos[$uid] && $userinfos[$uid]['nick'] = $staticinfos[$uid];
            $bankcardinfos[$uid] && $userinfos[$uid]['bank_card'] = $bankcardinfos[$uid]['cardid'];
            $bankcardinfos[$uid] && $userinfos[$uid]['bank_id'] = $bankcardinfos[$uid]['bankid'];
            $bankcardinfos[$uid] && $userinfos[$uid]['bankaddress'] = $bankcardinfos[$uid]['address'];
            $bankcardinfos[$uid] && $userinfos[$uid]['accountbank'] = $bankcardinfos[$uid]['accountbank'];
            $bankcardinfos[$uid] && $userinfos[$uid]['bank_name'] = $bankinfos[$bankcardinfos[$uid]['bankid']];
            $bankcardinfos[$uid] && $userinfos[$uid]['bank_username'] = $bankcardinfos[$uid]['name'];
            $bankcardinfos[$uid] && $userinfos[$uid]['bank_phone'] = $bankcardinfos[$uid]['phone'];
            
            $livestartinfo = $livestartinfos[$uid];//首播日期
            $lengthinfo = $lengthinfos[$uid];//直播时长
            $livedaysinfo = $livedaysinfos[$uid];//有效天数
            $livestartinfo && $userinfos[$uid]['live_start'] = $livestartinfo;
            $lengthinfo && $userinfos[$uid]['live_length'] = $lengthinfo;
            $livedaysinfo && $userinfos[$uid]['live_day'] = $livedaysinfo;
            
        }
        return $userinfos;
    }
    
    /* 含税劳务报酬计算
    　　税率表如下(表1)：
    　　级数	含税级距	税率（%）	速算扣除数
    　　1	不超过20000元的	20	0
    　　2	超过20000元至50000元的部分	30	2000
    　　3	超过50000元的部分	40	7000
    　　　　1.表中的含税级距为按照税法规定减除有关费用后的每次应纳税所得额;劳务报酬所得按次计算纳税，每次收入额不超过4000元的，减除费用800元，收入额超过4000元的，减除20%的费用，余额为应纳税所得额。
    　　　　	
    　　　　	　　2.应交个人所得税的计税公式：
    　　　　	　　　　应纳税额=应纳税所得额×适用税率-速算扣除数 (按表1进行) */
    
    static function getanchortax($wages){
        //=IF(I2<800,0,MAX(I2*IF(I2>4000,(1-20%),(1-800/I2))*{0.2,0.2,0.3,0.4}-{0,0,2000,7000}))
        //劳务报酬应纳税额（4 000元以内）＝（劳务报酬－800）×20%
        //劳务报酬应纳税额（超过4 000元）＝劳务报酬 ×（1－20%）×税率－速算扣除数
        if($wages<800){
            $tax = 0;
            return $tax;
        }
        
        if($wages<=20000){
            $rate = 0.2;
            $k = 0;
        }elseif($wages<=50000){
            $rate = 0.3;
            $k = 2000;
        }else{
            $rate = 0.4;
            $k = 7000;
        }
        if($wages<4000){
            $tax = ($wages-800)*0.2;
        }else{
            $tax = $wages*(1-0.2)*$rate-$k;
        }
        
        return $tax;
    }
    
    static function log($param,$wages_ids) {
        $dao = D('admin_wages_log');
        $user = get_user();
        $param['uaid'] = $user['uid'];
        $param['ctime'] = get_date();
        if($wages_ids){
            foreach ($wages_ids as $wages_id){
                $param['wages_id'] = $wages_id;
                $datalist[] = $param;
            }
            $dao->addAll($datalist);
        }else{
            $dao->add($param);
        }
    }
}