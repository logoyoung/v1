<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

function getRecommendNo($db)
{
    $res = $db->field('id,list')->select('recommend_information');
    if (false !== $res) {
        if ($res) {
           foreach ($res as $v){
              $temp[$v['id']]=$v['list'];
           }
           return $temp;
        } else {
            return array();
        }
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getRecommendNo($db);
if($res){
    $plist=$tlist=0;
    if(isset($res[1])){
        $plist=count(explode(',',$res[1]));
    }
    if(isset($res[2])){
        $tlist=count(explode(',',$res[2]));
    }
    succ(array('plist'=>array('public'=>$plist,'total'=>INFORMATION_RECOMMENT_NUMBER),'tlist'=>array('public'=>$tlist,'total'=>INFORMATION_RECOMMENT_NUMBER)));
}else{
    succ(array('plist'=>array('public'=>0,'total'=>0),'tlist'=>array('public'=>0,'total'=>0)));
}



