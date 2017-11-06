<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 陪玩页面 多游戏分类下技能列表 按评分平均分排序
 */
namespace api\due;
include '../../../include/init.php';
use service\due\DueRecGameService;

class dueIndex
{

    private $recomendGame;

    private $page;

    public function __construct()
    {
        //if(!isset($_POST['page'])) render_error_json(errorDesc(-4013),-4013);
        $this->page = isset($_POST['page']) && $_POST['page']>0 ? round($_POST['page']) : 1 ;
        $this->recomendGame = new DueRecGameService();
    }

    /**
     * 获取推荐的游戏分类
     * -------------
     * 
     * @return array
     */
    private function getRecGame()
    {
        return 
        $this->recomendGame->getGamesByComment($this->page,0);
    }

    /**
     * 返回接口结果
     * ---------
     * 
     * @return json
     */
    public function display()
    {
        $datas = $this->getRecGame();
        if (empty($datas)) {
            /* $code = DueRecGameService::REC_GAME_ERROR_CODE_01;
            $desc = DueRecGameService::$errorMsg[$code];
            render_error_json($desc, $code); */
            $datas = [];
        }  
        $datas = $this->filter_datas($datas);
        render_json([
            'list' => $datas
        ]);
    }
    /**
     * 接口数据返回过滤
     * ------------
     * @return array
     */
    private function filter_datas(array $data){
        $datas = [];
        foreach ($data as $k=>$v){
            $datas[$k]['gameid'] = $v['gameid'];
            $datas[$k]['name']   = $v['name'];
            $datas[$k]['icon']   = $v['icon'];
            $datas[$k]['iconx']   = $v['iconx'];
            foreach($v['list'] as $ko=>$vo){
                $datas[$k]['list'][$ko]['skillId'] = $vo['skillId'];
                $datas[$k]['list'][$ko]['uid']     = $vo['uid'];
                $datas[$k]['list'][$ko]['cert_id'] = $vo['cert_id'];
                $datas[$k]['list'][$ko]['game_id'] = $vo['game_id'];
                $datas[$k]['list'][$ko]['price']   = $vo['price'];
                $datas[$k]['list'][$ko]['unit']    = $vo['unit'];
                $datas[$k]['list'][$ko]['order_total'] = $vo['order_total'];
                $datas[$k]['list'][$ko]['game']    = $vo['game'];
                $datas[$k]['list'][$ko]['nick']    = $vo['nick'];
                $datas[$k]['list'][$ko]['pic']     = $vo['pic'];
                $datas[$k]['list'][$ko]['isLiving']= $vo['isLiving'];
            }
        }
        return $datas;
    }
}

$obj = new dueIndex();
$obj->display();

?>