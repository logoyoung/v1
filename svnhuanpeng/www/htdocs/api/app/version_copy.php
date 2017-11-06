<?php
include __DIR__."/../../../include/init.php";
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/14
 * Time: 12:15
 */
class AppUpdate
{
	private $version;

	private $versionInfo;

	private $forcedVersionList = [];

	private $channel;

	private $db;

	const DOWNLOAD_URL = WEB_ROOT_URL . "api/app/download.php?";

	const HUANPENG_CHANNEL = 8001;

	/**
	 * @param array $forcedVersionList
	 */
	public function setForcedVersionList( array $forcedVersionList )
	{
		$this->forcedVersionList = $forcedVersionList;
	}

	/**
	 * @param mixed $channel
	 */
	public function setChannel( $channel )
	{
		$this->channel = $channel;
	}

	/**
	 * @return \system\DbHelper
	 */
	public function getDb():\system\MysqlConnection
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
			'version' => $this->version
		];

		$sql  = "select * from admin_app WHERE channel_id=:channel AND version>=:version ORDER BY id DESC LIMIT 1";


		try
		{
			$res = $this->getDb()->query( $sql, $data );

			if ( $res[0] )
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

	private function isMustUp()
	{
		if ( $this->versionInfo['version'] <= $this->version )
		{
			return 0;
		}

		$list = [];

		foreach ( $this->forcedVersionList as $version => $must )
		{
			if ( $version > $this->version )
			{
				array_push( $list, $must );
			}
		}

		$sum = array_sum( $list );

		if ( $sum > 0 )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	private function _init()
	{
		$this->channel = $_POST['channel'] ? intval( $_POST['channel'] ) : 0;

		$this->version = $_POST['version'] ? intval( $_POST['version'] ) : 0;

		$this->versionInfo = $this->getVersionInfo();

		if(!$this->versionInfo['version'])
		{

			$this->setChannel(self::HUANPENG_CHANNEL);
			//重新获取默认渠道版本信息
			$this->versionInfo = $this->getVersionInfo();
		}

	}

	public function download()
	{

	}

	public function display()
	{
		$this->_init();

		$conf = $GLOBALS['env-def'][$GLOBALS['env']];

		$data = [
			'url'          => $this->versionInfo['app_url'] ? 'http://' . $conf['domain-img'] . $this->versionInfo['app_url'] : '',
			'version'      => intval($this->versionInfo['version']),
			'version_name' => $this->versionInfo['name'],
			'version_desc' => $this->versionInfo['note'],
			'isMustUp'     => $this->isMustUp()
		];

		succ( $data );
	}
}


$forcedVersionList = [
	'105' => 1,//'版本'=>array('是否强制更新'=>0)  0否 1是
	'107' => 1,
	'108' => 1
];


$app = new AppUpdate();

$app->setForcedVersionList($forcedVersionList);

$app->display();