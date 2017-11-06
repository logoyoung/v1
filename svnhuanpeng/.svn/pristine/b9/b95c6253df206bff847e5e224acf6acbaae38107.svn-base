<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/2/24
 * Time: 11:10
 *
 *
 * 定时检查更新推荐主播
 *
 */

include '/usr/local/huanpeng/include/init.php';
//$GLOBALS['env'] = 'DEV';
define('RECOMMEND_COUNT',6);
define('RECOMMEND_WAIT',0);
define('RECOMMED_END',1);
define('RECOMMEND_WEB',2);
define('RECOMMEND_APP',1);
define('RECOMMEND_LOG','/data/logs/huanpeng_recommend.log');
/**
 * 获取所有推荐主播
 * @param $db
 * @return array|string
 */
function getAllRecommendAnchorList( $db )
{
    $sql = "SELECT `uid` FROM `admin_recommend_live`";
    $res = $db->query( $sql );
    if( !$res )
    {
        return '';
    }
    $anchorList = array();
    while ($row = mysqli_fetch_row($res))
    {
        $anchorList[] = $row[0];
    }
    return $anchorList;
}

/**
 * 获取主播直播状态
 * @param $uid
 * @param $db
 * @return bool
 */
function checkAnchorLiveStatus( $uid, $db )
{
    $sql = " SELECT `uid` FROM `live` WHERE `uid`={$uid} AND `status`=".LIVE;
    $res = $db->query( $sql );
    if( !$res )
    {
        return false;
    }
    $row = mysqli_fetch_row( $res );
    if( !$row || !$row[0])
    {
        return false;
    }
    return true;
}

function updateAllAnchorStatus( $status, $db )
{
    $sql = "UPDATE `admin_recommend_live`  SET  `status`= {$status} WHERE `status`=".RECOMMED_END;
    $db->query( $sql );
    return $db->affectedRows?true:false;
}

function updateSomeAnchorStatus( $anchor,$status, $db )
{
    $anchorStr = '';
    foreach ($anchor as $k => $v)
    {
        if( !$anchorStr )
        {
            $anchorStr .= "'$v'";
        }
        else
        {
            $anchorStr .= ",'$v'";
        }

    }
    //$anchorStr = implode(',', $anchor );
    $sql = "UPDATE `admin_recommend_live` SET `status`={$status} WHERE `uid` IN ({$anchorStr})";
    $db->query( $sql );
}

function updateRecommedList($anchorList, $db)
{
    $anchorStr = implode(',',$anchorList);var_dump($anchorStr);
    $sql = "UPDATE `recommend_live` SET `list`='{$anchorStr}',`utime`=now() WHERE `client`=".RECOMMEND_WEB;
    $db->query( $sql );
    return $db->affectedRows?true:false;
}
function getCurrentRecommend( $db )
{
    $sql = "SELECT `list` FROM `recommend_live` WHERE `client`=".RECOMMEND_WEB;
    $res = $db->query($sql);
    $row = mysqli_fetch_row($res);
    return $row[0];
}
function checkAnchorHasLive($uid, $db)
{
    $sql = " SELECT `uid` FROM `live` WHERE `uid`={$uid}";
    $res = $db->query( $sql );
    if( !$res )
    {
        return false;
    }
    $row = mysqli_fetch_row( $res );
    if( !$row || !$row[0])
    {
        return false;
    }
    return true;
}
$db = new DBHelperi_huanpeng();

$currentRecommendAnchorList = getCurrentRecommend($db);
$currentRecommendAnchorList = explode(',',$currentRecommendAnchorList);
$curLiveAnchorList = array();
foreach ($currentRecommendAnchorList as $k => $v )
{
    if( checkAnchorLiveStatus($v,$db) )
    {
       $curLiveAnchorList[$k] = $v;
    }
    else
    {
        $currentRecommendAnchorList[$k] = '';
    }
}
echo "---------1-------\n";
var_dump($curLiveAnchorList);

$needLen = RECOMMEND_COUNT - count($curLiveAnchorList);
$allAnchorList = getAllRecommendAnchorList($db);
$allAnchorList = array_diff($allAnchorList,$curLiveAnchorList);

$liveAnchorList = array();
$recommendList = array();
foreach ($allAnchorList as $k=>$v)
{
    //var_dump(checkAnchorLiveStatus($v, $db));
    if(checkAnchorLiveStatus($v, $db))
    {
        $liveAnchorList[] = $v;
    }
    if( count($liveAnchorList) < $needLen )
    {
        continue;
    }
    else
    {
        break;
    }
}
echo "----------2----------\n";
var_dump($liveAnchorList);
//var_dump($liveAnchorList);
$liveCount = count($liveAnchorList);
$needLen -= $liveCount;
$allAnchorList = array_diff($allAnchorList,$liveAnchorList);
//$noLiveList = array_slice($allAnchorList,0,$needLen);
$noLiveList = array();
foreach ($allAnchorList as $k=>$v)
{
    if(count($noLiveList)==$needLen)
    {
        break;
    }
    if(checkAnchorHasLive($v, $db))
    {
        //$noLiveList[] = $v;
    }
}
echo "----------3----------\n";
var_dump($noLiveList);
//$recommendList = array_merge($liveAnchorList,$noLiveList);
$recommendList = $liveAnchorList;
echo "----------4----------\n";
var_dump($recommendList);


/*if( $liveCount==0 )
{
    $recommendList = array_slice($allAnchorList,0,$needLen);
}
else if($liveCount<$needLen)
{
    $diffArray = array_diff($allAnchorList,$liveAnchorList);
    $diffArray = array_slice($diffArray,0,$needLen-$liveCount);
    $recommendList = array_merge($liveAnchorList,$diffArray);
}
else
{
    $recommendList = $liveAnchorList;
}
*/
$j = 0;
for($i = 0;$i<RECOMMEND_COUNT;$i++)
{
    if(!isset($currentRecommendAnchorList[$i])||!$currentRecommendAnchorList[$i])
    {
        $currentRecommendAnchorList[$i] = $recommendList[$j];
        $j++;
    }
}
$currentRecommendAnchorList = $recommendList;
var_dump($currentRecommendAnchorList);
//$list = implode(',', $currentRecommendAnchorList);
//exit;
updateAllAnchorStatus(RECOMMEND_WAIT,$db);
updateSomeAnchorStatus($currentRecommendAnchorList, RECOMMED_END,$db);
$res = updateRecommedList($currentRecommendAnchorList,$db);
//var_dump($res);
$logMsg = implode(',',$currentRecommendAnchorList);
mylog($GLOBALS['env'].":".$logMsg,RECOMMEND_LOG);
var_dump($recommendList);
echo "\n";
echo "ok\n";















