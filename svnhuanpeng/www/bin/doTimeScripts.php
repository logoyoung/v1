<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/27
 * Time: 15:35
 */
$crontab = [
	'DEV' => [
		 '
		 */3 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/liveTimeOut.php DEV >> /data/logs/liveTimeOut.php.log 2>&1 &
		 */5 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/trancodeFilesBat.php DEV >> /data/logs/LiveCacheBat.php.log 2>&1 &
		 */1 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/wsClearDeadLine20170501.php DEV >> /data/logs/wsClearDeadLine20170501.php.log 2>&1 &
		 */10 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/sync-anchor-inco.php DEV
		 15 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/sync-anchor-inco-month.php DEV
		 */1 * * * * /usr/bin/php /usr/local/huanpeng/bin/updateRecommendAnchorList.php DEV
		 */5 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/liveViewer.php DEV
		 5 18 * * *  /usr/bin/php  /usr/local/huanpeng/bin/liveLength.php DEV
		 */20 * * * * /usr/bin/php  /usr/local/huanpeng/bin/liveLength_today.php DEV
		 '

	],
	'PRE' => [
		'
		 */3 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/liveTimeOut.php PRE >> /data/logs/liveTimeOut.php.log 2>&1 &
		 */5 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/trancodeFilesBat.php PRE >> /data/logs/LiveCacheBat.php.log 2>&1 &
		 */1 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/wsClearDeadLine20170501.php PRE >> /data/logs/wsClearDeadLine20170501.php.log 2>&1 &
		 */10 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/sync-anchor-inco.php PRE
		 15 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/sync-anchor-inco-month.php PRE
		 */1 * * * * /usr/bin/php /usr/local/huanpeng/bin/updateRecommendAnchorList.php PRE
		 */5 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/liveViewer.php PRE
		 5 18 * * *  /usr/bin/php  /usr/local/huanpeng/bin/liveLength.php PRE
		 */20 * * * * /usr/bin/php  /usr/local/huanpeng/bin/liveLength_today.php PRE
		 
		 * * * * *  php /usr/local/huanpeng/bin/due/orderCronTable.php
		 0 3 * * *  php /usr/local/huanpeng/bin/due/resetUserTags.php
		 */2 * * * * php /usr/local/huanpeng/bin/due/rongCloudSendMsg.php
		 */5 * * * * php /usr/local/huanpeng/bin/game/cacheGameLiveList.php >> /tmp/null 2&>1
		 */5 * * * * php /usr/local/huanpeng/bin/index/cacheIndexData.php >> /tmp/null 2&>1
		 * * * * * php /usr/local/huanpeng/bin/finance/deamon/cronGuaranteePay.php >> /data/logs/cronGuaranteePay.log 2&>1
		 '

	],
	'PRO' => [
		'
		 */3 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/liveTimeOut.php PRO >> /data/logs/liveTimeOut.php.log 2>&1 &
		 */5 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/trancodeFilesBat.php PRO >> /data/logs/LiveCacheBat.php.log 2>&1 &
		 */1 * * * * /usr/bin/php /usr/local/huanpeng/bin/live/wsClearDeadLine20170501.php PRO >> /data/logs/wsClearDeadLine20170501.php.log 2>&1 &
		 */10 * * * * /usr/local/bin/php /usr/local/huanpeng/bin/statistics/sync-anchor-inco.php PRO
		 15 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/sync-anchor-inco-month.php PRO
		 */1 * * * * /usr/bin/php /usr/local/huanpeng/bin/updateRecommendAnchorList.php PRO
		 */5 * * * * /usr/bin/php /usr/local/huanpeng/bin/statistics/liveViewer.php PRO
		 5 18 * * * /usr/bin/php  /usr/local/huanpeng/bin/liveLength.php PRO
		 */20 * * * * /usr/bin/php  /usr/local/huanpeng/bin/liveLength_today.php PRO
		 '

	]
];