<?php
/**
 * yalong
 * 陪玩页面推荐分类
 */
namespace service\due;

use lib\due\DueRecGame;
class DueRecommendGame
{
    private $gameObj;
    const GAME_ORDER_01 = 1;//ord 字段升序排
    const REC_GAME_ERROR_CODE_01 = -1000;
    public static $errorMsg =[
        self::REC_GAME_ERROR_CODE_01 =>'加载完毕'
    ];
     
    /**
     * 获取约玩游戏分类
     * ------------
     */
    public function getGames(){
        $this->gameObj = new DueRecGame();
        $this->gameObj->setSkillOrd(self::GAME_ORDER_01);
        return $this->gameObj->getGame();
    }
    /**
     * 按评分平均分倒序排
     * -------------
     */
    /**
     * 新人推荐列表 按注册时间
     * ----------------
     * @return array
     */
    public function getNewUsers(){
        
    }
}

?>