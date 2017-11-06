<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/1/7
 * Time: 11:12
 */
include '../include/init.php';
$videoid = $_GET['videoid'];

if(!isMobile()){
    header("Location:".WEB_ROOT_URL.'videoRoom.php?videoid='.$videoid);
}else{
    header("Location:".WEB_ROOT_URL."h5share/video.php?v=".$videoid);
}