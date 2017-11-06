<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/3
 * Time: 下午5:17
 */


include "../../include/init.php";
include "../../include/User.class.php";

exit;
$addReword = array(
	array(
		'type' => 'task',
		'desc' => '精彩视频数量榜奖励',
		'rewordType' => 'coin',
		'list' => array(
			'2625' => 500,
			'2290' => 200,
			'3055' => 200,
			'3490' => 100,
			'3430' => 100,
		)
	),
	array(
		'type' => 'task',
		'desc' => '直播有效时常榜奖励',
		'rewordType' => 'coin',
		'list' => array(
			'2290' => 500,
			'2625' => 200,
			'3490' => 200,
			'3055' => 100,
			'5285' => 100
		)
	),
	array(
		'type' => 'task',
		'desc' => '精彩视频阳光普照奖',
		'rewordType' => 'bean',
		'list' => array(
			'2290' => 6000,
			'2625' => 6000,
			'3490' => 6000,
			'3055' => 6000,
			'5285' => 6000,
			'3700' => 6000,
			'4245' => 4000,
			'4260' => 4000,
			'4380' => 4000,
			'3415' => 4000,
			'2780' => 4000,
			'4465' => 4000,
			'4410' => 4000,
			'3635' => 4000,
			'8485' => 4000,
			'7930' => 4000,
			'3250' => 4000,
			'3630' => 4000,
			'3635' => 4000,
			'3710' => 2000,
			'2225' => 2000,
			'3430' => 2000,
			'3100' => 2000,
			'7945' => 2000,
			'4565' => 2000,
			'4240' => 2000,
			'4655' => 2000,
			'2105' => 2000,
			'1860' => 2000,
			'4530' => 2000,
			'4295' => 2000,
			'3070' => 2000,
			'3505' => 2000,
			'3685' => 2000,
			'4310' => 2000,
			'4140' => 2000,
			'4160' => 2000
		)
	)
);

$db  = new DBHelperi_huanpeng();

foreach ($addReword as $key => $val )
{
	file_put_contents(LOG_DIR.'huanpeng_reword_record.log', json_encode($val)."\n", FILE_APPEND );
	if( $val['rewordType'] == 'coin' )
	{
		foreach ($val['list'] as $uid => $coin )
		{
			echo "uid => $uid  up coin $coin \n";
			$userHelp = new UserHelp( $uid, $db );
			$userHelp->upHpcoin( $coin );
			unset($userHelp);
		}
	} elseif( $val['rewordType'] == 'bean' )
	{
		foreach ($val['list'] as $uid => $bean )
		{
			echo "uid => $uid up bean $bean \n";
			$userHelp = new UserHelp( $uid, $db );
			$userHelp->upHpbean( $bean );
			unset($userHelp);
		}
	}
}