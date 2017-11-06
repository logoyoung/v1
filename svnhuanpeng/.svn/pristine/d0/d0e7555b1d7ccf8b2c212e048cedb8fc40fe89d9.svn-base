<?php
/**
 * Created by PhpStorm. 经济公司下发数据修复
 * User: dong
 * Date: 17/3/12
 * Time: 下午9:45
 */
require '/usr/local/huanpeng/include/init.php';
//$GLOBALS['env']='PRO';
$db = new DBHelperi_huanpeng();

$array = array(
	array( 'uid' => 12515, 'coin' => 86, 'bean' => 15 ),
	array( 'uid' => 21735, 'coin' => 293, 'bean' => 7 ),
	array( 'uid' => 24420, 'coin' => 143, 'bean' => 23 ),
	array( 'uid' => 29935, 'coin' => 66, 'bean' => 2 ),
	array( 'uid' => 33055, 'coin' => 47, 'bean' => 3 ),
	array( 'uid' => 46400, 'coin' => 44, 'bean' => 6 ),
	array( 'uid' => 64028, 'coin' => 84, 'bean' => 4 ),
	array( 'uid' => 89643, 'coin' => 107, 'bean' => 25 ),
	array( 'uid' => 89687, 'coin' => 216, 'bean' => 0 ),
);


function getd( $uid, DBHelperi_huanpeng $db )
{
	$sql = "SELECT uid,sum(gbd)/1000 as gb, sum(gdd)/1000 as gd from hpf_statement_201706 where uid =$uid AND hpf_statement_201706.ctime>'2017-06-23 00:00:00' AND `type`=6";
	$res = $db->query( $sql );

	$row = $res->fetch_assoc();

	return $row;
}

function run( $data, $db )
{
	$fobj = new \lib\Finance();
	foreach ( $data as $v )
	{
//		$info = $db->field( 'uid,coin,bean' )->where( "uid=" . $v['uid'] )->select( 'anchor' );
//		$info = $db->field('uid,sum(gbd)/1000 as gb, sum(gdd)/1000 as gd')->

		$info = getd( $v['uid'], $db );
		var_dump($info);

		if ( $info['uid'] )
		{
//			if ( $a > $c && $b > $d )
//			{

			$coin = bcadd( -$info['gb'], -$v['coin'], 3 );//abs( $info[0]['coin'] ) * 2 - 2 * $v['coin'];
			$bean = bcadd( -$info['gd'], -$v['bean'], 3 );//abs( $info[0]['bean'] ) * 2 - 2 * $v['bean'];

			echo "dddd=>gb = $coin\n";
			echo "dddd=>gd = $bean\n";

			$res = $fobj->innerRecharge( $info['uid'], 0, $coin, 0, $bean, 100, '经济公司发放扣除', 1111 );
			if ( is_array( $res ) )
			{
				$Obj              = new lib\Anchor( $info['uid'] );
				$updateUserHpbean = $Obj->updateUserHpBean( $res['hd'] );
				$updateUserHpcoin = $Obj->updateUserHpCoin( $res['hb'] );
				$updateAnchorBean = $Obj->updateAnchorBean( $res['gd'] );
				$updateAnchorCoin = $Obj->updateAnchorCoin( $res['gb'] );
				if ( $updateUserHpbean || $updateUserHpcoin || $updateAnchorBean || $updateAnchorCoin )
				{
					echo $res['tid'];
				}
				else
				{
					//记日志 TODO
					write_log( json_encode( $info ), 'Backunsuccess' );
				}
				echo -1;//更新失败
			}
//			}
//			else
//			{
//				echo 0;//发放失败
//			}


//				$sql="update anchor set coin=coin-".$v['coin'].",bean=bean-".$v['bean']." where uid=".$v['uid'] ;
//				$res=$db->query($sql);
//				if(false ===$res){
//					write_log(json_encode($info),'Back');
//				}
		}
		else
		{
			// 写日志
			write_log( json_encode( $info ), 'Back' );
		}

	}
}


run( $array, $db );

