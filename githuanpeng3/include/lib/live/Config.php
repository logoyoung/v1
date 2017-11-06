<?php
/**
 * live config
 * v 1.0
 * date 2017-09-04
 */
namespace lib\live;

class Config{

	// auth error code
	static $error_code = [
		'auth_user'  => '-1013',
		'auth_anchor' => '-4057',
		'auth_params' => '-4013',
		'filter_sensitive' => -4109,
		'live_pub'	  => '-7001',
		'live_device' => '-7002',
		'live_data' => '-7003',
		'live_fail' => '-7004',
		'stream_fail' => '-7005',
		'master_stop' => '-7007',
	];
	//quality
	static $app_quality = [
		[
			'quality' => '2',
			'width'   => '1280',
			'height'  => '720',
			'rate'    => '1500',
			'rec'	  => 'wifi',
			'desc'	  => '高清（720p，1.5M）',
		],
		[
			'quality'=>'1',
			'width'=>'960',
			'height'=>'540',
			'rate'=>'800',
			'rec'=>'4g',
			'desc'=>'普清（540p，800k）',
		],
	];

	static $pc_quality = [
		[
			'quality'=>'2',
			'width'=>'1280',
			'height'=>'720',
			'rate'=>'1500',
			'rec'=>'wifi',
			'desc'=>'高清（720p，1500k）',
		],
		[
			'quality'=>'1',
			'width'=>'960',
			'height'=>'540',
			'rate'=>'800',
			'rec'=>'4g',
			'desc'=>'普清（540p，800k）',
		],
	];

	static $live_orientation = [
		'vertical'   => '0',
		'horizontal' => '1',
		'pc'		 => '4',
	];

	static $doubel_quality = [
		[
			'quality'=>'0',
			'width'=>'640',
			'height'=>'360',
			'rate'=>'600',
			'rec'=>'wifi',
			'desc'=>'流畅（360p，600k）',
		],
		[
			'quality'=>'0',
			'width'=>'640',
			'height'=>'360',
			'rate'=>'600',
			'rec'=>'4g',
			'desc'=>'流畅（360p，600k）',
		],
	];

	static $live_type = [
		'screenshot' => '0',
		'camera'     => '1',
		'pc'         =>  '2',
		'doubelmaster' => '3',
		'doubelslave' => '4',
	];

	static $live_pub_type = [
		'continue'  => '0',
		'new'		=> '1',
		'otherdevice' => '2',
	];
	static $poster_type = [
		'big' => [
			'0' => 'big-shu',
			'1' => 'big-heng',
			'4' => 'big-heng',
		],
		'small' => [
			'0' => 'small-shu',
			'1' => 'small-heng',
			'4' => 'small-heng',
		],
	];
	static $play_type = [
		'disconnect' => '0',
		'play' => '1',
		'stop' => '2',
	];

	static $video_ext = [
		'src' => 'flv',
		'dest' => 'mp4',
		'pic' => 'jpg',
	];
	static $v_dir = [
		'DEV' => ['v' => 'dev/v/','i' => 'dev/i/'],
		'PRE' => ['v' => 'pre/v/','i' => 'pre/i/'],
		'PRO' => ['v' => 'pro/v/','i' => 'pro/i/'],
	];
	static $v_opt = [
		'merge'=>'1',
		'transcode'=>'2',
		'poster'=>'3',
		'move'=>0,
		'complete'=>'1',
		'clear'=>'2',
	];
	static $p_s = [
		'w' => 1280,
		'h' => 720,
	];
	static $v_bucket = '6huanpeng-test001';
	static $p_bucket = '';

	static $live_heart = 'liveheart';
	static $heart_expire = 3600;
	static $heart_start = false;
	// live status
	const LIVE_CREATE = 0;
	const LIVE = 100;
	const LIVE_STOP = 110;

}