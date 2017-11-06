<?php
namespace service\app;

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/14
 * Time: 16:50
 */
use lib\app\AppUpdate;

class AppUpdateService
{
	private $versionInfo;
	private $forcedVersionList = [];

	private $appUpdateObj;

	const DOWNLOAD_URL = WEB_ROOT_URL . "api/app/download.php?";

	/**
	 * @param array $forcedVersionList
	 */
	public function setForcedVersionList( array $forcedVersionList )
	{
		$this->forcedVersionList = $forcedVersionList;
	}

	public function __construct($channel,$version)
	{
		$this->appUpdateObj = new AppUpdate();

		$this->appUpdateObj->setChannel($channel);
		$this->appUpdateObj->setVersion($version);

		$this->versionInfo = $this->appUpdateObj->getVersionInfo();

		if(!$this->versionInfo['version'])
		{
			$this->appUpdateObj->setChannel(AppUpdate::HUANPENG_CHANNEL);

			//重新获取默认渠道版本信息
			$this->versionInfo = $this->appUpdateObj->getVersionInfo();

//			if(!$this->versionInfo['version'])
//			{
//				$this->appUpdateObj->setVersion(0);
//
//				$this->versionInfo = $this->appUpdateObj->getVersionInfo();
//			}
		}
	}

	public function isMustUp()
	{
		if ( $this->versionInfo['version'] <= $this->appUpdateObj->getVersion() )
		{
			return 0;
		}

		$list = [];

		foreach ( $this->forcedVersionList as $version => $must )
		{
			if ( $version > $this->appUpdateObj->getVersion() )
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

	public function download()
	{
		$filePath = $this->versionInfo['app_url'];

		$conf = $GLOBALS['env-def'][$GLOBALS['env']];

		$filePath = $conf['img-dir'] . "/$filePath";

		header();
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
		header("Content-Length: ". filesize($filePath));
		readfile($filePath);
	}

	private function getDownloadUrl()
	{
		$data = [
			'version'=>$this->versionInfo['version'],
			'channel' => $this->appUpdateObj->getChannel()
		];

		$params = http_build_query($data);

		$url = self::DOWNLOAD_URL . $params;

		return $url;

	}

	public function display()
	{

		$data = [
			'url'          => $this->getDownloadUrl(),//$this->versionInfo['app_url'] ? 'http://' . $conf['domain-img'] . $this->versionInfo['app_url'] : '',
			'version'      => intval($this->versionInfo['version']),
			'version_name' => $this->versionInfo['name'],
			'version_desc' => $this->versionInfo['note'],
			'isMustUp'     => $this->isMustUp()
		];

		succ( $data );
	}
}