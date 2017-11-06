<?php

require 'init.php';

/**
 * 后台审核视频类
 * yandong@6room.com
 * date 2016-6-30 15:06
 */
class Video
{

    private $video_free = 0;
    private $video_lock = 1;
    private $video_finish = 2;//pass
    private $video_unpass = 3;//unpass
    private $wait_db = 'admin_wait_pass_video';
    private static $db = null;

    function __construct($db = null)
    {
        if (is_null(self::$db)) {
            self::$db = new DBHelperi_admin();
        }
    }

    /**
     * 检测有无处于审核中的数据
     * @param type $adminid 审核者id
     * @return boolean
     */
    function getLockVideo($adminid)
    {
        if (empty($adminid)) {
            return false;
        }
        $res = self::$db->field("videoid")->where("adminid=$adminid and status=" . $this->video_lock)->limit(1)->select($this->wait_db);
        return $res ? $res[0]['videoid'] : '';
    }

    /**
     * 锁定一条数据
     * @param type $adminid 审核者id
     * @param type $videoid 录像id
     * @return boolean
     */
    function setVideoLock($adminid, $videoid)
    {
        if (empty($adminid) || empty($videoid)) {
            return false;
        }
        $data = array(
            'status' => $this->video_lock,
            'adminid' => $adminid,
            'utime' => date('Y-m-d H:i:s')
        );
        $res = self::$db->where("videoid=$videoid")->update($this->wait_db, $data);
        return $res ? $res : '';
    }

    /**
     * 获取一条待审核录像
     * @return int  录像id
     */
    function getNewVideo()
    {
        $res = self::$db->field('videoid')->where('status=' . $this->video_free)->order("ctime ASC")->limit(1)->select($this->wait_db);
        return $res ? (int)$res[0]['videoid'] : '';
    }

    /**
     * 完成审核
     * @param type $adminid 审核者id
     * @param type $videoid 录像id
     * @return boolean
     */
    function setVideoFinish($adminid, $videoid, $type)
    {
        if (empty($adminid) || empty($videoid)) {
            return false;
        }
        if ($type == 2) {//通过
            $data = array(
                'status' => $this->video_finish,
                'etime' => date('Y-m-d H:i:s')
            );
        }
        if ($type == 3) {//驳回
            $data = array(
                'status' => $this->video_unpass,
                'etime' => date('Y-m-d H:i:s')
            );
        }
        $res = self::$db->where("adminid=$adminid  and  videoid=$videoid")->update($this->wait_db, $data);
        return $res ? $res : '';
    }

    /**
     * 获取一条录像基本信息
     * @param type $videoid 录像id
     * @return boolean  and status=" . VIDEO_UNPUBLISH
     */
    function getVideoInfo($videoid)
    {
        if (empty($videoid)) {
            return false;
        }
        $res = self::$db->field('videoid,uid,gametid,gamename,title,ctime,vfile,orientation,length,poster,liveid')->where("videoid=$videoid")->limit(1)->select('video');
        return $res ? $res : '';
    }

    /**
     * 修改视频状态
     * @param int $videoid 录像id
     * @param array $data
     * @return boolean
     */
    function setVideoPassOrUnpass($videoid, $data)
    {
        if (empty($videoid) || empty($data)) {
            return false;
        }
        $res = self::$db->where('videoid=' . $videoid)->update('video', $data);
        return $res ? $res : '';
    }

    /**
     * 添加一条未通过审核录像
     * @param type $videoid 录像id
     * @param type $adminid 审核者id
     * @param type $type 驳回原因
     * @param type $describe 具体描述
     * @return boolean
     */
    function setVideoUnpass($videoid, $adminid, $reason, $describe)
    {
        if (empty($videoid) || empty($adminid) || empty($reason) || empty($describe)) {
            return false;
        }
        $data = array(
            'videoid' => $videoid,
            'adminid' => $adminid,
            'type' => $reason,
            'describe' => $describe
        );
        $res = self::$db->insert('admin_unpass_video', $data);
        return $res ? $res : '';
    }

    /**
     * 根据类型获取录像列表
     * @param type $type
     * @param type $page
     * @param type $size
     * @param type $db
     * @return type
     */
    function getvlistBytype($type, $page, $size)
    {
        if (empty($type)) {
            return false;
        }
        if ($type == 1) {//待审核
            $res = self::$db->field('videoid')->where('status=' . VIDEO_WAIT)->order('ctime  DESC')->limit($page, $size)->select('admin_wait_pass_video');
        }
        if ($type == 2) {//已审核
            $res = self::$db->field('videoid')->where('status=' . VIDEO)->order('ctime  DESC')->limit($page, $size)->select('admin_wait_pass_video');
        }
        if ($type == 3) {//审核中
            $res = self::$db->field('videoid')->where('status=' . VIDEO_UNPUBLISH)->order('ctime  DESC')->limit($page, $size)->select('admin_wait_pass_video');
        }
        if ($type == 4) {//未通过
            $res = self::$db->field('videoid')->where('status=' . VIDEO_UNPASS)->order('ctime  DESC')->limit($page, $size)->select('video');
        }
        if ($res !== false && !empty($res)) {
            $result = $res;
        } else {
            $result = '';
        }
        return $result;
    }

    /**
     * 批量获取录像信息
     * @param type $videoid 录像id string 如 2,23
     * @return array();
     */
    function getMostVideoInfo($videoid)
    {
        if (empty($videoid)) {
            return false;
        }
        $res = self::$db->field('videoid,uid,gametid,gamename,title,ctime,vfile,length,poster,liveid')->where("videoid in ($videoid)")->select('video');
        if (!empty($res)) {
            foreach ($res as $v) {
                $result[$v['videoid']] = $v;
            }
        } else {
            $result = array();
        }
        return $result;
    }

    /**
     * 待审核
     * @param type $db
     * @return string
     */
    function waitPass($uid = 0, $nick = '')
    {
        //if()        
        $res = self::$db->field('count(*) as total')->where('status=0')->select('admin_wait_pass_video');
        if (isset($res[0]['total'])) {
            $wpass = (int)$res[0]['total'];
        } else {
            $wpass = 0;
        }
        return $wpass;
    }

    /**
     * 已审核
     * @param type $db
     * @return string
     */
    function Pass()
    {
        $res = self::$db->field('count(*) as total')->where('status=' . VIDEO)->select('admin_wait_pass_video');
        if (isset($res[0]['total'])) {
            $pass = (int)$res[0]['total'];
        } else {
            $pass = 0;
        }
        return $pass;
    }

    /**
     * 审核中
     * @param type $db
     * @return string
     */
    function pending()
    {
        $res = self::$db->field('count(*) as total')->where('status=' . VIDEO_UNPUBLISH)->select('admin_wait_pass_video');
        if ($res !== false && isset($res[0]['total'])) {
            $pend = (int)$res[0]['total'];
        } else {
            $pend = 0;
        }
        return $pend;
    }

    /**
     * 审核未通过
     * @param type $db
     * @return string
     */
    function unPass()
    {
        $res = self::$db->field('count(*) as total')->where('status=' . VIDEO_UNPASS)->select('video');
        if ($res !== false && isset($res[0]['total'])) {
            $unpass = (int)$res[0]['total'];
        } else {
            $unpass = 0;
        }
        return $unpass;
    }

    /**
     * 删除录像 (逻辑删除)
     * @param type $videoId 录像id
     * @return boolean
     */
    function delVideo($videoId)
    {
        if (empty($videoId)) {
            return false;
        }
        $data = array('status' => VIDEO_DEL);
        $res = self::$db->where('videoid=' . $videoId)->update('video', $data);
        if ($res !== false) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 根据直播ID获取直播开始时间
     * @param type $liveIds array() 直播id
     * @return array
     */
    function getLiveTime($liveIds)
    {
        $liveIds = implode(',', $liveIds); 
        $live = self::$db->field('liveid,ctime')->where('liveid in (' . $liveIds . ')')->select('live');
        $res = array();
        if($live) {
            foreach ($live as $v) {
                $res[$v['liveid']] = $v['ctime'];
            }
        }
        return $res;
    }
    
}
