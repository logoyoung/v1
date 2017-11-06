<?php
/**
 * 首页展示游戏列表
 * yandong@6rooms.com
 * Date 2016-07-13 18:09
 */
include "../../includeAdmin/init.php";
$db = new DBHelperi_admin();

function  getShowGameList($db){
    $res=$db->field('gameid')->order("ctime ASC")->select('index_recommend_game');
    if($res){
        return array_column($res, 'gameid');
    }else{
        return array();
    }
}
$res=getShowGameList($db);
succ($res);


