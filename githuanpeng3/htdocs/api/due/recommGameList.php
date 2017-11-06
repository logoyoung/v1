<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 某游戏分类 技能推荐列表
 */

namespace api\due;
include '../../../include/init.php';

use service\due\DueRecGameService;
use service\due\DueRecommService;
use lib\due\DueRecGame;
use GuzzleHttp\json_encode;

class recommGameList
{

    private $skillObj;

    private $page;
    private $size;

    private $game_id;

    public function __construct()
    {
        $this->checkParam();
        $this->skillObj = new DueRecGameService();
    }

    /**
     * 参数初始化
     * --------
     */
    private function checkParam()
    {
        
        /* if (! isset($_POST['gameId'])) {
            $code = DueRecommService::GAME_ERROR_01;
            $desc = DueRecommService::$errorMsg[$code];
            render_error_json($desc, $code);
        } */
        isset($_POST['gameId']) ? $this->game_id = intval($_POST['gameId']) : $this->game_id = 0;
        //if(!isset($_POST['page'])) render_error_json(errorDesc(-4013),-4013);
        $this->page = isset($_POST['page']) && $_POST['page']>0 ? round($_POST['page']) : 1 ;
        $this->size = isset($_POST['size']) && $_POST['size']>0 ? round($_POST['size']) : 10 ;
    }

    /**
     * 通过游戏id 获取技能列表
     * ------------------
     * 
     * @param page、game_id
     * @return array
     */
    private function getGames()
    { 
        return $this->skillObj->getSkillByGameId($this->page, $this->game_id,$this->size);
    }

    /**
     * 返回客户端结果
     * ----------
     * 
     * @return json
     */
    public function display()
    {
        $data = $this->getGames();
        if (empty($data)) {
            /* $code = DueRecommService::SKILL_ERROR_CODE_01;
            $desc = DueRecommService::$errorMsg[$code];
            render_error_json($desc, $code); */
            $data = [];
        }
        $total = $this->skillObj->getGameCountByGameId($this->game_id);
        if(!empty($total)) $total = $total[0]['total'];
        else $total = 0;
        $data = !empty($data) ? $this->filter_datas($data) : [];
        render_json([
            'total'=>$total,
            'page_size'=>$this->size,
            'list' => $data
        ]);
    }
    /**
     * 接口数据返回过滤
     * ------------
     * @return array
     */
    private function filter_datas(array $data){
        $datas = [];
        foreach ($data as $v){
            $arr['skillId'] = $v['skillId'];
            $arr['uid']     = $v['uid'];
            $arr['cert_id'] = $v['cert_id'];
            $arr['game_id'] = $v['game_id'];
            $arr['price']   = $v['price'];
            $arr['unit']    = $v['unit'];
            $arr['order_total'] = $v['order_total']!='' ? $v['order_total'] : 0;
            $arr['game']    = $v['game'];
            $arr['nick']    = $v['nick'];
            $arr['pic']     = $v['pic'];
            $arr['isLiving']= $v['isLiving'];
            $arr['tags']    = $v['tags'];
            $arr['avg_score'] = $v['avg_score']/2;
            $datas[] = $arr;
        }
        unset($data);
        unset($arr);
        return $datas;
    }
}
$obj = new recommGameList();
$obj->display();
?>