<?php

/**
 * 主播兑换记录
 */

namespace lib;

use \DBHelperi_huanpeng;
use lib\Anchor;
use \RedisHelp;
use lib\Finance;
use Exception;

class AnchorExchange {

    /**
     * 0默认 
     */
    const EXCHANGE_STATUS_00 = 0;

    /**
     * 1创建 
     */
    const EXCHANGE_STATUS_01 = 1;

    /**
     * 2审核
     */
    const EXCHANGE_STATUS_02 = 2;

    /**
     * 3成功
     */
    const EXCHANGE_STATUS_03 = 3;

    /**
     * 4失败
     */
    const EXCHANGE_STATUS_04 = 4;
    const EXCHANGE_TABLE_CREATE_KEY = 'exchangeTbaleCreateKey';

    public static $status = [
        self::EXCHANGE_STATUS_00 => '默认',
        self::EXCHANGE_STATUS_01 => '创建',
        self::EXCHANGE_STATUS_02 => '审核',
        self::EXCHANGE_STATUS_03 => '成功',
        self::EXCHANGE_STATUS_04 => '失败',
    ];
    public $uid = '';
    public $db;
    public $tableName;

    const TABLE_NAME_PRE = 'exchange_detail_';
    const TABLE_NAME_TEMPLATE = 'exchange_detail_template';

    public function __construct($uid = '', $tableName = '', $db = '') {
        if ($uid) {
            $this->uid = (int) $uid;
        }
        if ($db) {
            $this->db = $db;
        } else {
            $this->db = new DBHelperi_huanpeng();
        }
        $this->_checkTable($tableName);
    }

    public static function getCreateTableSql() {
        $sql = "CREATE TABLE IF NOT EXISTS `exchange_detail_template` (
  `id`          int(11)                 NOT NULL AUTO_INCREMENT,
  `otid`        BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '映射ID',
  `tid`         BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '财务返回ID',
  `uid`         int(10)      UNSIGNED   NOT NULL DEFAULT '0' COMMENT '用户ID',
  `beforefrom`  BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换前被兑换项数值',
  `beforeto`    BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换前兑换成的数值',
  `afterfrom`   BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换后被兑换项数值',
  `afterto`     BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换后兑换成的数值',
  `message`     varchar(300)            NOT NULL DEFAULT ''  COMMENT '兑换描述',
  `number`      BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `type`        tinyint(3)   UNSIGNED   NOT NULL DEFAULT '0' COMMENT '兑换方式',
  `status`      tinyint(3)   UNSIGNED   NOT NULL DEFAULT '0' COMMENT '记录状态 0默认 1创建 2审核 3成功 4失败',
  `ctime`       timestamp               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime`       timestamp               NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        return $sql;
    }

    public static function getRedisCacheKey($tableName) {
        return self::EXCHANGE_TABLE_CREATE_KEY . "_" . $tableName;
    }

    /**
     * 建表
     * @param type $tableName
     */
    protected function createTable($tableName) {
        $redisObj = new RedisHelp();
        $cacheKey = self::getRedisCacheKey($tableName);
        if (FALSE === $redisObj->get($cacheKey)) {
            $sql = self::getCreateTableSql();
            $createSql = str_replace(self::TABLE_NAME_TEMPLATE, $tableName, $sql);
            $res = $this->db->query($createSql);
            if (TRUE == $res) {
                $redisObj->set($cacheKey, 'created', 7200);
            }
        }
    }

    /**
     * 检查
     * @param type $tableName
     * @return type
     * @throws Exception
     */
    public function _checkTable($tableName = null) {
        if (empty($tableName)) {
            $tableName = self::TABLE_NAME_PRE . date('Ym');
            $this->createTable($tableName);
        } else {
            $patt = '/^exchange_detail_\d{6}$/';
            $res = preg_match($patt, $tableName);
            if ($res) {
                $this->createTable($tableName);
            } else {
                throw new \Exception("The tables {$tableName}  not exist");
            }
        }
        $this->tableName = $tableName;
        return;
    }

    public function getRowByUidAndType($uid, $type, $tableNmae = null) {
        $tableNmae = $tableNmae ? $tableNmae : $this->tableName;
        $where = array(
            'uid' => $uid,
            'type' => $type
        );
        $res = $this->db->where($where)->select($tableNmae);
        return $res;
    }

    /**
     * 
     * @param type $data
     * $data =[
      "otid"=>'',
      "tid"=>'',
      "uid"=>'',
      "beforefrom"=>'', //兑换前的值   实际值一千倍
      "beforeto"=>'',  //兑换前的值    实际值一千倍
      "afterfrom"=>'',  //兑换后的值   实际值一千倍
      "afterto"=>'',   //兑换后的值    实际值一千倍
      "message" =>'', //描述
      "number"=>'',  // 兑换数量   整数
      "type"=>'',   //兑换类型     Finane.php中设置的兑换类型
      "status"=>'',  //兑换状态
      "ctime"=>'',   //创建时间
      "utime"=>'',]   //确认时间(提现使用)
     * 
     * @return boolean
     */
    public function insert($data) {
        if (empty($data)) {
            return FALSE;
        }
        $otid = isset($data['otid']) ? $data['otid'] : 0;
        if ($otid > 0) {
            $time = substr(strval($otid), 0, 10);
            $tableName = self::TABLE_NAME_PRE . date('Ym', $time);
            $this->_checkTable($tableName);
        }
        $this->dataFormat($data);
        $id = $this->db->insert($this->tableName, $data);
        return $id;
    }

    /**
     * update by id
     * @param type $id
     * @param type $data
     * @return type
     */
    public function updateById($id, $data) {
        //create table
        if (intval($id) > 0 && is_array($data)) {
            $this->dataFormat($data);
            $rows = $this->db->where("id={$id}")->update($this->tableName, $data);
        }
        return $rows;
    }

    /**
     * update by  otid
     * @param type $otid
     * @param type $data
     * @return type
     */
    public function update($otid, $data) {
        //create table
        $time = substr(strval($otid), 0, 10);
        $tableName = self::TABLE_NAME_PRE . date('Ym', $time);
        $this->_checkTable($tableName);
        if (intval($otid) > 0 && is_array($data)) {
            $this->dataFormat($data);
            $rows = $this->db->where("otid={$otid}")->update($tableName, $data);
        }
        return $rows;
    }

    /**
     * 数据格式化
     * @param type $data
     */
    public function dataFormat(&$data) {
        if (isset($data['beforefrom'])) {
            $data['beforefrom'] = $data['beforefrom'] * 1000;
        }
        if (isset($data['beforeto'])) {
            $data['beforeto'] = $data['beforeto'] * 1000;
        }
        if (isset($data['afterfrom'])) {
            $data['afterfrom'] = $data['afterfrom'] * 1000;
        }
        if (isset($data['afterto'])) {
            $data['afterto'] = $data['afterto'] * 1000;
        }
    }
    
    
    /**
     * 金豆换金币
     * @param type $anchorUid
     * @param type $number
     * @return boolean
     * @throws Exception
     */
    public function beanToCoin($anchorUid, $number,$otid=0) {

        $this->db->autocommit(false);
        $this->db->query('begin');
        try {
            $anchor = new Anchor($anchorUid, $this->db);
//            $anchorCertInfo = $anchor->getAnchorCertInfo();
            $anchorMoney = $anchor->getAnchorProperty();
			$otid = $otid ? $otid : getOtid();
            $desc = "用户{$anchorUid}用金豆兑换金币:{$number}";
            $data = array(
                'otid' => $otid,
                'uid' => $anchorUid,
//                'cid' => $anchorCertInfo['cid'],
                'type' => Finance::EXC_GD_GB,
                 'beforefrom' => $anchorMoney['bean'],
            'beforeto' => $anchorMoney['coin'],
                'afterfrom' => 0,
                'afterto' => 0,
                'number' => $number,
                'message' => $desc,
                'ctime' => date('Y-m-d H:i:s'),
                'status' => AnchorExchange::EXCHANGE_STATUS_01
            );
            $insertId = $this->insert($data);
            if (!$insertId) {
//				var_dump("写入失败");
                throw new Exception('写入失败');
            }
            $finance = new Finance();
            $res = $finance->excGD2GB($anchorUid, $number, $desc, $otid);
            if ($finance->checkBizResult($res)) {
                //更新数据
                $anchor->updateAnchorCoin($res['gb']);
				$anchor->updateAnchorBean($res['gd']);
                $updata = array(
                    'afterfrom' => $res['gb'],
                    'tid' => $res['tid'],
                    'afterto' => $res['gb'],
                    'status' => AnchorExchange::EXCHANGE_STATUS_02
                );
                $upResult = $this->update($otid, $updata);
                if (!$upResult) {
                    throw new Exception('更新失败');
                }
            } else {
                throw new Exception('扣费失败');
            }
            $this->db->commit();
            $return = TRUE;
        } catch (\Exception $e) {
            $this->db->rollback();
            $return = FALSE;
        }
        $this->db->autocommit(true);
        return $return;
    }

    /**
     * 金币提现
     * @param type $anchorUid
     * @param type $number
     * @return boolean
     * @throws Exception
     */
    public function coinToCNY($anchorUid, $number,$otid=0) {
        $this->db->autocommit(false);
        $this->db->query('begin');
        try {
            $anchor = new Anchor($anchorUid, $this->db);
//            $anchorCertInfo = $anchor->getAnchorCertInfo();
            $anchorMoney = $anchor->getAnchorProperty();
            $otid = $otid ? $otid : getOtid();
            $desc = "用户{$anchorUid}提现人民币:{$number}";
            $data = array(
                'otid' => $otid,
                'uid' => $anchorUid,
//                'cid' => $anchorCertInfo['cid'],
                'type' => Finance::EXC_GB_RMB,
                'beforefrom' => $anchorMoney['coin'],
                'beforeto' => 0,
                'afterfrom' => 0,
                'afterto' => 0,
                'number' => $number,
                'message' => $desc,
                'ctime' => date('Y-m-d H:i:s'),
                'status' => AnchorExchange::EXCHANGE_STATUS_01
            );
            $insertId = $this->insert($data);
            if (!$insertId) {
                throw new Exception('写入失败');
            }
            $finance = new Finance();
            $res = $finance->withdraw($anchorUid, $number, $desc, $otid);
            if ($finance->checkBizResult($res)) {
                //更新数据
                $anchor->updateAnchorCoin($res['gb']);
                $updata = array(
                    'afterfrom' => $res['gb'],
                    'tid' => $res['tid'],
                    'afterto' => 0,
                    'status' => AnchorExchange::EXCHANGE_STATUS_02
                );
                $upResult = $this->update($otid, $updata);
                if (!$upResult) {
                    throw new Exception('更新失败');
                }
            } else {
                throw new Exception('扣费失败');
            }
            $this->db->commit();
            $return = TRUE;
        } catch (Exception $e) {
            $this->db->rollback();
            $return = FALSE;
        }
        $this->db->autocommit(true);
        return $return;
    }

    public function refund($orderid, $uid, $gb, $desc, $otid)
	{
		$this->db->query('begin');
		$this->db->autocommit(false);

		try{
			$data  = [
				'status' => self::EXCHANGE_STATUS_04
			];

			$updateResult = $this->update($orderid, $data);
			if(!$updateResult)
			{
				throw new Exception("更新状态失败");
			}

			$finance = new Finance();
			//todo 退款应该使用财务的订单ID 来进行退款，而不是使用外部传入金额进行退款
			$res = $finance->withdrawRefund($uid,$gb,$desc,$otid);
			if(Finance::checkBizResult($res))
			{
				$anchor = new Anchor($uid, $this->db);

				$anchor->updateAnchorCoin($res['gb']);
			}
			else
			{
				throw new Exception('退款失败');
			}

			$this->db->commit();

			$return = $res['tid'];
		}
		catch (Exception $e)
		{
			$this->db->rollback();
			$return = false;
		}

		$this->db->autocommit(true);

		return $return;
	}

	public function success($orderid)
	{

		$data = ['status'=>self::EXCHANGE_STATUS_03];
		$this->update($orderid, $data);
	}

	public function getOrderInfo($orderid)
	{
		$time = substr(strval($orderid), 0, 10);
		$tableName = self::TABLE_NAME_PRE . date('Ym', $time);
		$this->_checkTable($tableName);

		$sql = "select * from $tableName where otid = $orderid";
		$res = $this->db->query($sql);
		if(!$res)
		{
			return false;
		}

		$row = $res->fetch_assoc();

		return $row;
	}
}
