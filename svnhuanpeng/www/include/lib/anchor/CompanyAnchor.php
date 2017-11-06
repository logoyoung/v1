<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/7/17
 * Time: 9:22
 */

namespace lib\anchor;

//include __DIR__ . "/../../init.php";

use system\DbHelper;
use Exception;
use system\MysqlConnection;

class CompanyAnchor
{
	const ANCHOR_TYPE_COMPANY = 1;
	const ANCHOR_TYPE_UNION   = 2;

	const ANCHOR_HP_CID    = 15;
	const ANCHOR_EMPTY_CID = 0;

	const TABLE_COMPANY = 'company';
	const TABLE_ANCHOR  = 'anchor';

	private $_db;

	public function getTable_anchor()
	{
		return self::TABLE_ANCHOR;
	}

	public function getTable_company()
	{
		return self::TABLE_COMPANY;
	}


	public function getDB( $type = 0 ): MysqlConnection
	{
		if ( !$this->_db )
		{
			$this->_db = DbHelper::getInstance( 'huanpeng' );
		}

		return $this->_db;
	}

	public function getCompanyIdList()
	{
		$table = $this->getTable_company();

		$param = [
			'cid_hp' => self::ANCHOR_HP_CID,
			'type'   => self::ANCHOR_TYPE_COMPANY
		];

		$sql = "select id from $table WHERE id !=:cid_hp and `type`=:type";

		try
		{
			$list = $this->getDB()->query( $sql, $param );
//			 $list = [];
			if ( is_array( $list ) )
			{
				return array_column( $list, 'id' );
			}
			else
			{
				return false;
			}
		} catch ( Exception $exception )
		{
			return false;
		}
	}

	public function getCompanyUserIdList()
	{
		$cidList = $this->getCompanyIdList();

		if ( !is_array( $cidList ) )
		{
			return false;
		}

		$table = $this->getTable_anchor();

		$param   = $cidList;
		$cidList = $this->getDB()->buildInPrepare( $cidList );

		$sql = "select uid,cid from $table WHERE  cid IN ($cidList) ";

		try
		{
			$result = $this->getDB()->query( $sql, $param );

			if ( !is_array( $result ) )
			{
				return false;
			}

			$userResult = [];

			foreach ( $result as $key => $val )
			{
				$cid = $val['cid'];
				$uid = $val['uid'];
				if ( !isset( $userResult[$cid] ) )
				{
					$userResult[$cid] = [];
				}

				array_push($userResult[$cid], $uid);
			}

			return $userResult;

		} catch ( Exception $exception )
		{
			return false;
		}
	}

}

