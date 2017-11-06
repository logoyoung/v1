<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:54
 */
include '../../../include/init.php';
use service\anchor\AnchorApplyService;
/**
 * 公司签约游戏分类
 * Class gameList
 */
class gameList
{
    const GAME_DOWN = 2;
    //先手动配置游戏列表以后后台编辑
    public $RecommendGameList = [
        ['gameId'=>'190','gameName'=>'王者荣耀'],
        ['gameId'=>'215','gameName'=>'穿越火线CF'],
        ['gameId'=>'150','gameName'=>'球球大作战'],
        ['gameId'=>'500','gameName'=>'龙之谷'],
        ['gameId'=>'465','gameName'=>'天天狼人杀'],
        ['gameId'=>'455','gameName'=>'阴阳师'],
        ['gameId'=>'135','gameName'=>'火影忍者'],
        ['gameId'=>'160','gameName'=>'全民枪战'],
        ['gameId'=>'62','gameName'=>'天天酷跑'],
        ['gameId'=>'195','gameName'=>'我的世界'],
        ['gameId'=>'525','gameName'=>'迷你世界'],
        ['gameId'=>'523','gameName'=>'弹弹堂'],
      //  ['gameId'=>'8','gameName'=>'炉石传说']
        ];
/*    public function getGameList()
    {
        $res = [];
        $CertService = new \service\anchor\AnchorApplyService();
        $data = $CertService->getGameList();
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
    }*/
    public function display()
    {
        //后台配置好开启
        // $list['list'] = $this->getGameList();
        $list['list'] = $this->RecommendGameList;
        $list['total'] = count($list['list']);
        render_json($list);
    }
}
$obj = new gameList();
$obj->display();