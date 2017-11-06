<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/27
 * Time: 15:13
 */


/**
 * 执行sql文件
 *
 * @param $sqlFilePath
 * @param $db
 */
include( __DIR__ . '/../include/init.php' );
define( 'SQL_DIR', WEBSITE_ROOT . 'docs/sql_table/' );

function doSql( $sqlFilePath, $db )
{
	$fp  = fopen( $sqlFilePath, 'rb' );
	$sql = '';
	while ( $str = fgets( $fp ) )
	{
		if( !preg_match( '/###/', $str ) )
		{
			$sql .= $str;
		}
	}

	$sql = str_replace( "\r", "\n", $sql );
	foreach ( explode( ";\n", trim( $sql ) ) as $query )
	{
		$query = trim( $query );
		$query = rtrim( $query, ';' );
//	print_r($query);
		$db->query( $query );
	}
}

$sqlFiles = [
	SQL_DIR . 'addColumns.sql',
	SQL_DIR . 'april.sql',
	SQL_DIR . 'createStreamLog.sql',
	SQL_DIR . 'finance.sql',
	SQL_DIR . "giftrecord.sql",
	SQL_DIR . "exchange_detail.sql",
	SQL_DIR."bank_card.sql",
	SQL_DIR."backend.sql"
];

$db = new DBHelperi_huanpeng();
foreach ( $sqlFiles as $k => $sqlFile )
{
	$r = doSql( $sqlFile, $db );
	echo "sql文件{$sqlFile}执行完成\n";
}