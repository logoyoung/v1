<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:54
 */
include '../../../include/init.php';
use service\due\DueCertService;
/**
 * 陪玩游戏分类
 * Class gameList
 */
class gameList
{
    const GAME_DOWN = 2;

    //先手动配置游戏列表以后后台编辑
    public $RecommendGameList = [
        ['gameId'=>'190','gameName'=>'王者荣耀'],
        ['gameId'=>'215','gameName'=>'穿越火线'],
        ['gameId'=>'150','gameName'=>'球球大作战'],
        ['gameId'=>'465','gameName'=>'天天狼人杀'],
      //  ['gameId'=>'8','gameName'=>'炉石传说']
        ];
    public function getGameList()
    {
        $res = [];
        $CertService = new DueCertService();
        $data = $CertService->getDueGameList();
        foreach($data as $key=>$value)
        {
            //过滤下架的应用
            if($value['status'] != self::GAME_DOWN )
            {
                $res[$key]['gameId'] = $value['gameid'];
                $res[$key]['gameName'] = $value['name'];
            }
        }
        return $res;
    }
    public function display()
    {
        //后台配置好开启
         $list['list'] = $this->getGameList();
        //$list['list'] = $this->RecommendGameList;
        $list['total'] = count($list['list']);
        render_json($list);
    }
}
$obj = new gameList();
$obj->display();