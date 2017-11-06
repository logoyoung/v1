<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/2
 * Time: 10:14
 */
namespace lib\due;
use service\common\ApiCommon;
use system\DbHelper;

/**
 * 约玩资质认证表类
 * Class cert
 * @package lib\due
 */
class DueCert
{
    //db 配置文件的key
    public static $dbConfName = 'huanpeng';
    private $uid = null;
    private $_db = null;
    private $_page = 1;
    private $_pageSize = 3;
    public  $param = [];
    //定义新增或更新资质审核状态 审核状态 -1,审核中.1,机器审核通过.2,人工审核通过.3,机器审核未通过 4,人工审核未通过
    const ADD_CERT_STATUS = '-1';
    /**
     * 初始化类
     * @param $uid
     * @param string $db
     */
    public function __construct( $uid, $db = '' )
    {
        if( $uid )
        {
            $this->uid = (int)$uid;
        }
        if( $db )
        {
            $this->_db = $db;
        }
        else
        {
            $this->_db = DbHelper::getInstance(self::$dbConfName);
        }
        return true;
    }
    /**
     * 约玩资质表
     * @return string
     */
    public function tableName()
    {
        return 'due_cert';
    }

    /**
     * 后台约玩资质审核表
     * @return string
     */
    public function adminTableName()
    {
        return 'admin_due_cert';
    }
    /**
     * 设置页数
     * @param $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    /**
     * 设置页面大小
     * @param $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->_pageSize = $pageSize;
        return $this;
    }
    /**
     * 查询主播约玩资质
     * @return bool|\PDOStatement
     */
    public function getAllCert()
    {
        $table = $this->tableName();
        //查询主播约玩资质
        $sql   = "SELECT  `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`ctime`,`utime` FROM `{$table}` WHERE `uid` = :uid  ORDER BY id DESC";
        //参数绑定
        $bdParam = [
            'uid' => $this->uid ,
        ];

        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 主播自己查询主播资质最新
     * @return bool|\PDOStatement
     */
    public function getAdminCert()
    {
        $table = $this->adminTableName();
        //查询主播约玩资质
        $sql   = "SELECT  `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`ctime`,`utime` FROM `{$table}` WHERE `uid` = :uid  ORDER BY id DESC";
        //参数绑定
        $bdParam = [
            'uid' => $this->uid ,
        ];

        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 获取主播列表按uid
     * @return bool|\PDOStatement
     */
    public function getCertListByUid()
    {
        //实际页码
        $this->_page = ($this->_page-1)*$this->_pageSize;
        $table = $this->tableName();
        //查询主播约玩资质
        $sql   = "SELECT  `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`ctime`,`utime` FROM `{$table}` WHERE `uid` = :uid  ORDER BY id DESC LIMIT :page,:pageSize";
        //参数绑定
        $bdParam = [
            'uid' => $this->uid ,
            'page'=> $this->_page,
            'pageSize'=> $this->_pageSize,
        ];

        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 查询资质信息
     * @param $data['certId'] 必须
     * @return bool|\PDOStatement
     */
    public function getCertByCertId($data)
    {
        $certId = $data['certId'];
        $table = $this->tableName();
        if(is_array($certId))
        {
            //绑定占位符
            $in    = $this->_db->buildInPrepare($certId);
            //查询主播约玩资质
            $sql   = "SELECT   `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`reason`,`ctime`,`utime` FROM `{$table}` WHERE `id` in ({$in})";
            $bdParam = $certId;
        }else
        {
            //查询主播约玩资质
            $sql   = "SELECT   `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`reason`,`ctime`,`utime` FROM `{$table}` WHERE  `id` =:certId   ORDER BY id";
            //参数绑定
            $bdParam = [
                'certId'   => $certId,
            ];
        }


        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 主播查询自己主播资质信息
     * @param $data['certId'] 必须
     * @return bool|\PDOStatement
     */
    public function getAdminCertByCertId($data)
    {
        $certId = $data['certId'];
        $table = $this->adminTableName();
        if(is_array($certId))
        {
            //绑定占位符
            $in    = $this->_db->buildInPrepare($certId);
            //查询主播约玩资质
            $sql   = "SELECT   `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`reason`,`ctime`,`utime` FROM `{$table}` WHERE `id` in ({$in})";
            $bdParam = $certId;
        }else
        {
            //查询主播约玩资质
            $sql   = "SELECT   `id` as certId,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`,`reason`,`ctime`,`utime` FROM `{$table}` WHERE  `id` =:certId   ORDER BY id";
            //参数绑定
            $bdParam = [
                'certId'   => $certId,
            ];
        }


        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 查询主播资质按gameId
     * @return array|bool
     */
    /**
     * @param $data
     * @return bool|\PDOStatement
     */
    public function getCertByGameId($data)
    {
        $gameId = $data['gameId'];
        $table = $this->tableName();
        //查询主播约玩资质
        $sql   = "SELECT  `id` FROM `{$table}` WHERE  `uid`=:uid and `game_id` =:game_id  ORDER BY id";
        //参数绑定
        $bdParam = [
            'uid' => $this->uid,
            'game_id'   => $gameId,
        ];

        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 增加主播约玩资质
     * @return bool
     */
    public function addCert($data)
    {
        $this->param = $data;

        $adminTable = $this->adminTableName();

        $adminSql   = "INSERT INTO `{$adminTable}`(`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`) VALUES(:uid,:game_id,:game_level_id,:pic_urls,:video_url,:video_size,:audio_url,:audio_size,:info,:status)";
        //参数绑定
        $bdParam = [
            'uid'           => $this->uid,
            'game_id'       =>  isset($this->param['gameId'])  ? $this->param['gameId'] : '',
            'game_level_id' =>  isset($this->param['gameLevelId']) ? $this->param['gameLevelId'] : '',
            'pic_urls'      =>  isset($this->param['picUrls']) ? $this->param['picUrls'] : '',
            'video_url'     =>  isset($this->param['videoUrl']) ? $this->param['videoUrl'] : '',
            'video_size'    =>  isset($this->param['videoSize']) ? $this->param['videoSize'] : '',
            'audio_url'     =>  isset($this->param['audioUrl']) ? $this->param['audioUrl'] : '',
            'audio_size'    =>  isset($this->param['audioSize']) ? $this->param['audioSize'] : '',
            'info'          =>  isset($this->param['info']) ? $this->param['info'] : '',
            'status'        => self::ADD_CERT_STATUS,
        ];
        try {
            //此处用事务
            $this->_db->beginTransaction();
            //插入sql
            $this->_db->execute($adminSql,$bdParam);
            //获取最后一条数据
            $lastId = $this->_db->lastInsertId();
            if($lastId)
            {
                $table = $this->tableName();
                $bdParam['id'] = $lastId;
                $sql   = "INSERT INTO `{$table}`(`id`,`uid`,`game_id`,`game_level_id`,`pic_urls`,`video_url`,`video_size`,`audio_url`,`audio_size`,`info`,`status`) VALUES(:id,:uid,:game_id,:game_level_id,:pic_urls,:video_url,:video_size,:audio_url,:audio_size,:info,:status)";
               $this->_db->execute($sql,$bdParam);
                $this->_db->commit();
            }
            return true;

        } catch (Exception $e) {
            $this->_db->rollBack();
            return false;
        }
    }

    /**
     * 修改主播资质
     * @return bool
     */
    public function updateCert($data)
    {
        $this->param = $data;
        $adminTable = $this->adminTableName();
        $sql   = "UPDATE `{$adminTable}` SET `game_level_id` = :game_level_id ,`pic_urls` = :pic_urls ,`video_url` = :video_url ,`video_size` = :video_size ,`audio_url` = :audio_url ,`audio_size` = :audio_size ,`info` = :info ,`status` = :status WHERE `id` = :id and `uid` = :uid LIMIT 1";
        //参数绑定
        $bdParam = [
            'id'            => isset($this->param['certId'])?$this->param['certId']:'',
            'uid'           => $this->uid,
            'game_level_id' => isset($this->param['gameLevelId'])?$this->param['gameLevelId']:'',
            'pic_urls'      => isset($this->param['picUrls'])?$this->param['picUrls']:'',
            'video_url'     => isset($this->param['videoUrl'])?$this->param['videoUrl']:'',
            'video_size'    => isset($this->param['videoSize'])?$this->param['videoSize']:'',
            'audio_url'     => isset($this->param['audioUrl'])?$this->param['audioUrl']:'',
            'audio_size'    => isset($this->param['audioSize'])?$this->param['audioSize']:'',
            'info'          => isset($this->param['info'])?$this->param['info']:'',
            'status'        => self::ADD_CERT_STATUS,
        ];
        try {

            return $this->_db->execute($sql,$bdParam);

        } catch (Exception $e) {

            return false;
        }
    }
}