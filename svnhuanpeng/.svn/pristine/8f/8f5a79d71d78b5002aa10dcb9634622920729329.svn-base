<?php

include '../../init.php';
/**
 * 直播结束回调入库
 * date 2016-05-09 14:35
 * author yandong@6rooms.com
 * copyright 6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * 添加一条数据到video表
 * @param type $uid 用户uid
 * @param type $url 录像路径
 * @param type $length 录像长度
 * @param type $db
 * @return boolean
 */
function addVideo($liveid, $url, $length, $db,$conf)
{
    if (empty($liveid) || empty($url)) {
        return false;
    }
    $liveInfo = checkStreamIsExist($liveid, $db);
    if( $GLOBALS['env'] == 'DEV'){
        $fname=str_replace('http://fvod.huanpeng.com'.'/','',urldecode($url));
    }else{
        $fname=str_replace($conf['domain-video'].'/','',urldecode($url));
    }
    if ($liveInfo) {
        $add = array(
            'uid' => $liveInfo[0]['uid'],
            'gametid' => $liveInfo[0]['gametid'],
            'gameid' => $liveInfo[0]['gameid'],
            'gamename' => $liveInfo[0]['gamename'],
            'title' => $liveInfo[0]['title'],
            'poster' => '',
            'length' => $length,
            'liveid' => $liveInfo[0]['liveid'],
            'ip' => $liveInfo[0]['ip'],
            'port' => $liveInfo[0]['port'],
            'vfile' => $fname,
            'orientation' => $liveInfo[0]['orientation']
        );
        if ($liveInfo[0]['antopublish'] == 1) {//校验是否自动发布
            $add['status'] = VIDEO_UNPUBLISH;
        }
        $res = $db->insert('video', $add);
        Log_for_net('录像合并完成回3', array('res' =>$res), $db);
        if (false !== $res) {
            if ($liveInfo[0]['antopublish'] == 1) {
                synchroAdminWiatPassVideo($res, $db); //如果是自动发布的添加到admin_wait_pass_video
            }
            $addres = addToDownLoad($liveInfo[0]['liveid'], urldecode($url), $db);//同步到下载记录表
            Log_for_net('添加到录像下载表', array('res' => $addres), $db);
            return true;
        } else {
            return false;
        }
    } else {
        return false; //查询出错
    }
}


function checkStreamIsExist($liveid, $db)
{
    if (empty($liveid)) {
        return false;
    }
    $res = $db->field('liveid,uid,gametid,gameid,gamename,title,poster,ip,port,orientation,antopublish')->where("liveid=$liveid")->limit(1)->select('live');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            return $res;
        }
    } else {
        return false;
    }
}

/**同步到录像下载表
 * @param $liveid  直播id
 * @param $url  下载地址
 * @param $db
 * @return bool
 */
function addToDownLoad($liveid, $url, $db)
{
    if (empty($liveid) || empty($url)) {
        return false;
    }
    $data = array(
        'liveid' => $liveid,
        'url' => $url
    );
    $res = $db->insert('video_download_record', $data);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

function getVideo_merge_recoed($taskid, $db)
{
    if (empty($taskid)) {
        return false;
    }
    $res = $db->field('liveid')->where("taskid='$taskid'")->limit(1)->select('video_merge_record');
    if (false !== $res) {
        if (!empty($res)) {
            return $res[0]['liveid'];
        } else {
            return array();
        }
    } else {
        return false;
    }
}

function setVideo_merge_recoed_status($taskid,$length, $db)
{
    if (empty($taskid) || empty($length)) {
        return false;
    }
    $res = $db->where("taskid='$taskid'")->update('video_merge_record',array('status'=>1,'length'=>$length));
    if (false !== $res) {
        if (!empty($res)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}



/**
 * start
 */
$body = @file_get_contents('php://input');
Log_for_net('录像合并完成回调', array('body' => base64_decode($body)), $db);
if (empty($body)) {
    echo 0;
    exit;
} else {
    $unbody = json_decode(base64_decode($body), true);
    if ($unbody['items'][0]['code'] == 3) { //成功
        $taskid = $unbody['id'];
        $url = $unbody['items'][0]['url'];
        $liveid = getVideo_merge_recoed($taskid, $db);//获取liveid
        $length = strstr($unbody['items'][0]['duration'],'.',true);
        if (false !== $liveid) {
            if (empty($liveid)) {
                Log_for_net('video_merge_record表中无对应数据', array('res' => $liveid), $db);
                echo 1;
                exit;
            } else {
                $addvideoStatus = addVideo($liveid, $url, $length, $db,$conf);
                Log_for_net('添加到录像表', array('res' => $addvideoStatus), $db);
                setVideo_merge_recoed_status($taskid,$length, $db);//更改video_merge_record表状态
                echo 1;
                exit;
            }
        } else {
            Log_for_net('录像回调mysql失败', array('res' => $liveid), $db);
            echo 1;
            exit;
        }
    } else {
        Log_for_net('录像合并失败', array('body' => json_encode(base64_decode($body),true)), $db);
        echo 0;
        exit;
    }
}
