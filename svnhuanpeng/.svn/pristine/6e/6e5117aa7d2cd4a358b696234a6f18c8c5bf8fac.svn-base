<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/7
 * Time: 下午12:01
 */

include __DIR__."/../../include/init.php";


$db = new DBHelperi_huanpeng(true);



$info = file(__DIR__.'/info.php');


$oldIntegral = json_decode( $info[1], true );
$newIntegral = json_decode( $info[2], true );

$oldGiftInfo = json_decode( $info[3], true );
$newGiftInfo = json_decode( $info[4], true );

$anchorLevelList = array();
$userLevelList = array();

//cal send bean user level;
$calBeanCreateExpNew = function ( $num ) use ( $newGiftInfo )
{
	$money = $newGiftInfo['31']['money'];
	$exp = $newGiftInfo['31']['exp'];
	return $num / $money * $exp;
};

$calBeanCreateExpOld = function ( $num ) use ( $oldGiftInfo )
{
	$money = $oldGiftInfo['31']['money'];
	$exp = $oldGiftInfo['31']['exp'];
	return $num / $money * $exp;
};

$calCoinCreateExpNew = function ( $gid ) use ( $newGiftInfo )
{
	$exp = $newGiftInfo[$gid]['exp'];
	return $exp;
};

$calCoinCreateExpOld = function ( $gid ) use( $oldGiftInfo )
{
	$exp = $oldGiftInfo[$gid]['exp'];
	return $exp;
};


$calOldLevel = function( $exp ) use ( $oldIntegral )
{
	$level = 1;
	foreach ($oldIntegral as $key => $val )
	{
		if( $exp > $val)
		{
			$level = $key + 1;
		}else
		{
			break;
		}
	}

	return $level;
};

$calNewLevel = function ( $exp ) use ( $newIntegral )
{
	$level = 1;
	foreach ($newIntegral as $key => $val )
	{
		if( $exp > $val)
		{
			$level = $key + 1;
		}else
		{
			break;
		}
	}

	return $level;
};

$sql = "select * from giftrecord";
$res = $db->query( $sql );
while( $row = $res->fetch_assoc() )
{
	$luid = $row['luid'];
	$uid = $row['uid'];
	$num = $row['giftnum'];

	if( !isset( $anchorLevelList[$luid] ) )
	{
		$anchorLevelList[$luid]['new'] = 0;
		$anchorLevelList[$luid]['old'] = 0;
	}

	if( !isset( $userLevelList[$uid ] ) )
	{
		$userLevelList[$uid]['new'] = 0;
		$userLevelList[$uid]['old'] = 0;
	}

	$createExpOld = $calBeanCreateExpOld( $num );
	$createExpNew = $calBeanCreateExpNew( $num );

	$userLevelList[$uid]['new'] = (float)$userLevelList[$uid]['new'] + $createExpNew;
	$userLevelList[$uid]['old'] = (float)$userLevelList[$uid]['old'] + $createExpOld;

	$anchorLevelList[$luid]['new'] = (float)$anchorLevelList[$luid]['new'] + $createExpNew;
	$anchorLevelList[$luid]['old'] = (float)$anchorLevelList[$luid]['old'] + $createExpOld;
}

$getUserLevel = function ( $uid ) use ( $db )
{
	$sql = "select `level`,`integral` from useractive where uid=$uid";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	return $row;
};


$getAnchorLevel = function( $luid ) use ( $db )
{
	$sql = "select `level`,`integral` from anchor where uid=$luid";
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();
	return $row;
};


$updateUserLevel = function ( $uid,$level,$integral, $nLevel, $nInterval ) use( &$db, $oldIntegral, $newIntegral )
{
	if(!$uid)
		return false;

	if( $nLevel < $level )
	{
		//保留当前等级以及相对应的integral
		$uplevel = $level;
		$upinterval = $integral - $oldIntegral[$level] + $newIntegral[$level];
	}elseif( $nLevel > $level ){
		//按照最新等级录入
		$uplevel = $nLevel;
		$upinterval = $nInterval;
	}else{
		//如果等级相等，计算对应的integral
		$uplevel = $nLevel;
		$upinterval = $nInterval;
	}

	$sql = "update useractive set integral=$upinterval,level=$uplevel where uid=$uid";
	if($db->query($sql))
	{
//		echo "uid interval upinterval ninterval level uplevel nlevel \n";
//		echo sprintf("%9d%9s%9s%9s%9d%9d%9d\n",$uid, $integral, $upinterval, $nInterval, $level, $uplevel, $nLevel );
		return true;
	}
	return false;

};


$updateAnchorLevel = function ( $uid,$level,$integral, $nLevel, $nInterval ) use( &$db, $oldIntegral, $newIntegral )
{
	if(!$uid)
		return false;

	if( $nLevel < $level )
	{
		//保留当前等级以及相对应的integral
		$uplevel = $level;
		$upinterval = $integral - $oldIntegral[$level] + $newIntegral[$level];
	}elseif( $nLevel > $level ){
		//按照最新等级录入
		$uplevel = $nLevel;
		$upinterval = $nInterval;
	}else{
		//如果等级相等，计算对应的integral
		$uplevel = $nLevel;
		$upinterval = $nInterval;
	}

	$sql = "update anchor set integral=$upinterval,level=$uplevel where uid=$uid";
	if( $db->query($sql))
	{
//		echo "uid interval upinterval ninterval level uplevel nlevel \n";
//		echo sprintf("%9d%9s%9s%9s%9d%9d%9d\n",$uid, $integral, $upinterval, $nInterval, $level, $uplevel, $nLevel );
		return true;
	}
	return false;
};

$insertUserLevel = function () use ( &$db, $newIntegral )
{
	foreach ( $newIntegral as $level => $integral)
	{
		$sql = "insert into userlevel (`level`, `integral`) VALUE ($level, $integral) on duplicate key update integral=$integral";
		if( !$db->query( $sql ) )
		{
			return false;
		}
	}
	return true;
};


$insertAnchorLevel = function () use ( &$db, $newIntegral )
{
	foreach ( $newIntegral as $level => $integral)
	{
		$sql = "insert into anchorlevel (`level`, `integral`) VALUE ($level, $integral) on duplicate key update integral=$integral";
		if( !$db->query( $sql ) )
		{
			return false;
		}
	}

	return true;
};


$updateGiftInfo = function () use ( &$db, $newGiftInfo )
{
	foreach ($newGiftInfo as $key => $value)
	{
		$id = $value['id'];
		$money = $value['money'];
		$exp = $value['exp'];
		$sql = "update gift set money=$money,exp=$exp where id=$id";
		if( !$db->query( $sql) )
		{
			return false;
		}
	}

	return true;
};

unset( $row );

$sql = "select * from giftrecordcoin";
$res = $db->query( $sql );
while( $row = $res->fetch_assoc() ){
	$luid = $row['luid'];
	$uid = $row['uid'];
	$gid = $row['giftid'];

	if( !isset( $anchorLevelList[$luid] ) )
	{
		$anchorLevelList[$luid]['new'] = 0;
		$anchorLevelList[$luid]['old'] = 0;
	}

	if( !isset( $userLevelList[$uid ] ) )
	{
		$userLevelList[$uid]['new'] = 0;
		$userLevelList[$uid]['old'] = 0;
	}

	$createExpNew = floatval( $calCoinCreateExpNew( $gid ) );
	$createExpOld = floatval( $calCoinCreateExpOld( $gid ) );

	$userLevelList[$uid]['new'] = (float)$userLevelList[$uid]['new'] + $createExpNew;
	$userLevelList[$uid]['old'] = (float)$userLevelList[$uid]['old'] + $createExpOld;

	$anchorLevelList[$luid]['new'] = (float)$anchorLevelList[$luid]['new'] + $createExpNew;
	$anchorLevelList[$luid]['old'] = (float)$anchorLevelList[$luid]['old'] + $createExpOld;
}




foreach ( $anchorLevelList as $key => $val ){
	$oldLevel = $calOldLevel( $val['old'] );
	$newLevel = $calNewLevel( $val['new'] );
	$levelInfo = $getAnchorLevel( $key );

	$anchorLevelList[$key]['cur'] = $levelInfo['integral'];
	$anchorLevelList[$key]['oldlevel'] = $oldLevel;
	$anchorLevelList[$key]['newlevel'] = $newLevel;
	$anchorLevelList[$key]['curlevel'] = $levelInfo['level'];
}

unset($oldLevel);
unset($newLevel);

foreach( $userLevelList as $key => $val ){
	$oldLevel = $calOldLevel( $val['old'] );
	$newLevel = $calNewLevel( $val['new'] );
	$levelInfo = $getUserLevel( $key );

	$userLevelList[$key]['cur'] = $levelInfo['integral'];
	$userLevelList[$key]['oldlevel'] = $oldLevel;
	$userLevelList[$key]['newlevel'] = $newLevel;
	$userLevelList[$key]['curlevel'] = $levelInfo['level'];
}


$db->autocommit(false);
$db->query('begin');

foreach ( $userLevelList as $key => $val)
{
//      echo sprintf("%9d%9s%9s%9s%9d%9d%9d\n",$key, $val['old'], $val['new'], $val['cur'],$val['oldlevel'], $val['newlevel'], $val['curlevel'] );
	if( !$updateUserLevel( $key, $val['curlevel'], $val['cur'], $val['newlevel'], $val['new'] ) )
	{
		$db->rollback();
		exit("user :$key update failed.\n");
	}
}


foreach ( $anchorLevelList as $key => $val )
{

	if( !$updateAnchorLevel( $key, $val['curlevel'], $val['cur'], $val['newlevel'], $val['new'] ) )
	{
		$db->rollback();
		exit("anchor :$key update failed.\n");
	}
}

if( !$insertUserLevel())
{
	$db->rollback();
	exit("insert user level failed.\n");
}

if( !$insertAnchorLevel() )
{
	$db->rollback();
	exit("insert anchor level failed.\n");
}

if(!$updateGiftInfo())
{
	$db->rollback();
	exit("update gift info failed.\n");
}


//sleep(5);

$db->commit();
//$db->rollback();
//echo "no error  and roll back\n";


exit("run success ");



//echo "         uid         old          new \n";
//echo sprintf("%9d%9s%9s%9s%9d%9d%9d\n",$key, $val['old'], $val['new'], $userLevelInfo['integral'],$oldLevel, $newLevel, $userLevelInfo['level'] );
//
//echo "    luid    old     new     cur   oldlevel  newlevel  curlevel\n";
//echo sprintf("%9d%9s%9s%9s%9d%9d%9d\n",$key, $val['old'], $val['new'], $anchorLevelInfo['integral'], $oldLevel, $newLevel, $anchorLevelInfo['level'] );
