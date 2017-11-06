<?php
namespace lib\app;
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/14
 * Time: 16:46
 */
class AppUpdate
{
	private $version;
	private $channel;

	private $db;

	const HUANPENG_CHANNEL = 8001;
	/**
	 * @param mixed $channel
	 */
	public function setChannel( $channel )
	{
		$this->channel = $channel;
	}

	public function getChannel()
	{
		return $this->channel;
	}

	/**
	 * @param mixed $version
	 */
	public function setVersion( $version )
	{
		$this->version = $version;
	}

	/**
	 * @return mixed
	 */
	public function getVersion()
	{
		return $this->version;
	}

	public function getDb()
	{
		if ( !$this->db )
		{
			$this->db = \system\DbHelper::getInstance( 'huanpeng' );
		}

		return $this->db;
	}

	public function getVersionInfo()
	{
		//如果没有传入channel  则默认为官方渠道
		$data = [
			'channel' => $this->channel ? $this->channel : self::HUANPENG_CHANNEL,
			'version' => $this->version ? $this->version : 0
		];

		$sql  = "select * from admin_app WHERE channel_id=:channel AND version>=:version ORDER BY id DESC LIMIT 1";

		try
		{
			$res = $this->getDb()->query( $sql, $data );

			if ( isset($res[0] ) )
			{
				return $res[0];
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
}