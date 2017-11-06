<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/14
 * Time: 11:37
 */

namespace lib\anchor;
use system\DbHelper;
use Exception;
/**
 * 主播签约经济公司
 * Class AnchorApplyCompany anchorapplycompany
 * @package lib\anchor
 */
class AnchorApplyCompany
{
    //db 配置文件的key
    const DB_CONF = 'huanpeng';
    //视频存储标记
    const IS_SAVE = 1;
    //主播取消申请
    const ANCHOR_CANCEL_APPLY = 1;
    public static $fields = [
        'id',// int(10) unsigned NOT NULL AUTO_INCREMENT,
        'uid',// int(10) unsigned NOT NULL COMMENT '主播id',
        'cid',// int(10) unsigned NOT NULL COMMENT '申请加入的公司ID',
        'videoid',// int(10) unsigned NOT NULL COMMENT '录像ID',
        'ctime',// timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '提交申请时间',
        'utime',// datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '如果用户取消，这里就是用户取消时间，否则是公司审核时间',
        'companyreason',// varchar(255) NOT NULL DEFAULT '' COMMENT '公司审核理由',
        'companyuid',// int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公司审核管理员ID',
        'admintime',// datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '运营审核时间',
        'adminid',// int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运营审核管理员ID',
        'adminreason',// varchar(255) NOT NULL DEFAULT '' COMMENT '运营审核理由',
        'status',// tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0未审核 1主播取消 2公司审核通过 3公司审核不通过 4运营审核通过 5运营审核不通过',
    ];
    /**
 * 定义表名 主播签约经纪公司表
 * @return string
 */
    public function tableName()
    {
        return 'anchorapplycompany';
    }

    /**
     * 主播视频表
     * @return string
     */
    public function tableName2()
    {
        return 'video';
    }

    /**
     * 添加主播申请签约
     * @param $uid
     * @param $cid
     * @param $videoid
     * @return bool
     */
    public function addAnchorApplyCompany($data)
    {
        $uid = $data['uid'];
        $cid = $data['cid'];
        $videoid = $data['videoid'];
        $db      = $this->getDb();
        $bdParam = [
            'uid'       => $uid,
            'cid'      => $cid,
            'videoid'     => $videoid,
        ];

        $sql = "INSERT INTO `{$this->tableName()}` (`uid`,`cid`,`videoid`) VALUES(:uid,:cid,:videoid)";

        try {
            //此处用事务
            $db->beginTransaction();
            //插入sql
            $db->execute($sql,$bdParam);
            //更改视频状态
            $this->setVideoSave($data);
            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * 获取主播最后一条签约信息
     * @param $uid
     * @return bool|\PDOStatement
     */
    public function getLastAnchorApply($uid)
    {
        $db      = $this->getDb();
        $fields = self::$fields;
        $table = $this->tableName();
        $fields  = $db->buildFieldsParam($fields);
        $sql   = "SELECT {$fields} FROM `{$table}` WHERE `uid` = :uid ORDER BY id DESC LIMIT 1";
        $bdParam = [
            'uid'       => $uid,
            ];
        try {
            return  $db->query($sql,$bdParam);
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 获取主播签约列表
     * @param $uid
     * @return bool|\PDOStatement
     */
    public function getAnchorApplyList($uid)
    {
        $db      = $this->getDb();
        $fields = self::$fields;
        $table = $this->tableName();
        $fields  = $db->buildFieldsParam($fields);
        $sql   = "SELECT {$fields} FROM `{$table}` WHERE `uid` = :uid ORDER BY id";
        $bdParam = [
            'uid'       => $uid,
        ];
        try {
            return  $db->query($sql,$bdParam);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 申请签约视频永久保留
     * @param $uid
     * @param $videoid
     * @return bool
     */
    public function setVideoSave($data)
    {
        $uid = $data['uid'];
        $videoid = $data['videoid'];
        $db      = $this->getDb();
        $table = $this->tableName2();
       $sql   = "UPDATE `{$table}` SET  `is_save` = :is_save WHERE uid=:uid and videoid = :videoid LIMIT 1";
        $bdParam = [
            'uid'     => $uid,
            'videoid' => $videoid,
            'is_save' => self::IS_SAVE,
        ];
        try {

            return $db->execute($sql,$bdParam);

        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * 更新主播取消申请状态
     * @param $data
     * @return bool
     */
    public function updateAnchorApplyCancelStatus($data)
    {
        $aid = $data['aid'];
        $db      = $this->getDb();
        $table = $this->tableName();
        $sql   = "UPDATE `{$table}` SET  `status` = :status,utime = :utime WHERE id=:aid LIMIT 1";
        $time = date("Y-m-d H:i:s");
        $bdParam = [
            'aid'     => $aid,
            'status' =>self::ANCHOR_CANCEL_APPLY,
            'utime'=>$time,
        ];
        try {

            return $db->execute($sql,$bdParam);

        } catch (Exception $e) {

            return false;
        }
    }
    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

}