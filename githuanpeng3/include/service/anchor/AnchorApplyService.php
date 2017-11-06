<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/14
 * Time: 11:39
 */

namespace service\anchor;
use lib\anchor\Company;
use service\common\AbstractService;
use lib\anchor\AnchorApplyCompany;
use lib\Anchor;
use service\common\ApiCommon;
/**
 * 主播签约服务
 * Class AnchorApplyService
 * @package service\anchor
 */
class AnchorApplyService extends AbstractService
{
    //获取主播技能评论列表失败
    const ERROR_GET_DATA= -5002;
    const ERROR_ANCHOR_CERT= -3057;
    const ERROR_ANCHOR_COMPANY = -4096;
    const IS_APPLYING = -100001;
    const IS_APPLY =-100002;
    const ERROR_DATA_OP = -8005;
    const ERROR_CANCEL_OP =-100003;
    //经济公司正常使用状态
    const COMPANY_USE_SATUS = 0;
    //是否申请状态中
    const IS_APPLY_STATUS = 1;
    //是否不在申请状态
    const NOT_APPLY_STATUS = 0;

    public static $errorMsg =[
        self::ERROR_GET_DATA        => '获取数据发生意外',
        self::ERROR_ANCHOR_CERT     =>'对不起,主播未认证',
        self::ERROR_ANCHOR_COMPANY  =>'经济公司不存在',
        self::IS_APPLY              =>'已经签约，无法重复签约',
        self::IS_APPLYING           =>'正在申请中，无法重复申请签约',
        self::ERROR_DATA_OP       =>'糟糕！数据写入失败',
        self::ERROR_CANCEL_OP       =>'找不到签约，无法取消',
        ];
    const SHOW_TEXT_STATUS_00 ='去签约';
    const SHOW_TEXT_STATUS_01 ='审核中';
    const SHOW_TEXT_STATUS_02 ='未通过';
    private $_uid;
    public  $redis;
    //公司数据服务
    public function companyData()
    {
        return new Company();
    }
    //主播签约公司服务
    public function anchorApplyCompany()
    {
        return new AnchorApplyCompany();
    }
    //获取主播数据
    public function anchorData()
    {
        return new Anchor($this->getUid());
    }
    //获取主播认证信息
    public function getAnchorCertInfo()
    {
        $data = $this->anchorData()->getAnchorCertInfo();
        return $data;
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
    //获取申请状态 0未审核 1主播取消 2公司审核通过 3公司审核不通过 4运营审核通过 5运营审核不通过
    public function getApplyStatus($status)
    {
        $status_value = [
            '0'=>self::IS_APPLY_STATUS,
            '1'=>self::NOT_APPLY_STATUS,
            '2'=>self::IS_APPLY_STATUS,
            '3'=>self::NOT_APPLY_STATUS,
            '4'=>self::IS_APPLY_STATUS,
            '5'=>self::NOT_APPLY_STATUS,
        ];
        return $status_value[$status];
    }
    /**
     * 获取正常经济公司列表
     * @return array|bool
     */
    public function getCompanyList()
    {
        $res = [];
        $companyData = $this->getAllCompany();
        if(empty($companyData))
        {
            $code   = self::ERROR_GET_DATA;
            $msg    = self::$errorMsg[$code];
            render_error_json('经济公司'.$msg,$code);
            return false;
        }
        else
        {
            foreach ($companyData as $value)
            {
                if($value['status'] == self::COMPANY_USE_SATUS)
                {
                    $res[] = $value;
                }
            }
            return $res;
        }
    }

    /**
     * 获取所有经济公司信息
     * @return array|bool|\PDOStatement
     */
    public function getAllCompany()
    {
        $data = $this->companyData()->getAllCompany();
        if($data === false)
        {
            $code   = self::ERROR_GET_DATA;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return [];
        }else
        {
            return $data;
        }
    }

    /**
     * 获取经济是公司是否存在
     * @param $data
     * @return bool
     */
    public function isExistCompany($data)
    {
        $cid = $data['cid'];
        $companyData = $this->getCompanyList();
        foreach ($companyData as $value)
        {
            if($cid == $value['id'])
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取主播最后一条签约
     * @return array|bool|\PDOStatement
     */
    public function getLastAnchorApply()
    {
        $data = $this->anchorApplyCompany()->getLastAnchorApply($this->getUid());
        if($data === false)
        {
            $code   = self::ERROR_GET_DATA;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return false;
        }else
        {
            return $data;
        }
    }
    /**
     * 获取主播申请中 签约    #判断是否签约申请中（0 申请中2 经济公司通过 4 运营通过）
     * @param $data
     * @return bool
     */
    public function isAllowApply($data)
    {
        $anchor = $this->getAnchorCertInfo();
        //判断是否认证主播
        if( $anchor['cert_status'] == 0)
        {
            $code   = self::ERROR_ANCHOR_CERT;
            $msg    = self::$errorMsg[$code];
            render_error_json($msg,$code);
            return false;
        }
        //判断是否有签约
        if($anchor['cid'] != 0)
        {
            $code   = self::IS_APPLY;
            $msg    = self::$errorMsg[$code];
            render_error_json($msg,$code);
            return false;
        }
        //判断公司是否存在
        if(!$this->isExistCompany($data))
        {
            $code   = self::ERROR_ANCHOR_COMPANY;
            $msg    = self::$errorMsg[$code];
            render_error_json($msg,$code);
            return false;
        }
        //判断最后一条签约是否在申请中
        $lastApplyData = $this->getLastAnchorApply();
        if(count($lastApplyData) > 0)
        {
            $applyStatus = $this->getApplyStatus($lastApplyData[0]['status']);
            if($applyStatus == self::IS_APPLY_STATUS)
            {
                $code   = self::IS_APPLYING;
                $msg    = self::$errorMsg[$code];
                render_error_json($msg,$code);
                return false;
            }
        }
        return true;

    }
    /**
     * 添加主播签约公司申请
     * @param $data
     */
    public function addAnchorApplyCompany($data)
    {
        #data应该此处验证这样方法便于统一修改 不必修改两处文件
        $requirRule = ['cid' =>'1','videoid'=>'1','gameid'=>'1','gamelevel'=>'1','qq'=>'1'];
        ApiCommon::checkParams($data,$requirRule);
        #判断是否签约申请中（status 1主播取消,3经济公司不通过,5运营不通过）
        if($this->isAllowApply($data))
        {
            $res = $this->anchorApplyCompany()->addAnchorApplyCompany($data);
            //插入失败
            if($res === false)
            {
                $code   = self::ERROR_DATA_OP;
                $msg    = self::$errorMsg[$code];
                render_error_json($msg,$code);
                return false;
            }
            //记日志 防止申请记录丢失
            write_log('主播申请 uid:' .$this->getUid().' | cid:'.$data['cid'].' | videoid:'.$data['videoid'],'apply_company.log');
            //返回aid
            $lastApplyData = $this->getLastAnchorApply();
            return $lastApplyData[0]['id'];
        }
        return false;
    }

    /**
     * 取消申请签约
     * @param $data
     * @return bool
     */
    public function cancelAnchorApply($data)
    {
        $requirRule = ['aid' =>'1'];
        ApiCommon::checkParams($data,$requirRule);
        //判断最后一条签约是否在申请中
        $lastApplyData = $this->getLastAnchorApply();
        if(count($lastApplyData) > 0)
        {
            //如果是申请中可以取消
            if( 0 == $lastApplyData[0]['status'])
            {
                $res = $this->anchorApplyCompany()->updateAnchorApplyCancelStatus($data);
                //插入失败
                if($res === false)
                {
                    $code   = self::ERROR_DATA_OP;
                    $msg    = self::$errorMsg[$code];
                    render_error_json($msg,$code);
                    return false;
                }
                //记日志 防止申请记录丢失
                write_log('主播取消申请 uid:' .$this->getUid().' | aid:'.$data['aid'],'apply_company.log');
                return true;
            }
        }
        $code   = self::ERROR_CANCEL_OP;
        $msg    = self::$errorMsg[$code];
        render_error_json($msg,$code);
        return false;
    }

    /**
     * 获取主播签约状态
     * @return array
     */
    public function getAnchorApplyStatusInfo()
    {
        $res = [];
        //验证主播是否有原始签约记录
        $anchor = $this->getAnchorCertInfo();
        if($anchor['cert_status'] == 1 && $anchor['cid'] > 0)
        {
            $res['aid'] = '';
            $res['cid'] = $anchor['cid'];
            $res['cname'] = $this->getCompanyName($anchor['cid']);
            $res['apply_status'] = '3';
            $res['reason'] ='';
        }else
        {
            $lastApplyData = $this->getLastAnchorApply();
            //签约无记录
            if(!$lastApplyData)
            {
                $res['aid'] = '';
                $res['cid'] = '';
                $res['cname'] = self::SHOW_TEXT_STATUS_00;
                $res['apply_status'] = '-1';
                $res['reason'] ='';
            }else
            {
                //0未审核 1主播取消 2公司审核通过 3公司审核不通过 4运营审核通过 5运营审核不通过 6 已解约
                switch ($lastApplyData[0]['status'])
                {
                    case 0:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] = self::SHOW_TEXT_STATUS_01;
                        $res['apply_status'] = '0';
                        $res['reason'] ='';
                        break;
                    case 1:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] = self::SHOW_TEXT_STATUS_00;
                        $res['apply_status'] = '-1';
                        $res['reason'] ='';
                        break;
                    case 2:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] = self::SHOW_TEXT_STATUS_01;
                        $res['apply_status'] = '1';
                        $res['reason'] ='';
                        break;
                    case 3:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] =  self::SHOW_TEXT_STATUS_02;
                        $res['apply_status'] = '2';
                        $res['reason'] = $lastApplyData[0]['companyreason'];
                        break;
                    case 4:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] = $this->getCompanyName($lastApplyData[0]['cid']);
                        $res['apply_status'] = '3';
                        $res['reason'] ='';
                        break;
                    case 5:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] =  self::SHOW_TEXT_STATUS_02;
                        $res['apply_status'] = '2';
                        $res['reason'] = $lastApplyData[0]['adminreason'];
                        break;
                    case 6:
                        $res['aid'] = $lastApplyData[0]['id'];
                        $res['cid'] = $lastApplyData[0]['cid'];
                        $res['cname'] = self::SHOW_TEXT_STATUS_00;
                        $res['apply_status'] = '-1';
                        $res['reason'] ='';
                        break;
                    default:
                        break;
                }
            }

        }
        return $res;
    }

    /**
     * 获取不到公司名
     * @param $cid
     * @return mixed|string
     */
    public function getCompanyName($cid)
    {
        $res = [];
        $allCompany = $this->getAllCompany();
        if(count($allCompany)>0)
        {
            foreach ($allCompany as $value)
            {
                $res[$value['id']] = $value['name'];
            }
            if(isset($res[$cid]))
            {
                return $res[$cid];
            }
        }
        return '';
    }
}