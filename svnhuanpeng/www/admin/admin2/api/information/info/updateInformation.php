<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 修改资讯
 * @author yandong@6room.com
 * date 2016-11-23  20:08
 */

/**修改资讯
 * @param $id  资讯id
 * @param $tid  资讯类型id
 * @param $title  标题
 * @param $poster  封面图
 * @param $content  内容
 * @param $status  状态
 * @param $db
 * @return bool
 */
function updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend,$url,$adtype,  $db)
{
    $data = array(
        'tid' => $tid,
        'title' => $title,
        'poster' => $poster,
        'content' => $content,
        'utime' => date('Y-m-d H:i:s', time()),
        'status' => $status,
        'type'=>$adtype,
        'url'=>$url,
        'isrecommend' => $isrecommend
    );
    $res = $db->where("id=$id")->update('admin_information', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**获取推荐状态
 * @param $id  资讯id
 * @param $db
 * @return bool
 */
function getRecommendStatus($id, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->field('isrecommend')->where("id=$id")->select('admin_information');
    if (false !== $res) {
        return $res[0]['isrecommend'];
    } else {
        return false;
    }
}

function getRecommentData($type, $db)
{
    if ($type) {
        $res = $db->field('id,list')->where("id = $type")->select('recommend_information');
    } else {
        $res = $db->field('id,list')->select('recommend_information');
    }
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $list[$v['id']] = $v['list'];
            }
            return $list;
        }
    } else {
        return false;
    }
}


$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$poster = isset($_POST['poster']) ? trim($_POST['poster']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$url = isset($_POST['url']) ? trim($_POST['url']) : '';
$adtype = isset($_POST['adtype']) ? trim($_POST['adtype']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$isrecommend = isset($_POST['isRecommend']) ? (int)($_POST['isRecommend']) : '';

if (empty($uid) || empty($encpass) || empty($id)) {
    error(-1007);
}
if (empty($tid)) {
    error(-1026);
}
if (empty($title)) {
    error(-1024);
}
if (empty($content)) {
    error(-1025);
}
if (!in_array($isrecommend, array(0, 1, 2))) {
    error(-1023);
}
if(!in_array($adtype,array(0,1))){
    error(-1023);
}
if($adtype==1){
    if(empty($adtype)){
        error(-1036);
    }
}
if($isrecommend==1){
    if(empty($poster)){
        error(-1030);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$title = filterWords($title);
$content = filterWords($content);
if ($status == 1) {
    $recomStatus = getRecommendStatus($id, $db);
    if ($recomStatus != $isrecommend) {
        $datalist = getRecommentData(0, $db);
        if ($datalist === false) {
            error(-1014);
        } else {
            if (empty($datalist)) {//是否有数据
                if (in_array($isrecommend, array(1, 2))) {//是否是推荐
                    $addres = addRecommentData($id, $isrecommend, $db);
                    if (false !== $addres) {
                        $res = updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend,$url,$adtype, $db);
                    } else {
                        error(-1014);
                    }
                } else {
                    $res = updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend,$url,$adtype, $db);
                }
            } else {
                if ($isrecommend == 0) {//修改为不推荐
                    if (isset($datalist[1])) {
                        $picList = implode(',', array_diff(explode(',', $datalist[1]), array($id)));
                        addRecommentData($picList, 1, $db);//更新轮播列表
                    }
                    if (isset($datalist[2])) {
                        $textList = implode(',', array_diff(explode(',', $datalist[2]), array($id)));
                        addRecommentData($textList, 2, $db);//更新文字列表
                    }
                    $res = updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend, $url,$adtype,$db);
                } else {
                    if ($recomStatus == 0) {//以前是非推荐状态现在改成推荐状态
                        if (isset($datalist[$isrecommend])) {
                            $textList = $datalist[$isrecommend];
                            $textList = explode(',', $textList);
                            if (count($textList) >= INFORMATION_RECOMMENT_NUMBER) {//推荐已经满了
                                $isrecommend = $recomStatus;
                            } else {
                                array_push($textList, "$id");
                                $textList = implode(',', $textList);
                                addRecommentData($textList, $isrecommend, $db);//更新到新列表
                            }
                        } else {
                            addRecommentData($id, $isrecommend, $db);//更新到新列表
                        }
                    } else {//以前是推荐状态改变推荐方式（有轮播改为列表推荐  反之亦然）
                        if ($datalist[$recomStatus]) {
                            $picList = implode(',', array_diff(explode(',', $datalist[$recomStatus]), array($id)));
                            addRecommentData($picList, $recomStatus, $db);//删除文字列表中的id
                            $textList = $datalist[$isrecommend];
                            if (empty($textList)) {
                                $textList = $id;
                            } else {
                                $textList = explode(',', $textList);
                                if (count($textList) > INFORMATION_RECOMMENT_NUMBER) {//推荐已经满了
                                    $isrecommend = $recomStatus;
                                }
                                if (count($textList) == INFORMATION_RECOMMENT_NUMBER) {
                                    unset($textList[count($textList) - 1]);
                                }
                                array_push($textList, "$id");
                                $textList = implode(',', $textList);
                            }
                            addRecommentData($textList, $isrecommend, $db);//更新到新列表

                        } else {
                            addRecommentData($id, $isrecommend, $db);//添加列表
                        }
                    }
                    $res = updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend, $url,$adtype, $db);
                }

            }
        }
    } else {//修改前后推荐状态一致直接修改
        $res = updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend, $url,$adtype, $db);
    }
} else {//不是发布状态直接修改
    $res = updateInforMation($id, $tid, $title, $poster, $content, $status, $isrecommend,$url,$adtype, $db);
}
if ($res) {
    succ();
} else {
    error(-1014);
}


