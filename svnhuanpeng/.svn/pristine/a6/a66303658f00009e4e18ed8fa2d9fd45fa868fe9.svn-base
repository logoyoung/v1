<?php

include '../../../include/init.php';

use service\room\LiveRoomService;
/**
 * App 端批量获取直播间观众头像
 * date 2016-05-10 13:36
 * anchor yandong@6rooms.com
 * @update xuyong 2017-5-18
 */
class liveUserPicList
{

    const ERROR_LUID_EMPTY = -4013;
    private $_luid;
    public static $errorMsg = [
        self::ERROR_LUID_EMPTY => '参数luid不能为空',
    ];

    public function display()
    {

        $this->_luid     = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
        if(!$this->_luid)
        {
            $code = self::ERROR_LUID_EMPTY;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('api:'.__FILE__.';line:'.__LINE__);
        $liveRoomService->setLuid($this->_luid);
        $onlineList      =  $liveRoomService->getOnlineUserList();

        render_json($onlineList);
    }
}

$obj = new liveUserPicList();
$obj->display();



die;
require(INCLUDE_DIR . 'LiveRoom.class.php');
/**
 * App 端批量获取直播间观众头像
 * date 2016-05-10 13:36
 * anchor yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取直播间的观众
 * @param type $luid 主播id
 * @param type $db
 * @return array
 */
function getUserPicList($luid, $db)
{
    $uids = array();
    $row = $db->field('uid')->where("luid= $luid  and  uid != $luid and  uid < " . LIVEROOM_ANONYMOUS . " group by uid")->order('tm DESC')->limit(20)->select('liveroom');
    if ($row) {
        foreach ($row as $v) {
            array_push($uids, $v['uid']);
        }
        if (false !== array_search($luid, $uids)) {
            array_splice($uids, array_search($luid, $uids), 1);
        }
    }

    return $uids;
}

/**
 * 根据用户id获取头像
 * @param type $uids 用户头像
 * @param type $db
 * @return type
 */
function getUserPicByUids($uids, $db)
{
    $uid = implode(',', $uids);
    $row = $db->field('uid,pic')->where("uid in($uid)")->select('userstatic');
    if ($row) {
        foreach ($row as $v) {
            $temp[$v['uid']] = $v['pic'];
        }
    } else {
        $temp = array();
    }
    return $temp;
}

/**
 * 获取最新进房间的二十个人的头像
 * @param type $luid 主播id
 * @param type $conf
 * @param type $db
 * @return array
 */
function getList($luid, $conf, $db)
{
    $res = getUserPicList($luid, $db);
    $number = count($res);
    if ($number < 20) {
        $redobj = new RedisHelp();
        $roobet = getRobotHeadList($luid, $redobj);
        $rootlist = array_reverse($roobet);
        $size = (20 - $number);
        $afterrev = array_slice($rootlist, 0, $size);
        $res=array_merge($res,$afterrev);
    }
    $result = array();
    if ($res) {
        $piclist = getUserPicByUids($res, $db);
        if ($piclist) {
            for ($i = 0, $k = count($res); $i < $k; $i++) {
                $temp['uid'] = $res[$i];
                $temp['head'] = (array_key_exists($res[$i], $piclist) && !empty($piclist[$res[$i]])) ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $piclist[$res[$i]] : DEFAULT_PIC;
                array_push($result, $temp);
            }
        }
    }
    return $result;
}

/**
 * start
 */
$luid = isset($_POST['luid']) ? (int)($_POST['luid']) : '';
if (empty($luid)) {
    error2(-4013);
}
$luid = checkInt($luid);
$Picres = getList($luid, $conf, $db);
$obj = new LiveRoom(1);
$onLineUser = $obj->getLiveUserCountByLuid($luid);

succ(array('list' => $Picres, 'total' => $onLineUser));