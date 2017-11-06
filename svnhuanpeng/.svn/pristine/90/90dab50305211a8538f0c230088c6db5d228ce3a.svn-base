<?php
namespace lib\anchor;

class AnchorGift
{
	private $_uid;

	private $_db;

	private $_redis;

	private $_uidGiftConfig = [];

	private $_tmpConfig = [];

	public function setUid( $uid )
	{
		$this->_uid = $uid;
	}

	/**
	 * @return PDO|\system\MysqlConnection
	 */
	public function getDb()
	{
		if ( !$this->_db )
		{
			$this->_db = \system\DbHelper::getInstance( 'huanpeng' );
		}

		return $this->_db;
	}

	public function getRedis()
	{
		if ( !$this->_redis )
		{
			$this->_redis = new \system\RedisHelper();
		}

		return $this->_redis;
	}

	/**
	 * @return array|bool
	 */
	public function getGiftConfig()
	{
		$rule = $this->_getUserGiftTypeAndSelfGiftConf()
			->_getGiftTemplateConfigByType()
			->_getGiftConfigDetailByTemplateConfig();

		return $rule;
	}

	private function _getUserGiftTypeAndSelfGiftConf()
	{
		//TODO 获取用户配置应该为动态配置，可以由后台进行配置
		$this->_uidGiftConfig = [
			'gift_type' => 1,
			'config_id' => 1
		];

		return $this;
	}

	private function _getGiftTemplateConfigByType()
	{

		if ( $this->_uidGiftConfig['gift_type'] )
		{
			$data = [ 'type' => $this->_uidGiftConfig['gift_type'] ];

			$sql    = "select * from gift_template where `type`=:type";
			$result = $this->getDb()->query( $sql, $data );

			if (isset($result[0]) && is_array( $result[0] ) )
			{
				$this->_tmpConfig = $result[0];
			}
		}

		return $this;

	}

	/**
	 *
	 */
	private function _getGiftConfigDetailByTemplateConfig()
	{
		$rule = '';

		if(!isset($this->_tmpConfig))
		{
			return false;
		}

		if ( $this->_tmpConfig['config_id'] == $this->_uidGiftConfig['config_id'] )
		{
			$rule = 'parent';
		}
		else
		{
			if ( $this->_tmpConfig['conver_rule'] == 1 )
			{
				$rule = 'both';
			}
			elseif ( $this->_tmpConfig['conver_rule'] == 2 )
			{
				$rule = 'parent';
			}
			else
			{
				return false;
			}
		}

		$parentDetail = [];
		$selfDetail = [];

		$parentDetail = $this->_getGiftDetail($this->_tmpConfig['config_id']);

		if($rule == 'both')
		{
			$selfDetail = $this->_getGiftDetail($this->_uidGiftConfig['config_id']);
		}

		return array_merge($parentDetail,$selfDetail);

	}

	private  function _getGiftDetail($configid)
	{

		$data = ['config_id'=>$configid];
		$sql = "select * from gift_config_detail WHERE config_id=:config_id ORDER BY `order`";

		$result = $this->getDb()->query($sql, $data);

		$detail = [];

		foreach ($result as $value)
		{
			//todo 礼物ID以及数量作为主键 如果以后，不再存在同一礼物不同数量的情况，则可以改进
//			$key = $value['gift_id']."-".$value['num'];
			$key = $value['order'];
			$detail[$key] = $value;
		}

		return $detail;
	}
}