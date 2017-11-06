<?php
/**
 * 获取经纪公司旗下主播列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();


/**
 * start
 */
$cid = isset($_POST['cid']) ? (int)$_POST['cid'] : '';
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;

//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-1007);
//}
//
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}

$res=array(
    array('uid'=>'90','nick'=>'蚂蚁呀嘿','time'=>'1:42:12','hpbean'=>'300','hpcoin'=>'400','userCount'=>200),
    array('uid'=>'10','nick'=>'solo','time'=>'5:41:12','hpbean'=>'400','hpcoin'=>'10','userCount'=>100),
    array('uid'=>'20','nick'=>'marry','time'=>'6:20:22','hpbean'=>'500','hpcoin'=>'20','userCount'=>40),
    array('uid'=>'30','nick'=>'你菜','time'=>'0:40:22','hpbean'=>'600','hpcoin'=>'50','userCount'=>50),
    array('uid'=>'40','nick'=>'菜花','time'=>'10:40:32','hpbean'=>'8000','hpcoin'=>'4000','userCount'=>2000),
    array('uid'=>'50','nick'=>'白云','time'=>'3:30:52','hpbean'=>'350','hpcoin'=>'400','userCount'=>1000),
    array('uid'=>'60','nick'=>'黑土','time'=>'5:41:42','hpbean'=>'60','hpcoin'=>'520','userCount'=>300),
    array('uid'=>'70','nick'=>'show','time'=>'6:20:32','hpbean'=>'311','hpcoin'=>'900','userCount'=>400),
    array('uid'=>'80','nick'=>'韩梅梅','time'=>'3:30:2','hpbean'=>'322','hpcoin'=>'40','userCount'=>20),
    array('uid'=>'90','nick'=>'李雷','time'=>'3:40:2','hpbean'=>'200','hpcoin'=>'40','userCount'=>10),
    array('uid'=>'110','nick'=>'翠华','time'=>'4:10:2','hpbean'=>'40','hpcoin'=>'50','userCount'=>90),
    array('uid'=>'930','nick'=>'啷个哩个啷','time'=>'3:40:2','hpbean'=>'50','hpcoin'=>'30','userCount'=>20),
    array('uid'=>'950','nick'=>'卡卡西塞塞','time'=>'2:40:12','hpbean'=>'60','hpcoin'=>'20','userCount'=>80),
    array('uid'=>'940','nick'=>'就是这么溜','time'=>'3:10:20','hpbean'=>'400','hpcoin'=>'90','userCount'=>90),
    array('uid'=>'910','nick'=>'固网','time'=>'2:20:2','hpbean'=>'3000','hpcoin'=>'1000','userCount'=>1200),
    array('uid'=>'960','nick'=>'toke','time'=>'10:40:2','hpbean'=>'1700','hpcoin'=>'900','userCount'=>2300),
);
if ($res) {
    succ(array('list'=>$res,'total'=>count($res)));
} else {
    error(array('list'=>array(),'total'=>0));
}


/**获取经纪公司旗下的主播
 * @param $cid
 * @param $db
 * @return array|bool
 */
function getAnchorListByCid($cid,$db){
  if(empty($cid)){
      return  false;
  }
  $res=$db->field('uid,level,bean,coin')->where('cid='.$cid)->select('anchor');
    if(false !==$res){
        if($res){
            return $res;
        }else{
            return array();
        }
    }else{
        return false;
    }
}

/**获取主播人气值
 * @param $uids
 * @param $db
 * @return bool
 */
function  getAnchorPopular($uids,$db){
    if(empty($uids)){
        return false;
    }
    $uids=implode(',',$uids);
    $res=$db->where("uid in ($uids)")->select('anchor_most_popular');
}

/**获取主播直播时长
 * @param $uids
 * @param $db
 * @return bool
 */
function  getAnchorLiveLength($uids,$db){
    if(empty($uids)){
        return false;
    }
    $uids=implode(',',$uids);
    $res=$db->where("uid in ($uids)")->select('live_length');
}