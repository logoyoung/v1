<?php

/**
 * 缓存类
 * data 2016-06-07 14:19
 * author yandong@6rooms.com
 */
require 'init.php';
require_once 'redis.class.php';

class Cache {

    private $db;
    private $redis;
    private $liveUser = "LIVE_USER"; //在线人数
    private $liveCtime = "LIVE_CTIME"; //开播时间
    private $liveFans = "LIVE_FANS"; //粉丝数
    private $videoView = "VIDEO_VIEW"; //观看次数
    private $videoCtime = "VIDEO_CTIME"; //发布时间
    private $videoCollect = "VIDEO_COLLECT"; //收藏人数

    public function __construct() {
        if ($this->db === null) {
            $this->db = new DBHelperi_huanpeng();
        }
        if ($this->redis === null) {
            $this->redis = new redishelp();
        }
    }

    /**
     * 根据主播ID和录像id5获取游戏id
     * @param type $lvid 主播id || 录像id
     * @param type $type  主播 [0] || 录像[1]
     */
    private function getGameId($lvid, $type) {
        if (empty($lvid)) {
            return false;
        }
        if ($type == 0) {
            $res = $this->db->field('gameid')->where("uid=$lvid and status=" . LIVE)->select('live');
        }
        if ($type == 1) {
            $res = $this->db->field('gameid')->where("videoid=$lvid  and status=" . VIDEO)->select('video');
        }
        return $res ? $res[0]['gameid'] : '';
    }

    /**
     * 进入直播间+在线人数
     * @param type $luid  主播id
     * @return type float
     */
    public function enterLiveRoom($luid) {
        $gameid = $this->getGameId($luid, 0);
        if ($gameid) {
            $gkey = $this->liveUser . ':' . $gameid;
            $this->redis->zincrby($gkey, 1, $luid);
        }
        $key = $this->liveUser;
        return $this->redis->zincrby($key, 1, $luid);
    }

    /**
     * 退出直播间-在线人数
     * @param type $luid  主播id
     * @return type float
     */
    public function quitLiveRoom($luid) {
        $gameid = $this->getGameId($luid, 0);
        if ($gameid) {
            $gkey = $this->liveUser . ':' . $gameid;
            $this->redis->zincrby($gkey, -1, $luid);
        }
        $key = $this->liveUser;
        return $this->redis->zincrby($key, -1, $luid);
    }

    /**
     * 关注主播时,关注数+1
     * @param type $luid 主播id
     */
    public function userFollow($luid) {
        $key = $this->liveFans;
        $this->redis->zincrby($key, 1, $luid);
    }

    /**
     * 取消关注时,关注数-1
     * @param type $luid
     */
    public function cancelFollow($luid) {
        $key = $this->liveFans;
        $this->redis->zincrby($key, -1, $luid);
    }

    /**
     * 开始直播
     * @param type $luid
     * @return type
     */
    public function startLive($luid, $gameid) {
        if (empty($gameid)) {
            $gameid = $this->getGameId($luid, 0);
        }
        if ($gameid) {
            $ctime_key = $this->liveCtime . ':' . $gameid;
            $this->redis->zadd($ctime_key, time(), $luid);
        }
        $key = $this->liveCtime;
        return $this->redis->zadd($key, time(), $luid);
    }

    /**
     * 结束直播的时候把该主播对应的数据从集合中删掉
     * @param type $luid  主播id
     * @param type $gameid
     */
    public function endtLive($luid, $gameid) {
        if (empty($gameid)) {
            $gameid = $this->getGameId($luid, 0);
        }
        if ($gameid) {
            $user_key = $this->liveUser . ':' . $gameid;
            $ctime_key = $this->liveCtime . ':' . $gameid;
            $this->redis->zRem($user_key, $luid);
            $this->redis->zRem($ctime_key, $luid);
        }
        $ukey = $this->liveUser;
        $ckey = $this->liveCtime;
        $this->redis->zRem($ukey, $luid);
        $this->redis->zRem($ckey, $luid);
    }

    /**
     * 录像观看次数+1
     * @param type $videoid 录像id
     */
    public function videoView($videoid) {
        $gameid = $this->getGameId($videoid, 1);
        $gkey = $this->videoView . ':' . $gameid;
        $this->redis->zincrby($gkey, 1, $videoid);
    }

    /**
     * 录像收藏+1
     * @param type $videoid 录像id
     */
    public function videoCollect($videoid) {
        $gameid = $this->getGameId($videoid, 1);
        $collect_key = $this->videoCollect . ':' . $gameid;
        $this->redis->zincrby($collect_key, 1, $videoid);
    }

    /**
     * 录像收藏-1
     * @param type $videoid 录像id
     */
    public function videoCancel($videoid) {
        $gameid = $this->getGameId($videoid, 1);
        $collect_key = $this->videoCollect . ':' . $gameid;
        $this->redis->zincrby($collect_key, -1, $videoid);
    }

    /**
     * 审核通过时发布时间+1条数据
     * @param type $luid
     * @return type
     */
    public function videoPass($videoid) {
        $gameid = $this->getGameId($videoid, 1);
        $ctime_key = $this->videoCtime . ':' . $gameid;
        $this->redis->zincrby($ctime_key, 1, $videoid);
    }

    /**
     * 删除录像的时候删除集合中对应的数据
     * @param type $videoid  录像id
     */
    public function videoDelete($videoid) {
        $gameid = $this->getGameId($videoid, 1);
        $ctime_key = $this->videoCtime . ':' . $gameid;
        $collect_key = $this->videoCollect . ':' . $gameid;
        $view_key = $this->videoView . ':' . $gameid;
        $this->redis->zRem($ctime_key, $luid);
        $this->redis->zRem($collect_key, $luid);
        $this->redis->zRem($view_key, $luid);
    }

    /*
     * 获取最热的直播[在线人数]
     * @param int $lastid  主播id
     * @param int $gameid  游戏id
     *  @param int $page   页数
     * @param int $size    请求数
     * @return array()
     */

    public function getHotLive($lastid, $gameid, $page, $size) {
        $res = array();
        if ($gameid) {
            $key = $this->liveUser . ':' . $gameid;
        } else {
            $key = $this->liveUser;
        }
        if ($lastid) {
            $position = $this->redis->zrevrank($key, $lastid); //获取当前位置
            $res['res'] = $this->redis->zRevRange($key, $position + 1, $position + $size, true);
        } else {
            $offset = ($page - 1) * $size;
            $res['res'] = $this->redis->zRevRange($key, $offset, ($offset + $size) - 1, true);
        }
        $res['count'] = $this->getCount($key); //获取总数
        return $res;
    }

    /**
     * 获取最新的直播[开播时间]
     * @param type $luid
     * @return type
     */
    public function getNewLive($lastid, $gameid, $page, $size) {
        $res = array();
        if ($gameid) {
            $key = $this->liveCtime . ':' . $gameid;
        } else {
            $key = $this->liveCtime;
        }
        if ($lastid) {
            $position = $this->redis->zrevrank($key, $lastid); //获取当前位置
            $res['res'] = $this->redis->zRevRange($key, $position + 1, $position + $size, true);
        } else {
            $offset = ($page - 1) * $size;
            $res['res'] = $this->redis->zRevRange($key, $offset, ($offset + $size) - 1, true);
        }
        $res['count'] = $this->getCount($key); //获取总数
        return $res;
    }

    /**
     * 获取最多关注的直播[粉丝数]
     * @param type $luid
     * @return type
     */
    public function getMostFollowLive($lastid, $gameid, $page, $size) {
        $followlist = array();
        if ($gameid) {//如果是按gamid
            $key = $this->liveCtime . ':' . $gameid;
            $fkey = $this->liveFans . ':' . $gameid;
            if ($this->redis->zcard($fkey) == 0) {
                $liveAnchor = $this->redis->zRevRange($key, 0, -1, true);
                $AnchorId = array_keys($liveAnchor);
                for ($i = 0, $k = count($AnchorId); $i < $k; $i++) {
                    $follow = $this->redis->zScore($this->liveFans, $AnchorId[$i]);
                    $this->redis->zadd($fkey, $follow, $AnchorId[$i]);
                    $this->redis->expire($fkey,120); //设置过期时间
                }
            }
            if ($lastid) {
                $position = $this->redis->zrevrank($fkey, $lastid); //获取当前位置
                $res['res'] = $this->redis->zRevRange($fkey, $position + 1, $position + $size, true);
            } else {
                $offset = ($page - 1) * $size;
                $res['res'] = $this->redis->zRevRange($fkey, $offset, ($offset + $size) - 1, true);
            }
            $res['count'] = $this->getCount($fkey); //获取总数
        } else {
            $key = $this->liveFans;
            if ($lastid) {
                $position = $this->redis->zrevrank($key, $lastid); //获取当前位置
                $res['res'] = $this->redis->zRevRange($key, $position + 1, $position + $size, true);
            } else {
                $offset = ($page - 1) * $size;
                $res['res'] = $this->redis->zRevRange($key, $offset, ($offset + $size) - 1, true);
            }
            $res['count'] = $this->getCount($key); //获取总数
        }

        return $res ? $res : array();
    }

    /**
     * 获取最热的录像[播放数]
     * @param int $videoid  录像id
     * @param int $gameid   游戏id
     * @param int $size     请求数
     * @return array()
     */
    public function getHotVideo($videoid, $gameid, $page, $size) {
        $res = array();
        $key = $this->videoView . ':' . $gameid;
        if ($videoid) {
            $position = $this->redis->zrevrank($key, $videoid); //获取当前位置
            $res['res'] = $this->redis->zRevRange($key, $position + 1, $position + $size, true);
        } else {
            $offset = ($page - 1) * $size;
            $res['res'] = $this->redis->zRevRange($key, $offset, ($offset + $size) - 1, true);
        }
        $res['count'] = $this->getCount($key); //获取总数
        return $res;
    }

    /**
     * 获取最新的录像[审核通过时间]
     * @param int $videoid  录像id
     * @param int $gameid   游戏id
     * @param int $size     请求数
     * @return array()
     */
    public function getNewVideo($videoid, $gameid, $page, $size) {
        $res = array();
        $key = $this->videoCtime . ':' . $gameid;
        if ($videoid) {
            $position = $this->redis->zrevrank($key, $videoid); //获取当前位置
            $res['res'] = $this->redis->zRevRange($key, $position + 1, $position + $size, true);
        } else {
            $offset = ($page - 1) * $size;
            $res['res'] = $this->redis->zRevRange($key, $offset, ($offset + $size) - 1, true);
        }
        $res['count'] = $this->getCount($key); //获取总数
        return $res;
    }

    /**
     * 获取最新的录像[收藏数]
     * @param type $videoid  录像id
     * @param type $gameid   游戏id
     * @param type $size     请求数
     * @return array()
     */
    public function getMostCollectVideo($videoid, $gameid, $page, $size) {
        $res = array();
        $key = $this->videoCollec . ':' . $gameid;
        if ($videoid) {
            $position = $this->redis->zrevrank($key, $videoid); //获取当前位置
            $res['res'] = $this->redis->zRevRange($key, $position + 1, $position + $size, true);
        } else {
            $offset = ($page - 1) * $size;
            $res['res'] = $this->redis->zRevRange($key, $offset, ($offset + $size) - 1, true);
        }
        $res['count'] = $this->getCount($key); //获取总数
        return $res;
    }

    public function getKey($gameid) {
        $ukey = $this->liveUser;
        $ckey = $this->liveCtime;
        $fkey = $this->liveFans;
        $ures = $this->redis->zRevRange($ukey, 0, -1, true);
        $cres = $this->redis->zRevRange($ckey, 0, -1, true);
        $fres = $this->redis->zRevRange($fkey, 0, -1, true);
        $u_key = $this->liveUser . ':' . $gameid;
        $c_key = $this->liveCtime . ':' . $gameid;
        $f_key = $this->liveFans . ':' . $gameid;
        $u_res = $this->redis->zRevRange($u_key, 0, -1, true);
        $c_res = $this->redis->zRevRange($c_key, 0, -1, true);
        $f_res = $this->redis->zRevRange($f_key, 0, -1, true);
        $count = $this->getCount($ukey);
        return array(
            'nogid' => array($ukey => $ures, $ckey => $cres, $fkey => $fres, 'count' => $count),
            'yesgid' => array($u_key => $u_res, $c_key => $c_res, $f_key => $f_res));
    }

    /**
     * 清空数据
     */
    public function flushAll() {
        $this->redis->flushAll();
    }

    /**
     * 获取一个key下的元素数量
     * @param string $key
     * @return int
     */
    public function getCount($key) {
        return $this->redis->zcard($key);
    }

}
