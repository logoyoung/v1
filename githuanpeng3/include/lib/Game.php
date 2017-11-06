<?php

namespace lib;

use DBHelperi_huanpeng;
use RedisHelp;
/**
 * 游戏类
 * User: dong
 * Date: 17/3/30
 * Time: 上午9:50
 */
class Game
{
    //上架游戏
    const STATUS_01 = 0;
    //下架游戏
    const STATUS_02 = 1;
    
    private $_db = null; //数据库对象
    private $_conf = null; //数据库对象
    private $_CLASSIFY_GAME = 1; //导航分类推荐游戏
    private $_RECOMMEND_GAME = 2; //游戏分类推荐游戏
    private $_FLOOR_GAME = 3; //楼层游戏

    public function __construct($db = null, $conf = null)
    {
        if ($db)
        {
            $this->_db = $db;
        } else
        {
            $this->_db = new DBHelperi_huanpeng();
        }
        if ($conf)
        {
            $this->_conf = $conf;
        } else
        {
            $this->_conf = $GLOBALS['env-def'][$GLOBALS['env']];
        }
    }

    /**
     * 推荐楼层展示游戏
     *
     * return array(array('gameID'=>'90','gameName'=>'英雄联盟','floor'=>3))
     * gameID ＃游戏id
     * gameName ＃游戏名称
     * floor #层数
     */
    public function getShowGames()
    {
        $list = array();
        $res = $this->_db->field('gameid,number')->where("type=" . $this->_FLOOR_GAME)->select('admin_recommend_game');
        if ($res)
        {
            $gameids = explode(',', $res[0]['gameid']);
            $number = explode(',', $res[0]['number']);
            $gameInfos = $this->_getGameInfo($gameids, $this->_db);
            for ($i = 0, $k = count($gameids); $i < $k; $i++)
            {
                $temp['gameid'] = $gameids[$i];
                $temp['number'] = $number[$i] ? $number[$i] : 2;
                $temp['icon'] = array_key_exists($gameids[$i], $gameInfos) ? $gameInfos[$gameids[$i]]['icon'] : '';
                array_push($list, $temp);
            }
        }
        return $list;
    }

    /**
     * 获取游戏详细信息
     *
     * @param $gamidsArray
     * @param $db
     *
     * @return array
     */
    private function _getGameInfo($gamidsArray, $db)
    {
        $list = array();
        if ($gamidsArray)
        {
            $gameids = implode(',', $gamidsArray);
            $res = $db->where("gameid in ($gameids)")->select('game');
        } else
        {
            $res = $db->where('`status`=' . self::STATUS_01)->select('game');
        }

        if ($res)
        {
            foreach ($res as $v)
            {
                $list[$v['gameid']] = $v;
                $list[$v['gameid']]['icon'] = $v['icon'] ? DOMAIN_PROTOCOL . $this->_conf['domain-img'] . '/' . $v['icon'] : '';
                $list[$v['gameid']]['poster'] = $v['poster'] ? DOMAIN_PROTOCOL . $this->_conf['domain-img'] . '/' . $v['poster'] : '';
                $list[$v['gameid']]['bgpic'] = $v['bgpic'] ? DOMAIN_PROTOCOL . $this->_conf['domain-img'] . '/' . $v['bgpic'] : '';
            }
        }
        return $list;
    }

    /**
     * 获取游戏分类推荐游戏
     *
     * return array('list'=>array(array('gameID'=>'','poster'=>,'gameName'=>)),'total'=>'46');
     * gameID #游戏id
     * poster #封面图
     * gameName #游戏名称
     * total #总游戏数
     */
    public function getRecommendGame()
    {
        $list = array();
        $res = $this->_db->field('gameid')->where("type=" . $this->_RECOMMEND_GAME)->select('admin_recommend_game');
        if ($res)
        {
            $gameids = explode(',', $res[0]['gameid']);
            $gameInfos = $this->_getGameInfo($gameids, $this->_db);
            $liveNumberByGids = $this->getLiveCountByGid($gameids, $this->_db);
            for ($i = 0, $k = count($gameids); $i < $k; $i++)
            {
                $temp['gameid'] = $gameids[$i];
                $temp['name'] = array_key_exists($gameids[$i], $gameInfos) ? $gameInfos[$gameids[$i]]['name'] : '';
                $temp['poster'] = array_key_exists($gameids[$i], $gameInfos) ? $gameInfos[$gameids[$i]]['poster'] : '';
                $temp['liveCount'] = array_key_exists($gameids[$i], $liveNumberByGids) ? $liveNumberByGids[$gameids[$i]] : 0;
                array_push($list, $temp);
            }
        }
        return array('list' => $list, 'total' => $this->getGameNumber());
    }

    /**
     * 根据游戏名称获取游戏id和游戏类型
     *
     * @param string $gameName 游戏名称
     * @param object $db
     *
     * return array   array('gametid'=>'','name'=>,'gameid'=>);
     */
    public static function getGameIdAndTypeByName($gameName, $db)
    {
        if (empty($gameName))
        {
            return false;
        }
        $res = $db->field('gameid,name,gametid')->where("name='$gameName'")->limit(1)->select('game');
        if ($res)
        {
            return array('gameid' => $res[0]['gameid'], 'name' => $res[0]['name'], 'gametid' => $res[0]['gametid']);
        } else
        {
            return array();
        }
    }

    /**
     * 获取导航分类推荐游戏
     *
     * return array(array('gameID'=>'','gameName'=>));
     * gameID #游戏id
     * gameName #游戏名称
     */
    public function getClassifyRecommendGame()
    {
        $list = array();
        $res = $this->_db->field('gameid')->where("type=" . $this->_CLASSIFY_GAME)->select('admin_recommend_game');
        if ($res)
        {
            $gameids = explode(',', $res[0]['gameid']);
            $gameInfos = $this->_getGameInfo($gameids, $this->_db);
            for ($i = 0, $k = count($gameids); $i < $k; $i++)
            {
                $temp['gameid'] = $gameids[$i];
                $temp['name'] = array_key_exists($gameids[$i], $gameInfos) ? $gameInfos[$gameids[$i]]['name'] : '';
                $temp['poster'] = array_key_exists($gameids[$i], $gameInfos) ? $gameInfos[$gameids[$i]]['poster'] : '';
                array_push($list, $temp);
            }
        }
        return $list;
    }

    /**
     * 根据游戏id获取游戏详情
     *
     * @param  string $gameid 游戏id
     *
     * @return array|bool|mixed
     */
    public function getGameInfoByGameId($gameid)
    {
        if (empty($gameid))
        {
            return false;
        }
        $res = $this->_getGameInfo(array($gameid), $this->_db);
        if ($res)
        {
            return $res[$gameid];
        } else
        {
            return array();
        }
    }

    /**
     * 获取平台总共有多少款游戏
     *
     * @return int 总游戏数量
     */
    public function getGameNumber()
    {
        $res = $this->_db->field('count(*) as total')->where('`status` = ' . self::STATUS_01)->select("game");
        if ($res)
        {
            return $res[0]['total'];
        } else
        {
            return 0;
        }
    }

    /**
     * 获取所有游戏列表
     *
     * return 返回二维数组  array(array('gameID'=>'','poster'=>,'gameName'=>,'liveTotal'=>));
     * gameID #游戏id
     * poster #封面图
     * gameName #游戏名称
     * liveTotal #该游戏正在直播数
     */
    public function getGameList()
    {
        $list = array();
        $res = $this->_getGameInfo(array(), $this->_db);
        if ($res)
        {
            $liveNumberByGids = $this->getLiveCountByGid(array_keys($res), $this->_db);
            foreach ($res as $v)
            {
                $list[$v['gameid']] = $v;
                $list[$v['gameid']]['liveCount'] = array_key_exists($v['gameid'], $liveNumberByGids) ? $liveNumberByGids[$v['gameid']] : 0;
            }
        }
        return $list;
    }

    /**
     * 根据游戏id获取正在直播的数量
     *
     * @param array $gameidsArray 游戏id 多个的话用数组
     *
     * return array array('90'=>20); key游戏id value 直播数
     */
    public static function getLiveCountByGid($gameidsArray,$db)
    {
        $list = array();
        if (empty($gameidsArray) || !is_array($gameidsArray))
        {
            return false;
        }
        $gameids = implode(',', $gameidsArray);
        $res = $db->field('gameid,count(*) as  total')->where("gameid in ($gameids)  and status=" . LIVE . " group by gameid")->select('live');
        if ($res)
        {
            foreach ($res as $v)
            {
                $list[$v['gameid']] = $v['total'];
            }
        }
        return $list;
    }

    /**
     * 根据游戏id获取已发布录像的数量
     *
     * @param array $gameidsArray 游戏id 如查询多个时 如:array(45,35)
     *
     * return array array('90'=>20); key游戏id value 录像数
     */
    public static function getVideoCountByGid($gameidsArray, $db)
    {
        $list = array();
        if (empty($gameidsArray) || !is_array($gameidsArray))
        {
            return false;
        }
        $gameids = implode(',', $gameidsArray);
        $res = $db->field('gameid,count(*) as  total')->where("gameid in ($gameids)  and status=" . VIDEO . " group by gameid")->select('video');
        if ($res)
        {
            foreach ($res as $v)
            {
                $list[$v['gameid']] = $v['total'];
            }
        }
        return $list;
    }

    /**
     * 通过gameid获取game信息
     * -------------------
     * @param unknown $gamidsArray
     * @param unknown $db
     */
    public function getGameInfo($gamidsArray, $db)
    {
        return $this->_getGameInfo(array_unique($gamidsArray), $db);
    }

    /**
     * 获取游戏类型名称
     * @param int $gametid
     * @param object $db 
     * @return string
     */
    public function getGameTypeName($gametid,$db)
    {
        $gtname = '';
        $name = $db->field('name')->where('gametid=' . $gametid . '')->limit('1')->select('gametype');
        if ($name)
        {
            $gtname = $name[0]['name'];
        }
        return $gtname;
    }

    /**
     * 获取所有游戏id
     * @param object $db 
     * @return array|boolean
     */
    public function getGameIds($db)
    {
        return $db->field('gameid')->select('game');
    }


    public static function getDB()
    {
        return new DBHelperi_huanpeng();
    }

    public static function getRedis()
    {
        return new RedisHelp();
    }

}
