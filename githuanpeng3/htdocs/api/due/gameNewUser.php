<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 新技能列表 最新按时间倒序
 */
namespace api\due;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include '../../../include/init.php';
use service\due\DueRecGameService;
use service\due\DueRecommService;

class gameNewUser
{

    private $skillObj;

    private $page;
    private $size;

    public function __construct()
    {
        //if(!isset($_POST['page'])) render_error_json(errorDesc(-4013),-4013);
        $this->page = isset($_POST['page']) && $_POST['page']>0 ? round($_POST['page']) : 1 ;
        $this->size = isset($_POST['size']) && $_POST['size']>0 ? round($_POST['size']) : 10 ;
        if (! isset($_POST['gameId'])) {
            $code = DueRecommService::GAME_ERROR_01;
            $desc = DueRecommService::$errorMsg[$code];
            render_error_json($desc, $code);
        }
        $this->game_id = intval($_POST['gameId']);
        $this->skillObj = new DueRecGameService();
    }

    /**
     * 获取最新技能列表
     * -----------
     * 
     * @return array
     */
    private function getNewSkills()
    {
        return $this->skillObj->getNewUsers($this->page,$this->game_id,$this->size);
    }

    /**
     * 设置查询排序规则
     * ------------
     * 
     * @return string
     */
    private function setOrderWay()
    {
        return "order by ctime desc";
    }

    /**
     * 通过技能id 查询内容列表
     * ------------------
     * 
     * @return Ambigous <multitype:, boolean, multitype:multitype: >
     */
    public function getNewSkillInfo()
    {
        $data = $this->getNewSkills();
        $recSkillObj = new DueRecommService();
        $order = $this->setOrderWay();
        return $recSkillObj->getSkillInfos($data, $order);
    }

    /**
     * 返回接口结果
     * ---------
     * 
     * @return json
     */
    public function display()
    {
        $data = $this->getNewSkillInfo();
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
            $arr['order_total'] = $v['order_total'];
            $arr['game']    = $v['game'];
            $arr['nick']    = $v['nick'];
            $arr['pic']     = $v['pic'];
            $arr['isLiving']= $v['isLiving'];
            $arr['tags']    = $v['tags'];
            $arr['avg_score'] = $v['avg_score'];
            $datas[] = $arr;
        }
        unset($data);
        unset($arr);
        return $datas;
    }
}

$obj = new gameNewUser();
$obj->display();

?>