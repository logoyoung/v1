<?php
namespace Cli\Controller;
use HP\Log\Log;
class UserNickController extends \Think\Controller
{
	/**
	 * 同步用户昵称到昵称审核表
	 * 按用户id同步
	 */

	public function  user($uid){
		$udao=D('userstatic');
		$info=$udao->where("uid=$uid")->select();
		if($info){
			$adao=D('admin_user_nick');
			$res=$adao->where("uid=$uid")->select();
			if(!$res){
				$data=array(
					'uid'=>$uid,
					'nick'=>$info[0]['nick'],
					'oldnick'=>$info[0]['nick'],
					'utime'=>date('Y-m-d H:i:s'),
					'status'=>3
				);
				$isok=$adao->add($data);
				if($isok){
					echo '1'."\n";
				}else{
					echo '0'."\n";
				}
			}
		}
	}

	/**
	 * @param string $t  时间  如 2017-05-08
	 * 按时间同步
	 */
	public function days($t=''){
		if($t){
			$stime=$t.' 00:00:00';
			$etime=$t.' 23:59:59';
		}else{
			$stime=date('Y-m-d').' 00:00:00';
			$etime=date('Y-m-d').' 23:59:59';
		}
		$udao=D('userstatic');
		$info=$udao->where("rtime>='$stime'  and rtime<='$etime'")->select();
		if($info){
			$adao=D('admin_user_nick');
			foreach ($info as $v){
				$res=$adao->where("uid=".$v['uid'])->select();
				if(!$res){
					$data=array(
						'uid'=>$v['uid'],
						'nick'=>$v['nick'],
						'oldnick'=>$v['nick'],
						'utime'=>date('Y-m-d H:i:s'),
						'status'=>3
					);
					$isok=$adao->add($data);
					if($isok){
						echo '1'."\n";
					}else{
						echo '0'."\n";
					}
				}
			}
		}
	}

	public function run()
	{
		$dao = D( 'userstatic' );
		$res = $dao->field( 'uid,nick' )->select();
		if( $res )
		{
			$admin = D( 'admin_user_nick' );
			foreach ( $res as $v )
			{
				$check = $admin->where( "uid=" . $v['uid'] )->select();
				if( !$check )
				{
					$data = array(
						'uid' => $v['uid'],
						'nick' => $v['nick'],
						'oldnick' => $v['nick'],
						'ctime' => date( 'Y-m-d H:i:s' ),
						'status' => 3
					);
					$admin->add( $data );
					Log::statis($v['uid']."<===>".$v['nick'],'','run');
				}
			}
		}
	}

	public function lastday($t='')
	{
		if($t){
			$data = $t;
		}else{
			$data = date( 'Y-m-d', strtotime( "-1 day" ) );
		}
		$three = D( 'three_side_user' );
		$res = $three->where( "ctime like '%$data%'" )->select();
		if( $res )
		{
			Log::statis(json_encode($res),'','three_side_user');
			$uid = implode( ',', array_column( $res, 'uid' ) );
			$userDao = D( 'userstatic' );
			$userinfo = $userDao->field( "uid,nick" )->where( "uid in ($uid)" )->select();
			if( $userinfo )
			{
				$admin = D( 'admin_user_nick' );
				foreach ($userinfo as $v){
					$check=$admin->where("uid=".$v['uid'])->select();
					if(!$check){
						$data = array(
							'uid' => $v['uid'],
							'nick' => $v['nick'],
							'oldnick' => $v['nick'],
							'ctime' => date( 'Y-m-d H:i:s' ),
							'status' => 3
						);
						$admin->add( $data );
						Log::statis($v['uid'].'<==>'.$v['nick'],'','three_side_user_nick');
					}
				}

			}
		}
	}

}