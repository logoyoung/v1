<?php
/**
 * 说明：
 * 通过直播ID获取该直播所有流名称以及流所在的服务器
 *
 * @param object $db
 * @param number $liveid//直播ID
 * @param array $serverList//服务器列引用(ip:port)
 * @param array $streamList//流名称引用
 * @return boolean
 */
function getStreamListByLive($db, $liveid, &$serverList, &$streamList)
{   if(!$liveid)
        return false;
    $serverList = array();
    $streamList = array();
    $sql = "SELECT * FROM `liveStreamRecord` WHERE `liveid`={$liveid}";
    $res = $db->query($sql);
    if (! $res)
        return false;
    if (! $res->num_rows)
        return false;
    while ($row = $res->fetch_assoc()) {
        $serverList[] = $row['server'];
        $streamList[] = $row['stream']; // var_dump($row);
    }
    return true;
}

$liveid = isset($_GET['liveid'])?$_GET['liveid']:0;
$db = new DBHelperi_huanpeng();
getStreamListByLive($db, $liveid, $serverList, $streamList);
$addr = array();
foreach ($serverList as $k=>$v){
    $addr[] = $v.$streamList[$k];
}
echo json_encode($addr);
exit;
