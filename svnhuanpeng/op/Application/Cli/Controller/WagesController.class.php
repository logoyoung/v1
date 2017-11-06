<?php
/*
 *  工资计算
 *  每月月初初始化 上月 工资表： admin_wages
 */
namespace Cli\Controller;
use HP\Op\Wages;
class WagesController extends \Think\Controller
{
   
   public function init($do=null,$stime=null,$etime=null,$uids=null){
        $dao = D("admin_wages");
        $stime?$stime = $stime: $stime = date("Y-m-01",strtotime(date("Y-m-01"))-86400);
        $etime?$etime = $etime: $etime = date("Y-m-t",strtotime($stime));
        $month = date('Y-m',strtotime($stime));
        echo "============================ month: ".$month." =============start========";echo "\r\n";
        if($do=='reset'){
            $dao->where(['month'=>$month])->delete();
            echo $dao->_sql(); echo "\r\n";
        }
        $userinfos = Wages::getWages($stime,$uids);
        foreach ($userinfos as $userinfo){
            if($do=='reset'||$do=='do'){
                echo $dao->data($userinfo)->add();echo "\r\n";
            }else{
                echo $dao->fetchSql(true)->data($userinfo)->add();echo "\r\n";
            }
        }
        echo "============================ month: ".$month." =============end========";echo "\r\n";
    }
}
