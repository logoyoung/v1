<?php
// +----------------------------------------------------------------------
// | Anchor Info
// +----------------------------------------------------------------------
// | Author: zwq
// +----------------------------------------------------------------------
namespace HP\Op;
class Game extends \HP\Cache\Proxy{
    
    static $recommendType = [1=>"导航栏游戏分类推荐",2=>"游戏分类推荐",3=>"首页游戏推荐楼层",4=>"App楼层推荐游戏"];
    /**获取游戏类型map
     * @param $db
     */
    static function getTypeinfo(){
        $db = D('Gametype');
        $map = $db->getField('gametid,name,icon');
        return $map;
    }
    
    /**获取经纪公司
     * @param $db
     */
    static function getGames(){
        $db = D('Game');
        $map = $db->getField('gameid,gametid,name,poster');
        return $map;
    }
    
    /**获取推荐信息
     * @param $db
     */
    static function getRecommendGame(){
        $gameRecommendDB = D('RecommendGame');
        $map = $gameRecommendDB->getField("type,gameid,number");
        return $map;
    }
    
    /**格式化游戏推荐信息
     * @param $db
     */
    static function formatRecommendGame($recommendData){
        $datas = null;
        if($recommendData){
            foreach ($recommendData as $type=>$typeData){
                $data = $typeData;
                $data['gameid'] && $data['gameids'] = explode(',', $data['gameid']);
                $data['number'] && $data['numbers'] = explode(',', $data['number']);
                if($data['gameids'] && $data['numbers'] ) {
                    $data['gameidNumber'] = array_combine($data['gameids'], $data['numbers']);
                }
                $data['recommendNumber'] = count($data['gameids']);
                $datas[$type] = $data;
            }
        }
        return $datas;
    }
    
    static function updateRecommend($type,$gameid,$num){
        $gameRecommendDB = D('RecommendGame');
        $data['gameid'] = $gameid;
        $data['number'] = $num;
        $data['type'] = $type;
        $data['utime'] = get_date();
        $res = $gameRecommendDB->where(["type"=>$type])->save($data);
        return $res;
    }

	static public function gameInfo($gidarray){
		$db = D('Game');
		if(is_array($gidarray)){
			$gidarray=implode(',',$gidarray);
			$where['gameid'] = ['in',$gidarray];
		}
		$res= $db->field("gameid,gametid,name")->where($where)->getField('gameid,gametid,name,poster');
		return $res?$res:array();
	}

	public static function  checkIsExistByType($type){
		$Dao=D('admin_recommend_game');
		$res=$Dao->where("type=$type")->select();
		if($res){
			return true;
		}else{
			return false;
		}
	}

	public  static function  addRecomgameByType($type,$gameid,$num){
		$Dao=D('admin_recommend_game');
		$data['type']=$type;
		$data['gameid']=$gameid;
		$data['number']=$num;
		$data['utime']=date("Y-m-d H:i:s");
		$res=$Dao->add($data);
		if($res){
			return true;
		}else{
			return false;
		}
	}
    
}