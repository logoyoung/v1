<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/2
 * Time: 16:44
 */
namespace service\due;
use lib\Anchor;
use lib\due\DueComment;
use lib\due\DueSkill;
use lib\due\DueCert;
use lib\due\DueOrder;
use lib\due\DueTags;
use lib\due\DueRecGame;
use service\live\VideoService;
use system\RedisHelper;
use service\common\AbstractService;
use service\common\UploadImagesCommon;
use service\user\UserDataService;
use service\live\LiveService;
use service\rule\TextService;
use service\common\ApiCommon;
/**
 *  陪玩资质认证服务
 * Class CertService
 * @package service\due\cert
 */
class DueCertService  extends AbstractService
{
    //增加技能开关缓存
    const DUESKILL_SWITCH_KEY = 'dueSkillSwitch';
    const DUESKILL_SWITCH_KEY_EXPIRE = 300;
    //资质先发后审开关 1为先发后审 0为先审后发S
    const ALLOW_CERT_MODEL_SWITCH = 1;
    //允许主播添加资质个数
    const ALLOW_DUE_CERT_LIST = 3;
    //游戏下架
    const GAME_DOWN = 2;
    //暂时只允许签约主播  1 所有主播 2  签约主播
    const ALLOW_DUE_ANCHOR = 2;
    //获取主播资质列表失败
    const ERROR_ALL_CERTLIST = -8002;
    //获取主播技能列表失败
    const ERROR_ALL_SKILLLIST = -8003;
    //获取主播技能评论列表失败
    const ERROR_ALL_COMMENTLIST = -8004;
    //数据写入失败
    const ERROR_ALL_DATAOPTION = -8005;
    //已存在资质
    const IS_EXIST_CERT = - 5001;
    //获取订单数失败
    const ERROR_ORDER_TOTAL = -5002;
    //获取标签失败
    const ERROR_GET_TAG = -8008;
    //不允许申请资质
    const ERROR_NOT_ALLOW = -8009;
    //图片地址不正确
    const ERROR_PIC_URLS = -8010;
    //超过最多申请资质限制
    const ERROR_CERT_MAX = -8011;
    //获取游戏列表失败
    const ERROR_GAME_LIST = -8012;
    //缺少必须参数
    const ERROR_PARAM = -4013;
    //参数值错误
    const ERROR_NOT_NULL = -993;
    //资质审核状态
    //审核中
    const CERT_STATUS_WAIT = -1;
    //通过
    const CERT_STATUS_PASS = 1;
    //未通过
    const CERT_STATUS_UNPASS = 2;
    //陪玩资质图片子目录
    const CERT_IMG_SUBDIR = 'dueCert';
    //数据操作
    const DATA_OPTION = 'add';
    //资质详情评论数
    const CERT_DETAIL_COMMNET = 5;
    //技能开关关闭状态
    const SKILL_SWITCH_OFF = -1;
    //技能开关打开状态
    const SKILL_SWITCH_ON = 1;
    //定义单位
    const UNIT_ROUND = '局';
    const UNIT_HOUR = '小时';
    public static $errorMsg =[
        self::ERROR_ALL_CERTLIST => '获取主播资质失败',
        self::ERROR_ALL_SKILLLIST => '获取主播设置失败',
        self::ERROR_ALL_COMMENTLIST => '获取主播评论失败',
        self::ERROR_ALL_DATAOPTION => '糟糕！数据写入失败',
        self::ERROR_PARAM =>'必要参数缺少',
        self::ERROR_NOT_NULL =>'参数值错误：',
        self::CERT_STATUS_WAIT =>'资质审核中',
        self::CERT_STATUS_UNPASS =>'资质审核未通过',
        self::IS_EXIST_CERT =>'游戏资质已存在,不能再次添加',
        self::ERROR_GET_TAG =>'获取标签失败',
        self::ERROR_NOT_ALLOW=>'对不起,目前仅允许签约主播申请',
        self::ERROR_PIC_URLS=>'不正确的图片地址',
        self::ERROR_CERT_MAX=>'对不起,申请资质超限',
        self::ERROR_GAME_LIST=>'获取游戏列表失败',
        self::GAME_DOWN =>'对不起,该游戏已下架',

    ];
    //订单结束不可再更改
    const ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT = 1000;
    private $_uid;
    private $_enc;
    public  $redis;

    //返回审核状态
    public static function setStatus($status)
    {
        switch(self::ALLOW_CERT_MODEL_SWITCH)
        {
            //先审后发
            case 0:
                //审核状态
                $status_value = [
                    '-1'=>self::CERT_STATUS_WAIT,
                    '1'=>self::CERT_STATUS_WAIT,
                    '2'=>self::CERT_STATUS_PASS,
                    '3'=>self::CERT_STATUS_UNPASS,
                    '4'=>self::CERT_STATUS_UNPASS,
                ];
                break;
            //先发后审
            case 1:
                //审核状态
                $status_value = [
                    '-1'=>self::CERT_STATUS_WAIT,
                    '1'=>self::CERT_STATUS_PASS,
                    '2'=>self::CERT_STATUS_PASS,
                    '3'=>self::CERT_STATUS_UNPASS,
                    '4'=>self::CERT_STATUS_UNPASS,
                ];
                break;
            default:
                //都不显示
                $status_value = [
                    '-1'=>self::CERT_STATUS_WAIT,
                    '1'=>self::CERT_STATUS_UNPASS,
                    '2'=>self::CERT_STATUS_UNPASS,
                    '3'=>self::CERT_STATUS_UNPASS,
                    '4'=>self::CERT_STATUS_UNPASS,
                ];
                break;
        }

        return $status_value[$status];
    }
    //返回单位中文名
    public static function getUnitName($unit=1)
    {
        $unit_arr = [
            '1'=>self::UNIT_ROUND,
            '2'=>self::UNIT_HOUR,
        ];
        if(array_key_exists($unit,$unit_arr))
        {
            return $unit_arr[$unit];
        }
        return $unit_arr['1'];
    }

    //返回陪玩图片服务器地址
    public function getImageDomain()
    {
        return UploadImagesCommon::getImageDomainUrl();
    }
    //上传设置陪玩资质地址
   public  function setImagePath()
   {
       return UploadImagesCommon::setImagePath($this->_uid,self::CERT_IMG_SUBDIR);
   }

    /**
     * 上传资质图片
     * @return array
     */
    public function uploadCertImage()
    {
       $res =  UploadImagesCommon::uploadImage($this->_uid,self::CERT_IMG_SUBDIR);
        return $res;
    }
    //使用apicommon中方法
    public function apiCommon()
    {
        return new ApiCommon();
    }

    //获取资质数据
    public function dueCertData()
    {
        return  new DueCert($this->getUid());

    }
    //获取技能数据
    public function dueSkillData()
    {
        return  new DueSkill($this->getUid());
    }
    //获取评价数据
    public function dueCommentData()
    {
        return  new DueComment($this->getUid());
    }
    //获取游戏数据
    public function dueGameData()
    {
        return new DueRecGame();
    }
    //获取订单数据
    public function dueOrderData()
    {
        return new DueOrder();
    }
    //获取标签数据
    public function dueTagData()
    {
        return new DueTags();
    }
    //获取主播数据
    public function getAnchorData()
    {
        return new Anchor($this->getUid());
    }
    //获取用户信息服务
    public function userInfo()
    {
        return new UserDataService();
    }
    //获取标签服务
    public function getTagService()
    {
        return new DueTagsService();
    }
    //获取主播服务
    public function getLiveService()
    {
        return new LiveService();
    }
    //获取视频服务
    public function getVideoService()
    {
        return new VideoService();
    }
    /**
     * 获取redis资源
     * @return \redis
     */
    public function getRedis()
    {
        if (is_null($this->redis))
        {
            $this->redis = RedisHelper::getInstance("huanpeng");
        }
        return $this->redis;
    }
    public function setUid($uid)
    {
        $this->_uid     = $uid;
        return $this;
    }

    public function getUid()
    {
        return  $this->_uid;
    }

    public function setEnc()
    {
        return $this->_enc;
    }
    //获取主播认证信息
    public function getAnchorCertInfo()
    {
        $data = $this->getAnchorData()->getAnchorCertInfo();
        return $data;
    }
    //获取主播是否直播状态
    public function isLiving($luid)
    {
        return $this->getLiveService()->setLuid($luid)->isLiving();
    }
    //验证图片地址是否正确
    public function checkPicUrls($picUrls)
    {
        $len = strlen($picUrls);
        if($len < 100)
        {
            $code = self::ERROR_PIC_URLS;
            $msg =  self::$errorMsg[$code].' picUrls:'.$picUrls;
            render_error_json($msg, $code);
        }
        return true;
    }
    /**
     * 返回是否允许注册约玩
     * @param bool $die
     * @return bool
     */
    public function getIsAllowDue($die = false)
    {
        //主播认证信息
        $anchorCertInfo = $this->getAnchorCertInfo();
        $Allow = self::ALLOW_DUE_ANCHOR;
        switch($Allow)
        {
            case 1:
                if($anchorCertInfo['cert_status'] == 1)
                {
                    return true;
                }
                break;
            case 2:
                //cid 不等于0 为签约主播
                if(($anchorCertInfo['cid'] > 0) && ($anchorCertInfo['cert_status'] == 1))
                {
                    return true;
                }
                break;
            default:
                return false;
                break;
        }
        if($die === true)
        {
            $code = self::ERROR_NOT_ALLOW;
            $msg =  self::$errorMsg[$code] .'uid:'.$this->_uid;
            render_error_json($msg, $code);
        }
        return false;
    }
    /**
     * 获取资质技能列表
     * @return array
     */
    public function getAllCertList()
    {
        $res = [];
        $skillIds=[];
        $totalBySkillID = [];
        //获取主播认证列表
        $list['certlist'] = $this->getCertByUid();
       if(count($list['certlist']) > 0)
       {
           $list['skilllist'] = $this->getSkillByUid();
           //如果没有技能
           if (count($list['skilllist']) == 0)
           {
               $res = $list['certlist'];
               foreach($res as $key=>$value)
               {
                   $gameName = $this->getGameNameByGameId($value['game_id']);
                   $res[$key]['gameName'] = $gameName;
               }
           }else
           {
               //合并资质和技能数据
               foreach ($list['certlist'] as $key => $value)
               {
                   foreach ($list['skilllist'] as $v)
                   {
                       if ($value['certId'] == $v['cert_id'])
                       {
                           //将技能数据和资质数据合并
                           $list['certlist'][$key] = array_merge($value, $v);
                           $list['certlist'][$key]['star'] = $v['avg_score']/2;
                           $skillIds[]= $v['skillId'];
                       }
                   }
               }
               if(count($skillIds) >0 )
               {
                   $totalBySkillID = $this->getOrderTotalBySkillId($skillIds);
               }
               //循环放入订单数
              foreach($list['certlist'] as $key=>$value)
              {
                  $gameName = $this->getGameNameByGameId($value['game_id']);
                  $list['certlist'][$key]['gameName'] = $gameName;
                    foreach ($totalBySkillID as $v)
                    {
                        if(isset($value['skillId']))
                        {
                            if($v['skill_id'] == $value['skillId'])
                            {
                                $list['certlist'][$key]['orderTotal'] = $v['order_total'];
                            }
                        }
                    }
              }
               $res = $list['certlist'];
           }

           return $res;
       }else{
           return [];
       }
    }
    /**
     * 主播自己获取资质技能列表
     * @return array
     */
    public function getAdminCertList()
    {
        $res = [];
        $skillIds=[];
        $totalBySkillID = [];
        //获取主播认证列表
        $list['certlist'] = $this->getAdminCertByUid();
        if(count($list['certlist']) > 0)
        {
            $list['skilllist'] = $this->getSkillByUid();
            //如果没有技能
            if (count($list['skilllist']) == 0)
            {
                $res = $list['certlist'];
                foreach($res as $key=>$value)
                {
                    $gameName = $this->getGameNameByGameId($value['game_id']);
                    $res[$key]['gameName'] = $gameName;
                }
            }else
            {
                //合并资质和技能数据
                foreach ($list['certlist'] as $key => $value)
                {
                    foreach ($list['skilllist'] as $v)
                    {
                        if ($value['certId'] == $v['cert_id'])
                        {
                            //将技能数据和资质数据合并
                            $list['certlist'][$key] = array_merge($value, $v);
                            $list['certlist'][$key]['star'] = $v['avg_score']/2;
                            $skillIds[]= $v['skillId'];
                        }
                    }
                }
                if(count($skillIds) >0 )
                {
                    $totalBySkillID = $this->getOrderTotalBySkillId($skillIds);
                }
                //循环放入订单数
                foreach($list['certlist'] as $key=>$value)
                {
                    $gameName = $this->getGameNameByGameId($value['game_id']);
                    $list['certlist'][$key]['gameName'] = $gameName;
                    foreach ($totalBySkillID as $v)
                    {
                        if(isset($value['skillId']))
                        {
                            if($v['skill_id'] == $value['skillId'])
                            {
                                $list['certlist'][$key]['orderTotal'] = $v['order_total'];
                            }
                        }
                    }
                }
                $res = $list['certlist'];
            }

            return $res;
        }else{
            return [];
        }
    }

    /**
     * 获取主播技能列表 资质审核通过
     * @param $data['luid']
     * @return array
     */
    public function getAllCertListByStatusPass($data)
    {
        //资质详情
        $res = [];
        $requirRule = ['luid' => 'NOTNULL'];
        $this->checkParams($data, $requirRule);
        //获取所有资质详情
        $list = $this->getAllCertList();
        foreach ($list as $key=>$value)
        {
            $value['status'] = DueCertService::setStatus($value['status']);
            //具有技能且资质审核通过
            if(isset($value['skillId']) && $value['status'] == self::CERT_STATUS_PASS)
            {
                //技能开关为开
                if($value['switch'] == 1)
                {
                    $res[] = $value;
                }
            }
        }
        return $res;
    }

    /**
     * 资质认证详情
     * @param $data
     * @return array
     */
    public function getCertDetail($data)
    {
        $res = [];
        $skillIds = [];
        $uids = [];
        $tmpCommnet =[];
        $tags = [];
        //资质详情
        $requirRule = ['skillId' => 'NOTNULL', 'certId' => 'NOTNULL'];
        $this->checkParams($data, $requirRule);
        //获取所有标签
        $allTags = $this->getTagService()->getAllTags();
        //获取主播认证列表
        $list['certlist'] = $this->getCertDetailByCertId($data);
        if (empty($list['certlist']))
        {
            $code = self::ERROR_ALL_CERTLIST;
            $msg =  self::$errorMsg[$code] .$this->_uid;
            render_error_json($msg, $code);
        }

            $list['skilllist'] = $this->getSkillBySkillId($data);
            //目前仅有一层数组
            foreach ($list['certlist'] as $key => $value) {
                //目前仅有一层技能
                foreach ($list['skilllist'] as $v) {
                    //将技能数据和资质数据合并
                    $res[$key] = array_merge($value, $v);
                    if ($data['skillId'] == -1) {
                        $res[$key]['star'] = 0;
                    } else {
                        $res[$key]['star'] = $v['avg_score']/2;
                        //目前仅有一个技能
                        $skillIds[] = $v['skillId'];
                    }
                }
            }
            if (count($skillIds) > 0) {
                //获取订单数按技能id
                $totalBySkillID = $this->getOrderTotalBySkillId($skillIds);
                //目前仅有一个技能
                $skillId['skillId'] = $skillIds[0];
                $skillId['pageSize'] = 100000;
                //获取本详情评论
                $comment = $this->getCommentBySkillId($skillId);
                $commentTotal = count($comment);

                if (count($totalBySkillID) > 0) {
                    //循环放入订单数
                    foreach ($res as $key => $value) {

                        foreach ($totalBySkillID as $v) {
                            if ($v['skill_id'] == $value['skillId']) {
                                $res[$key]['orderTotal'] = $v['order_total'];
                            }
                        }
                        if ($commentTotal > 0)
                        {
                            foreach ($comment as $kk => $com)
                            {
                                //此处仅有一个技能
                                if ($value['skillId'] == $com['skill_id'])
                                {
                                    $tmpCommnet[$kk]['uid'] = $com['uid'];
                                    $tmpCommnet[$kk]['comment'] = $com['comment'];
                                    //$tmpCommnet[$kk]['tagIds'] = $com['tag_ids'];
                                    $tmpCommnet[$kk]['star'] = $com['star']/2;
                                    $tmpCommnet[$kk]['ctime'] = strtotime($com['ctime']);
                                    $uids[] = $com['uid'];
                                    $tagIds = $this->getTagService()->getTagNameByIds($com['tag_ids'],$allTags);
                                    $tmpCommnet[$kk]['tags']= is_array($tagIds)?$tagIds:[];
                                }
                             }
                        }
                    }
                }
            }
            $userInfoarr = [];
            foreach($res as $key=>$value)
            {
                //获取所有用户信息
                $uids[] = $value['uid'];
                $uidarr = array_unique($uids);
                $userInfo = $this->userInfo();
                $userInfo->setUid($uidarr);
                $userInfoarr =  $userInfo->batchGetUserInfo();
                if(count($tmpCommnet)>0)
                {
                    foreach($tmpCommnet as $k=>$comList)
                    {
                        $tmpCommnet[$k]['nick']  = $userInfoarr[$comList['uid']]['nick'];
                        $tmpCommnet[$k]['pic']  = $userInfoarr[$comList['uid']]['pic'];
                    }
                }

            }
            $gameName = $this->getGameNameByGameId($res[0]['game_id']);
            $res[0]['comment']['list'] =  $tmpCommnet;
            $res[0]['comment']['commentTotal'] =  count($tmpCommnet);
            $res[0]['nick'] = $userInfoarr[$res[0]['uid']]['nick'];
            $res[0]['pic'] = $userInfoarr[$res[0]['uid']]['pic'];
            $res[0]['gameName'] = $gameName;
            $tagIdsByUid = $this->getTagService()->getUserTagsByUid($res[0]['uid']);
            $tagName =  $this->getTagService()->getTagNameByIds($tagIdsByUid,$allTags);
            $res[0]['tags'] = $tagName;
            $res[0]['isLiving'] = $this->isLiving($res[0]['uid']);
            //获取主播直播信息
            if($res[0]['isLiving'])
            {
                //获取直播信息 一维数组
                $anchorMoreInfo = $this->getLiveService()->setLuid($res[0]['uid'])->getLastLive();
                $res[0]['vtype'] = 1;
                $res[0]['poster'] = $this->getLiveService()->setLuid($res[0]['uid'])->getLivePoster().$anchorMoreInfo['poster'];
                $res[0]['lvid'] = isset($anchorMoreInfo['liveid']) ? $anchorMoreInfo['liveid'] : '';

            }else
            {
                //离线视频信息 二维数组
                $anchorMoreInfo  = $this->getVideoService()->setUid($res[0]['uid'])->getVideoList();
                if($anchorMoreInfo === false)
                {
                    $res[0]['vtype'] = 0;
                    $res[0]['lvid'] = '';
                    $res[0]['poster'] = '';
                    $res[0]['videoUrl'] = '';
                    $res[0]['orientation'] = '';
                }else
                {
                    $res[0]['vtype'] = 2;
                    $res[0]['lvid'] = $anchorMoreInfo[0]['videoID'];
                    $res[0]['poster'] = isset($anchorMoreInfo[0]['poster']) ? $anchorMoreInfo[0]['poster'] : '';
                    $res[0]['videoUrl'] = isset($anchorMoreInfo[0]['videoUrl']) ? $anchorMoreInfo[0]['videoUrl'] : '';
                    $res[0]['orientation'] = isset($anchorMoreInfo[0]['orientation']) ? $anchorMoreInfo[0]['orientation'] : '';
                }

            }

            return $res;
        }
    /**
     * 根据认证id 返回认证信息
     * @param $data
     * @return array
     */
    public function getCertDetailByCertId($data)
    {
        $data = $this->dueCertData()->getCertByCertId($data);
        if($data === false)
        {
            $code   = self::ERROR_ALL_CERTLIST;
            $msg    = self::$errorMsg[$code].$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            return $data;
        }
    }
    /**
     * 获取评论总数按技能id
     * @param $skillId
     * @return int
     */
    public function getCommentTotalBySkillId($skillId)
    {
       $data =  $this->dueCommentData()->getTotal($skillId);
        if($data === false)
        {
            $code   = self::ERROR_ALL_COMMENTLIST;
            $msg    = self::$errorMsg[$code].$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return 0;
        }else
        {
            return $data['num'];
        }

    }
    /**
     * 获取所有标签名数据按tagId
     * @param $tagId
     * @return bool|int
     */
    public function getTagByTagId($tagId)
    {
        $tag = $this->dueTagData()->getAllTags();
        if(false === $tag)
        {
            $code   = self::ERROR_GET_TAG;
            $msg    = self::$errorMsg[$code].$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return 0;
        }
        foreach($tag as $t)
        {
            if($t['id'] == $tagId)
            {
                return $t['tag'];
            }
        }
        return false;
    }
    /**
     * 获取主播资质列表
     *@return array
     */
    public function getCertByUid()
    {
        $data = $this->dueCertData()->getAllCert();
        if($data === false)
        {
            $code   = self::ERROR_ALL_CERTLIST;
            $msg =  self::$errorMsg[$code] .$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            return $data;
        }

    }
    /**
     * 主播自己获取主播资质列表
     *@return array
     */
    public function getAdminCertByUid()
    {
        $data = $this->dueCertData()->getAdminCert();
        if($data === false)
        {
            $code   = self::ERROR_ALL_CERTLIST;
            $msg =  self::$errorMsg[$code] .$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            render_error_json($msg,$code);
        }
        return $data;
    }
    /**
     * 按certId 获取资质
     * @param $data
     * @return array|bool|\PDOStatement
     */
    public function getCertByCertId($postData)
    {
        $res = [];
        $requirRule = ['certId'=>'NOTNULL'];
        $this->checkParams($postData,$requirRule);
        //获取最新的资质
        $data = $this->dueCertData()->getAdminCertByCertId($postData);
        if(!$data)
        {
            $code   = self::ERROR_ALL_CERTLIST;
            $msg =  self::$errorMsg[$code] .$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            render_error_json($msg,$code);
        }else
        {
            //格式化是否通过
            $status = $this->setStatus($data[0]['status']);
            //判断是否通过审核
            // -1,审核中.1,机器审核通过.2,人工审核通过.3,机器审核未通过 4,人工审核未通过
            if($status == self::CERT_STATUS_WAIT)
            {
                $code   = self::CERT_STATUS_WAIT;
                $msg    = ['status'=>$code,'desc'=>self::$errorMsg[$code]];
                render_json($msg);
            }
            if($status == self::CERT_STATUS_PASS)
            {
                $data[0]['status'] = 1;
                foreach($data as $key=>$value)
                {
                    $gameName = $this->getGameNameByGameId($value['game_id']);
                    $data[$key]['gameName'] = $gameName;
                }
                $res = $data;
            }
            if($status == self::CERT_STATUS_UNPASS)
            {
                foreach($data as $key=>$value)
                {
                    $gameName = $this->getGameNameByGameId($value['game_id']);
                    $data[$key]['gameName'] = $gameName;
                }
                $res = $data;
                $code   = self::CERT_STATUS_UNPASS;
                $imgDomain = $this->getImageDomain();
                $res['imgDomain']   = $imgDomain;
                $msg    = ['status'=>$code,
                            'desc'=>self::$errorMsg[$code],
                            'certId'=>$data[0]['certId'],
                            'gameId'=>$data[0]['game_id'],
                            'gameName'=>$data[0]['gameName'],
                            'picUrls'=>$data[0]['pic_urls'],
                            'info'=>$data[0]['info'],
                            'reason'=>$data[0]['reason'],
                            'imgDomain'=>$imgDomain,
                ];
                render_json($msg);

            }
            return $res;
        }
    }
    
    /**
     * 根据认证ID返回认证信息
     * @param $certId
     * @return array
     */
    public function getCertInfoByCertIds($certId) {
        $res = [];
        $postData = ['certId'=>$certId];
        $requirRule = ['certId'=>'NOTNULL'];
        $this->checkParams($postData,$requirRule);
        $data = $this->dueCertData()->getCertByCertId($postData);
        foreach ($data as $row){
            $res[$row['certId']] = $row;
        }
        return $res;
    }

    /**
     * 是否是约玩主播
     *即存在通过审核的资质
     * @return bool
     */
    public function isDueAnchor()
    {
        //获取资质列表
        $certList = $this->getCertByUid();
        if(count($certList) > 0)
        {
            foreach ($certList as $value)
            {
                $status = DueCertService::setStatus($value['status']);
                if($status == self::CERT_STATUS_PASS)
                {
                    return true;
                }
            }
        }
        return false;

    }
    /**
     * 主播是否有技能开关为开
     * @return bool
     */
    public function isAnchorSkillOn()
    {
            $skillList = $this->getSkillByUid();
            if(count($skillList) > 0)
            {
                foreach ( $skillList as $value)
                {
                    if($value['switch'] == self::SKILL_SWITCH_ON)
                    {
                        return true;
                    }
                }
            }
        return false;
    }
    public function resetDueSkillCache()
    {
        $key = self::DUESKILL_SWITCH_KEY.$this->_uid;
        $skillList = $this->dueSkillData()->getAllSkill();
        if(!$skillList)
        {
            $code   = self::ERROR_ALL_SKILLLIST;
            $msg    = self::$errorMsg[$code].$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return false;
        }
        if(count($skillList) > 0)
        {
            $jsonData = hp_json_encode($skillList);
           $res =  $this->getRedis()->set($key,$jsonData);
           if($res)
           {
               $this->getRedis()->expire($key,self::DUESKILL_SWITCH_KEY_EXPIRE);
               $log = "resetDueSkillCache:".$jsonData." |expired: ".self::DUESKILL_SWITCH_KEY_EXPIRE."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
               write_log($log);
               return true;
           }
           else
           {
               $log = "error: redis resetDueSkillCache failed :".$jsonData." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
               write_log($log);
               return false;
           }
        }
        return false;
    }
    
    /**
     * 获取主播技能列表 不能更改返回 其他服务层调用
     *@return array
     */
    public function  getSkillByUid()
    {
        $key = self::DUESKILL_SWITCH_KEY.$this->_uid;
        $skillListJson = $this->getRedis()->get($key);
        if($skillListJson === false)
        {
            $data = $this->dueSkillData()->getAllSkill();
            if($data ===  false)
            {
                $code   = self::ERROR_ALL_SKILLLIST;
                $msg    = self::$errorMsg[$code].$this->_uid;
                $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
                write_log($log);
                return [];
            }else
            {
                $jsonData = hp_json_encode($data);
                $res = $this->getRedis()->set($key,$jsonData);
                if($res)
                {
                    $this->getRedis()->expire($key,self::DUESKILL_SWITCH_KEY_EXPIRE);
                    $log = "redis set DueSkilCache :".$jsonData." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                    write_log($log);
                }else
                {
                    $log = "error: redis resetDueSkillCache failed :".$jsonData." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                    write_log($log);
                }
                return $data;
            }
        }else
        {
            $data = json_decode($skillListJson,true);
            return $data;
        }
    }

    /**
     * 初始化技能数据
     * @return array
     */
    public function initSkillData()
    {
        $data = [
            'price'=>'100',
            'unit'=>'1',
            'switch'=>'-1',
        ];
        return $data;
    }
    /**
     * 添加技能数据 更新技能数据 更新开关状态 @（第一次打开开关才会插入初始数据）改为后台插入初始数据
     * @param $data
     * @return bool
     */
    public function addSkill($data)
    {
        $op = '';
        $requirRule = ['skillId'=>'NOTNULL','certId'=>'NOTNULL','gameId'=>'NOTNULL','price'=>'NOTNULL','unit'=>'NOTNULL','switch'=>'NOTNULL'];
        //此处验证data数据是否合法
        $this->checkParams($data,$requirRule);
        switch($data['switch'])
        {
            //关闭技能
            case self::SKILL_SWITCH_OFF:
                $op = $this->dueSkillData()->updateSkillBySkillId($data);
                $this->resetDueSkillCache();
                break;
            //打开技能
            case self::SKILL_SWITCH_ON:
                //增加验证游戏是否下架
                $gameData = $this->dueGameData()->getGameInfoByGameId($data['gameId']);
                foreach($gameData as $value)
                {
                    if(isset($value['status']))
                    {
                        if($value['status'] == self::GAME_DOWN)
                        {
                            $code = self::GAME_DOWN;
                            $msg  = self::$errorMsg[$code];
                            render_error_json($msg,$code);
                        }
                    }
                }
                //验证是否首次添加 用gameId
                $res = $this->dueSkillData()->getSkillBygameId($data);
                if(false === $res)
                {
                    $code = self::ERROR_ALL_DATAOPTION;
                    $msg  = self::$errorMsg[$code];
                    render_error_json($msg,$code);
                }
                //如果从未添加过技能 则添加一条
               if( is_array($res) && count($res) == 0)
                {
                    $op = $this->dueSkillData()->addSkill($data);
                }
                else
                {
                    $op = $this->dueSkillData()->updateSkillBySkillId($data);
                    $this->resetDueSkillCache();
                }
                break;
            default:
                break;
        }
        if(false === $op)
        {
            $code = self::ERROR_ALL_DATAOPTION;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }
        return true;
    }
    /**
     * 获取主播技能 按技能id
     * @param $data
     * @param string $order 排序规则
     * @return bool|\PDOStatement
     */
    public function  getSkillBySkillId($data,$order='')
    {
        $requirRule = ['skillId'=>'NOTNULL'];
        $this->checkParams($data,$requirRule);
            $data = $this->dueSkillData()->getSkillBySkillId($data,$order);
            if(!$data)
            {
                $code   = self::ERROR_ALL_CERTLIST;
                $msg =  self::$errorMsg[$code] .$this->_uid;
                $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
                write_log($log);
                render_error_json($msg,$code);
            }
        return $data;
    }

    /**
     * 获取订单总数 按skillId
     * @param $skillId
     * @return array|string
     */
    public function getOrderTotalBySkillId($skillId)
    {
        //$data = $this->dueOrderData()->getOrderTotalBySkillId($skillId);
        if(!is_array($skillId))
        {
            $skillId = array($skillId);
        }
        //获取所有订单号和技能
        $allOrder = $this->dueOrderData()->getAllOrderBySkillId($skillId);
        if(false === $allOrder)
        {
            $code   = self::ERROR_ORDER_TOTAL;
            $msg    = self::$errorMsg[$code].$this->_uid;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            //判断是否有订单
            if(count($allOrder)>0)
            {
                //拿到订单号
                $allOrderIds = array_column($allOrder,'order_id');
                //获取所有订单状态
                $allOrderStatus = $this->dueOrderData()->getOrderStatusByOrderId($allOrderIds);
                if(false === $allOrderStatus)
                {
                    $code   = self::ERROR_ORDER_TOTAL;
                    $msg    = self::$errorMsg[$code].$this->_uid;
                    $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
                    write_log($log);
                    return [];
                }else
                {
                    //订单完成订单号
                    $orderEndIds =[];
                    //获得完成订单号
                    foreach ($allOrderStatus as $value)
                    {
                        if($value['status'] == self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT)
                        {
                            $orderEndIds[] =$value['order_id'];
                        }
                    }
                    //如有完成订单
                    if(count($orderEndIds)>0)
                    {
                        $orderSkill = [];
                        $skillArray = [];
                        $data =[];
                        //获取订单号为索引的数组
                        foreach ($allOrder as $value)
                        {
                            $orderSkill[$value['order_id']] = $value['skill_id'];
                        }
                        foreach ($orderSkill as $key=>$skill)
                        {
                            //找出完成订单的技能
                            if(in_array($key,$orderEndIds))
                            {
                                $skillArray[] = $skill;
                            }
                        }
                        //生成技能订单数量数据array('skillId'=>order_total);
                        $skillCountData = array_count_values($skillArray);
                        $i = 0;
                        foreach ($skillCountData as $key=>$value)
                        {
                            $data[$i]['skill_id'] = $key;
                            $data[$i]['order_total'] = $value;
                            $i++;
                        }
                        return $data;
                    }
                }
            }
        }
        return [];
    }

    /**
     * 获取评论 按技能ID
     * @param $data['skillId]
     * @return array|bool
     */
    public function getCommentBySkillId($data)
    {
        $requirRule = ['skillId'=>'NOTNULL'];
        $this->checkParams($data,$requirRule);
        //默认初始化技能
        if($data['skillId'] == -1)
        {
            return [];
        }
        $res = $this->dueCommentData()->getCommentBySkillId($data);
        if(false === $res)
        {
            $code   = self::ERROR_ALL_COMMENTLIST;
            $msg    = self::$errorMsg[$code].$this->_uid;;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            return $res;
        }

    }

    /**
     * 获取评论列表 分页
     * @param $postData['page'] 页码  $postData['pageSize'] limit值
     * @return array|bool
     */
    public function getCommentList($postData)
    {
        $requirRule = ['skillId'=>'NOTNULL','page'=>'NOTNULL','pageSize'=>'NOTNULL'];
        $this->checkParams($postData,$requirRule);
        $data = $this->dueCommentData()->getCommentBySkillId($postData);
        if(false === $data)
        {
            $code   = self::ERROR_ALL_COMMENTLIST;
            $msg    = self::$errorMsg[$code].$this->_uid;;
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            $res =[];
            $total = count($data);
            if($total >0)
            {
                $uidArr = [];
                //拼接其他数据
                foreach($data as $key=>$value)
                {
                    //获取所有用户信息
                    $uids[] = $value['uid'];
                    $uidArr = array_unique($uids);
                }
                $userInfo = $this->userInfo();
                $userInfo->setUid($uidArr);
                $userInfoArr = $userInfo->batchGetUserInfo();
                foreach($data as $key=>$value)
                {
                    $res[$key]['nick'] = $userInfoArr[$value['uid']]['nick'];
                    $res[$key]['pic'] = $userInfoArr[$value['uid']]['pic'];
                    $tagName = $this->getTagService()->getTagsByids($value['tag_ids']);
                    $res[$key]['tags']= $tagName;
                }
            }
            return $res;
        }
    }

    /**
     *返回评论总数 分页用
     * @param $postData
     * @return int
     */
    public function getCommnetTotal($postData)
    {
        $requirRule = ['skillId'=>'NOTNULL'];
        $this->checkParams($postData,$requirRule);
        $data = $this->dueCommentData()->getCommnetTotal($postData);
        if($data === false)
        {
            return 0;
        }else
        {
            return $data[0]['total'];
        }

    }

    /**
     * 获取陪玩游戏列表
     * @return array
     */
    public function getDueGameList()
    {
      
         $RecommendGameList = $this->dueGameData()->getAllGameList();
        if($RecommendGameList == false)
        {
            $code = self::ERROR_GAME_LIST;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }else
        {
            return $RecommendGameList;
        }
    }
    /**
     * 获取陪玩技能价格列表
     *
     */
     public function getSkillPriceList($postData)
     {
         $requirRule = ['unit'=>'NOTNULL'];
         $this->checkParams($postData,$requirRule);
          $priceList = [
              ['price'=>'60','priceName'=>'60 欢朋币/局'],
              [ 'price'=>'80','priceName'=>'80 欢朋币/局'],
              ['price'=>'100','priceName'=>'100 欢朋币/局'],
              ['price'=>'120','priceName'=>'120 欢朋币/局'],
              ['price'=>'160','priceName'=>'160 欢朋币/局'],
              ['price'=>'180','priceName'=>'180 欢朋币/局'],
     ];
         return $priceList;
     }
    /**
     * 获取游戏名称
     * @param $gameId
     * @return bool|string
     */
    public function getGameNameByGameId($gameId)
    {
        $RecommendGameList = $this->getDueGameList();
      foreach($RecommendGameList as $key=>$value)
        {
            if($value['gameid'] == $gameId)
            {
                return $value['name'];
            }
        }
        return false;
    }
    /**
     * 添加资质认证
     * @param $data
     * @return bool
     */
    public function addCert($data)
    {
        $op = '';
        $requirRule = ['option' =>'NOTNULL','gameId'=>'NOTNULL','picUrls'=>'NOTNULL','info'=>'1'];
        //此处验证data数据是否合法
        $this->checkParams($data,$requirRule);
        //验证描述是否合法
        $this->apiCommon()->textFilter($this->_uid,$data['info'],TextService::CHANNEL_THEME);
        //验证是否首次添加
        $res = $this->dueCertData()->getCertByGameid($data);
        if(false === $res)
        {
            $code = self::ERROR_ALL_DATAOPTION;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }
        else if( is_array($res) && count($res) == 0)
        {
            //限制主播添加资质次数
            $certInfo = $this->dueCertData()->getAllCert();
            if(count($certInfo)>=self::ALLOW_DUE_CERT_LIST)
            {
                $code = self::ERROR_CERT_MAX;
                $msg  = self::$errorMsg[$code];
                render_error_json($msg,$code);
            }
            $op = $this->dueCertData()->addCert($data);
        }
        else
        {
            if($data['option'] == self::DATA_OPTION)
            {
                $code = self::IS_EXIST_CERT;
                $msg  = self::$errorMsg[$code];
                render_error_json($msg,$code);
            }
            //其他操作走更新
            $requirRule = ['certId'=>'NOTNULL','gameId'=>'NOTNULL','picUrls'=>'NOTNULL','info'=>'1'];
            //此处验证data数据是否合法
            $this->checkParams($data,$requirRule);
            $op = $this->dueCertData()->updateCert($data);
            //此处关闭已有技能
            if($op)
            {
                $data['switch'] = self::SKILL_SWITCH_OFF;
                $op = $this->dueSkillData()->updateSwitchByGameId($data);
            }

        }


        if(false === $op)
        {
            $code = self::ERROR_ALL_DATAOPTION;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

    return true;
    }

    /**
     * 添加或更新技能
     * @param $data
     * @return bool
     */
    public function updateSkillBySkillId($data)
    {
        $requirRule = ['skillId'=>'NOTNULL','certId'=>'NOTNULL','gameId'=>'NOTNULL','price'=>'NOTNULL','unit'=>'NOTNULL'];
        //此处验证data数据是否合法
        $this->checkParams($data,$requirRule);
        //此处验证身份是否合法
        $certInfo = $this->dueCertData()->getCertByCertId($data);
        $certStatus = $this->setStatus($certInfo[0]['status']);
        //资质审核通过
        if($certStatus == self::CERT_STATUS_PASS)
        {
            if($data['skillId'] == -1)
            {
                $data['switch'] = '-1';
                $op = $this->dueSkillData()->addSkill($data);
            }else{
                $op = $this->dueSkillData()->updateSkillBySkillId($data);
                if(false === $op)
                {
                    $code = self::ERROR_ALL_DATAOPTION;
                    $msg  = self::$errorMsg[$code];
                    render_error_json($msg,$code);
                }
            }
        }else
        {
            $code = self::CERT_STATUS_WAIT;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        return true;
    }
    /**
     * 验证必要参数以及状态
     * @param $data @待验证数据
     * @param $require @验证数据规则
     * @return bool
     */
    public  function checkParams($data,$requirRule)
    {
        foreach ($requirRule as $k=>$v)
        {
            $paraKey = array_key_exists($k, $data);
            if ( $paraKey )
            {
                //规则
                switch($v)
                {
                    //不能为空
                    case 'NOTNULL':
                        if(empty($data[$k]))
                        {
                            $code = self::ERROR_NOT_NULL;
                            $msg  = self::$errorMsg[$code].''.$k;
                            render_error_json($msg,$code);
                        }
                        break;
                    default:
                        return true;
                }
            }
            else
            {
                $code = self::ERROR_PARAM;
                $msg  = self::$errorMsg[$code].''.$k;
                render_error_json($msg,$code);
            }
        }
        return true;
    }

}