<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/27
 * Time: 下午6:04
 */


include __DIR__ . '/../../include/init.php';

$data = [
	[
		'item_id'       => '​1241134518',
		'product_id'    => 'hpcoin_42_iphone',
		'hpcoin_amount' => 42,
		'cash_amount'   => 4.2,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	],
	[
		'item_id'       => '1241139051',
		'product_id'    => 'hpcoin_84_iphone',
		'hpcoin_amount' => 84,
		'cash_amount'   => 8.4,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	],
	[
		'item_id'       => '1241142531',
		'product_id'    => 'hpcoin_210_iphone',
		'hpcoin_amount' => 210,
		'cash_amount'   => 21,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	],
	[
		'item_id'       => '1241148513',
		'product_id'    => 'hpcoin_350_iphone',
		'hpcoin_amount' => 350,
		'cash_amount'   => 35,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	],
	[
		'item_id'       => '1241148517',
		'product_id'    => 'hpcoin_896_iphone',
		'hpcoin_amount' => 896,
		'cash_amount'   => 89.6,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	],
	[
		'item_id'       => '1241149899',
		'product_id'    => 'hpcoin_2296_iphone',
		'hpcoin_amount' => 2296,
		'cash_amount'   => 229.6,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	],
	[
		'item_id'       => '1241150397',
		'product_id'    => 'hpcoin_4326_iphone',
		'hpcoin_amount' => 4326,
		'cash_amount'   => 432.6,
		'bid'           => 'com.huanpeng.show',
		'app_item_id'   => '1240076397',
		'channel_id'    => 9001
	]
];

$sql = "CREATE TABLE IF NOT EXISTS iap_product_info (
  `item_id`       BIGINT(20) UNSIGNED     NOT NULL DEFAULT 0,
  `app_item_id`   BIGINT(20) UNSIGNED     NOT NULL DEFAULT 0,
  `product_id`    VARCHAR(100)            NOT NULL DEFAULT '',
  `hpcoin_amount` INT(10) UNSIGNED        NOT NULL DEFAULT 0,
  `cash_amount`   NUMERIC(13, 3) UNSIGNED NOT NULL DEFAULT 0.000,
  `bid`           VARCHAR(100)            NOT NULL DEFAULT '',
  `channel_id`    INT(10) UNSIGNED        NOT NULL DEFAULT 0,
  PRIMARY KEY (`item_id`)
);";


$db = new DBHelperi_huanpeng();

$db->query( $sql );


foreach ( $data as $value )
{
	$sql = $db->insert( 'iap_product_info', $value, true );
	var_dump( $sql . "\n" );

	var_dump( $db->query( $sql ) . "\n" );
}

$sql = "create table IF NOT EXISTS iap_handle_table_record(
  `name` VARCHAR(20) not NULL DEFAULT '',
  `status` INT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`name`),
  KEY (`status`)
);";

var_dump( $db->query( $sql ) );
