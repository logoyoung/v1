<?php

/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 热门推荐 通过page翻页实现换一批
 */
namespace api\due;

include '../../../include/init.php';
use service\due\DueRecommService;

class hotRecomm
{

    private $recommendService;

    private $page;

    public function __construct()
    {   
        $this->recommendService = new DueRecommService();
    }

    /**
     * 通过技能ID 获取技能相关信息
     * --------------------
     * 
     * @return array
     */
    public function getSkillInfo()
    {
        return $this->recommendService->getSkillInfo();
    }

    /**
     * 返回接口结果
     * ----------
     */
    public function display()
    {
        $datas = $this->getSkillInfo();
        if (empty($datas)) {
            /* $code  = DueRecommService::SKILL_ERROR_CODE_01;
            $desc = DueRecommService::$errorMsg[$code];
            render_error_json($desc, $code);*/
            $datas = [];
        }
        //$datas = !empty($datas) ? $datas : [];
        if($_POST['a'] == 10){
            render_json([
                'list' => []
            ]);
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
        foreach ($data as $v){
            $arr['skillId'] = $v['skillId'];
            $arr['uid']     = $v['uid'];
            $arr['cert_id'] = $v['cert_id'];
            $arr['game_id'] = $v['game_id'];
            $arr['price']   = $v['price'];
            $arr['unit']    = $v['unit'];
            $arr['order_total'] = $v['order_total'];
            $arr['game']    = $v['game'];
            $arr['nick']    = $v['nick'];
            $arr['pic']     = $v['pic'];
            $arr['isLiving']= $v['isLiving'];
            $datas[] = $arr;
        }
        unset($data);
        unset($arr);
        return $datas;
    }
}

$obj = new hotRecomm();
$obj->display();
