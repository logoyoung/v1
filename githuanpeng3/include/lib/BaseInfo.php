<?php

namespace lib;

use \DBHelperi_huanpeng;
use RedisHelp;

/**
 * 基础信息类
 * Created by PhpStorm.
 * User: dong
 * Date: 17/4/10
 * Time: 下午5:31
 */
class BaseInfo
{

    private $_db = ''; //数据库对象
    private $_conf = ''; //配置数组
    public $redisObj = ''; //redis对象

    const INFORMATION_TYPE_All = 0; //轮播&列表
    const INFORMATION_TYPE_PICTUR = 1; //轮播
    const INFORMATION_TYPE_LIST = 2; //列表
    const INFORMATION_CLIENT_APP = 1; //请求端为app
    const INFORMATION_CLIENT_WEB = 2; //请求端为web
    const INDEX_CAROUSEL_LIVE_COUNT = 6;//首页轮播直播数

    public function __construct($db = '', $conf = '', $redis = '')
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
        if ($redis)
        {
            $this->redisObj = $redis;
        } else
        {
            $this->redisObj = new RedisHelp();
        }
    }

    /**
     * 获取首页咨询列表
     *
     * @param int    $type   1轮播 2列表
     * @param int    $client 1app  2web
     * @param object $db
     *
     * @return array|bool
     */
    public function getRecommemdInfo($type, $client)
    {
        if ($type == self::INFORMATION_TYPE_All)
        {//轮播&&列表
            $id = 'id in (1,2)';
        }
        if ($type == self::INFORMATION_TYPE_PICTUR)
        {//轮播
            $id = 'id in (1)';
        }
        if ($type == self::INFORMATION_TYPE_LIST)
        {//列表
            $id = 'id in (2)';
        }
        $res = $this->_db->field('id,list')->where("$id  and  client=$client ")->select('recommend_information');
        if ($res !== false)
        {
            if (empty($res))
            {
                return [];
            } else
            {
                foreach ($res as $v)
                {
                    $temp[$v['id']] = $v['list'];
                }
                return $temp;
            }
        } else
        {
            return false;
        }
    }

    /*
     * 根据资讯id获取标题图片
     *
     * @param string $ids  资讯id
     * @param $db
     *
     * @return array|bool
     */

    public function getInfoListByIds($ids)
    {
        if (empty($ids))
        {
            return false;
        }
        $nowTime = date('Y-m-d H:i:s');
        $res = $this->_db->field('id,tid,title,poster,url,is_login,thumbnail')->where("id  in ($ids) AND stime<='$nowTime' AND etime>='$nowTime'")->select('admin_information');
        if (false !== $res)
        {
            if (empty($res))
            {
                return [];
            } else
            {
                foreach ($res as $v)
                {
                    $temp[$v['id']] = $v;
                }
                return $temp;
            }
        } else
        {
            return false;
        }
    }

    /**
     * 获取资讯类型
     *
     * @return array|bool
     */
    public function getInformationType()
    {
        $res = $this->_db->field('id,name')->select('admin_information_type');
        if (false !== $res)
        {
            if ($res)
            {
                foreach ($res as $v)
                {
                    $temp[$v['id']] = $v['name'];
                }
                return $temp;
            } else
            {
                return [];
            }
        } else
        {
            return false;
        }
    }

 
    /**
     * 获取首页推荐视频ID
     *
     * @param int $client
     *
     * @return array
     */
    private function _getRecommendLiveLists($client, $db)
    {
        $rows = $db->field('list')->where('client=' . $client)->select('recommend_live');
        if ($rows !== false && !empty($rows))
        {
            $rows = $rows[0]['list'];
        } else
        {
            $rows = [];
        }
        return $rows;
    }

    /**
     * 根据主播ID获取海报图
     *
     * @param array  $uids
     * @param object $db
     *
     * @return array
     */
    private function _getInfoByUidList($uids, $db)
    {
        if (empty($uids))
        {
            return false;
        }
        $res = $db->field('uid,poster')->where("status=1 and uid in ($uids)")->select('admin_recommend_live');
        if (false !== $res && !empty($res))
        {
            foreach ($res as $v)
            {
                $temp[$v['uid']] = $v['poster'];
            }
            return $temp;
        } else
        {
            return [];
        }
    }

    /**
     *  获取已推荐主播最后一次的直播信息
     *
     * @param string $uids 主播id串
     * @param object $db
     *
     * @return array|bool
     */
    private function _getRecommendAnchorLast($uids, $db)
    {
        if (empty($uids))
        {
            return false;
        }
        $sql = "select liveid,poster,uid,`status`,stream,`server`,orientation from live where uid in ($uids) and `status`= " . LIVE . " order by liveid desc";

        $res = $db->doSql($sql);
        if (false !== $res && !empty($res))
        {
            foreach ($res as $v)
            {
                $lives[$v['uid']] = $v;
            }
            return $lives;
        } else
        {
            return [];
        }
    }

    
    /**
     *  获取主播直播信息
     *
     * @param string $uids 主播id串
     *
     * @return array|bool
     */
    public function getAnchorLive($uids)
    {
        if (empty($uids))
        {
            return false;
        }

        $sql = "select liveid,poster,uid,status,stream,server,orientation from (select * from live where uid in ($uids) order by liveid  desc) live  group by uid order by liveid desc";
        $res = $this->_db->doSql($sql);
        if ($res)
        {
            foreach ($res as $v)
            {
                $lives[$v['uid']] = $v;
            }
            return $lives;
        } else
        {
            return [];
        }
    }
    
    /**
     * 获取首页轮播视频列表
     *
     * @param type $client
     *
     * @return array
     */
    public function RecommendLiveLists($client = self::INFORMATION_CLIENT_WEB)
    {
        $recommend = $this->_getRecommendLiveLists($client, $this->_db);

        $res = [];
        if ($recommend)
        {
            $recommend = explode(',', $recommend);
            $recommend = implode(',', array_values_to_string($recommend));
            $res = $this->_getRecommendAnchorLast($recommend, $this->_db);            
        }

        return $res;
    }

    /**
     *  获取待推荐主播
     * @return array
     */
    public function getWaitForRecommend()
    {
        return $this->_db->field('uid')->where('status=0')->select('admin_recommend_live');
    }
    
    /**
     * 获取最近的8条历史纪录
     *
     * @param int    $uid
     * @param object $db
     *
     * @return array
     */
    private function _gethistory($uid, $db)
    {
        $hluid = [];
        $historyluids = $db->field('luid')->order('stime DESC')->limit(10)->where('uid=' . $uid)->select('history');
        foreach ($historyluids as $historyluid)
        {
            $hluid[] = $historyluid['luid'];
        }
        return $hluid;
    }

    /**
     * 随机获取8位正在直播中的主播
     *
     * @param object $db
     *
     * @return array
     */
    private function _getLiveUid($db, $size)
    {
        $arr = [];
        if (SHOREW_DEBUG)
        {
            $uids = $db->field('uid')->order('rand()')->limit($size)->select('live');
        } else
        {
            $uids = $db->field('uid')->where('status=' . LIVE)->order('rand()')->limit($size)->select('live');
        }
        foreach ($uids as $uid)
        {
            $arr[] = $uid['uid'];
        }
        return $arr;
    }

    /**
     * 随机获取8个已关注的游戏
     *
     * @param int    $uid
     * @param object $db
     *
     * @return array
     */
    private function _gamefollow($uid, $db)
    {
        $gameids = $db->field('gameid')->where('uid=' . $uid)->select('gamefollow');
        return $gameids;
    }

    /**
     * 获取符合已关注游戏类型的直播
     *
     * @param int    $gameid
     * @param object $db
     *
     * @return array
     */
    private function _getLiveByGameid($gameid, $db)
    {
        $listarray = $gid = $luids = [];
        foreach ($gameid as $gv)
        {
            $gid[] = $gv['gameid'];
        }
        $gids = implode(',', $gid);
        if (SHOREW_DEBUG)
        {
            $luid = $db->field('uid')->where('gameid in (' . $gids . ')')->select('live');
        } else
        {
            $luid = $db->field('uid')->where('status =' . LIVE . ' and  gameid in (' . $gids . ')')->select('live');
        }
        foreach ($luid as $luidv)
        {
            $luids[] = $luidv['uid'];
        }
        return $luids;
    }

    /**
     * 根据uid查询主播是否直播过
     * 
     * @param int $luid 
     * @return int|boolean 
     */
    
    public function hasLive(int $luid)
    {
        return $this->_db->field('count(*)')->where('uid=' . $luid)->select('live');
    }

    /**
     * 获取猜到的数据
     *
     * @param type $luids
     * @param type $size
     * @param type $db
     *
     * @return type
     */
    private function _getGuessData($luids, $size, $db)
    {
        if (SHOREW_DEBUG)
        {
            $rows = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation')
                            ->where('uid in (' . $luids . ')')
                            ->order('rand()')->limit($size)->select('live');
        } else
        {
            $rows = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation')
                            ->where('status=' . LIVE . ' and uid in (' . $luids . ')')
                            ->order('rand()')->limit($size)->select('live');
        }

        return $rows;
    }

    /**
     *
     * 猜你喜欢,获取八条数据
     * @param int $size
     * @return array
     */
    public function getGuessLiveLists($uid, $size)
    {
        if (!empty($uid))
        {
            $total = [];
            $gameid = $this->_gamefollow($uid, $this->_db);
            $luids = '';
            if (!empty($gameid))
            {
                $luids = $this->_getLiveByGameid($gameid, $this->_db);
            }
            $historyluid = $this->_gethistory($uid, $this->_db);
            if (!empty($luids))
            {
                $total = array_merge($total, $luids);
            }
            if (!empty($historyluid))
            {
                $total = array_merge($total, $historyluid);
            }
            if (count(array_unique($total)) >= $size)
            {
                $luidlistss = array_rand($total, $size);
                if ($luidlistss)
                {
                    $ids = implode(',', $luidlistss);
                    $rows = $this->_getGuessData($ids, $size, $this->_db);
                } else
                {
                    $rows = [];
                }
                if (count($rows) < $size)
                {
                    if (!empty($rows))
                    {
                        foreach ($rows as $k => $v)
                        {
                            $rowsRes[$k] = $v['uid'];
                        }
                        $uids = $this->_getLiveUid($this->_db, $size);
                        $total = array_merge($rowsRes, $uids);
                        $luidlists = array_rand(array_flip(array_unique($total)), $size);
                        if ($luidlists)
                        {
                            $ids = implode(',', $luidlists);
                            $rows = $this->_getGuessData($ids, $size, $this->_db);
                        } else
                        {
                            $rows = [];
                        }
                    } else
                    {
                        $uids = $this->_getLiveUid($this->_db, $size);
                        if ($uids)
                        {
                            $ids = implode(',', $uids);
                            $rows = $this->_getGuessData($ids, $size, $this->_db);
                        } else
                        {
                            $rows = [];
                        }
                    }
                }
            } else
            {
                $uids = $this->_getLiveUid($this->_db, $size);
                $luidlists = array_merge($total, $uids);
                if ($luidlists)
                {
                    $ids = implode(',', $luidlists);
                    $rows = $this->_getGuessData($ids, $size, $this->_db);
                } else
                {
                    $rows = [];
                }
            }
        } else
        {
            $ids = $this->_getLiveUid($this->_db, $size);
            if ($ids)
            {
                $ids = implode(',', $ids);
                $rows = $this->_getGuessData($ids, $size, $this->_db);
            } else
            {
                $rows = [];
            }
        }
        return $rows;
    }

    public static function getRedis()
    {
        return new RedisHelp();
    }
}
