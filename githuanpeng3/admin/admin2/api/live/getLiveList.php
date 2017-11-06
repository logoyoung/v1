<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/8
 * Time: 下午4:26
 */
//ini_set("display_errors", 'On');
//error_reporting(E_ALL);

header("Content-Type:text/event-stream");
header("Cache-control:no-cache");
include "../../includeAdmin/init.php";
include INCLUDE_DIR . "live/Review.class.php";

$db = new DBHelperi_admin();
use hp\live\Review;

if ($_COOKIE['admin_uid']) {
    $review = new Review($_COOKIE['admin_uid'], $db);
//    $myList = listMerge($review->myTask(), $review);
//    resizeAndSetClose($myList, listMerge($review->myTask(), $review), $review);
//    if (count($myList) < 9) {
//        addNewList($myList, $review->lockTask($review->getTask()), $review);
//    }
//    resetListArray($myList);
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$myList = $review->getTask($page);
    myListEvent($myList);
} else {

}
flush();
ob_flush();


function myListEvent($list)
{
    echo "event:myReviewList\n";
    echo "data:" . json_encode(array('list' => $list, "count" => count($list))) . "\n\n";
}

function addNewList(&$myList, $listOne,$review)
{
    if (!$myList) {
        $myList = array();
    }

    if($listOne){
//        array_push($myList, array(
//            'liveid'=> $listOne['liveid'],
//            'luid' =>$review->getLuid($listOne['liveid'])
//        ));
        $list = array(
            'liveid'=> $listOne['liveid'],
            'luid' =>$review->getLuid($listOne['liveid'])
        );
        resizeAndSetClose($myList, $list, $review);
    }
}

function resetListArray(&$myList){
    $tmp = array();
    foreach($myList as $key => $val){
        array_push($tmp, array('liveid'=>$val, 'luid'=>$key));
    }

    $myList = $tmp;
}

function listMerge($list, $review){
    $tmp = array();
    foreach($list as $val){
        array_push($tmp, array(
            'liveid' => $val['liveid'],
            'luid' => $val['luid']
        ));
    }

    return $tmp;
}



function resizeAndSetClose(&$myList, $list, $review){
    if(!$myList) $myList = array();
    foreach($list as $value){
        if(!$review->isLiveOn($value['liveid'])){
            $review->succEnd($value['liveid']);
            continue;
        }
        if(isset($myList[$value['luid']])){
            $liveid = $value['liveid'] < $myList[$value['luid']] ? $value['liveid'] : $myList[$value['luid']];
            $review->succEnd($liveid);
        }else{
            $myList[$value['luid']] = $value['liveid'];
        }
    }
}



