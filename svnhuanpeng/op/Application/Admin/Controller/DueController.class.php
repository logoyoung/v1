<?php

namespace Admin\Controller;

use Common\Model\SalaryModel;
use HP\Op\Statis;
use HP\Op\Anchor;
use HP\Op\Game;


class DueController extends BaseController
{

	protected $pageSize = 20;

	public function _access()
	{
		return [
			'index' => [ 'index' ],
			'recommend' => [ 'index' ],
			'delete' => [ 'index' ],
			'addgame' => [ 'index' ],
			'recommendgame' => [ 'index' ],
			'gameindex' => [ 'index' ],
			'deletegame' => [ 'index' ]

		];
	}

	/**
	 * 技能列表页面
	 */
	public function index()
	{
		I( "get.uid" ) ? $uid = I( "get.uid" ) : '';
		I( "get.gid" ) ? $gid = I( "get.gid" ) : '';
		I( "get.status" ) ? $status = I( "get.status" ) : '';
		$where = 1;
		if( $uid )
		{
			$where .= "  and uid =$uid";
		}
		if( $gid )
		{
			$where .= "  and game_id =$gid";
		}
		if( $status == 1 )
		{//未推荐
			$where .= " and status=0";
		}
		if( $status == 2 )
		{//已推荐
			$where .= " and status=1";
		}
		if( $status == 3 )
		{//设置为展示的主播
			$where .= " and switch=1";
		}
		if( $status == 4 )
		{//设置为未展示的主播
			$where .= " and switch=-1";
		}
		$Dao = D( 'due_skill' );
		$total = $Dao->where( $where )->count();
		$Page = new \HP\Util\Page( $total, $this->pageSize );
		$list = array();
		if( $total )
		{
			$list = $Dao->where( $where )->order( 'ctime desc ' )->limit( $Page->firstRow . ',' . $Page->listRows )->select();
			$userinfo=Anchor::anchorInfo(array_column($list,'uid'));
			$gameinfo=Game::gameInfo(array_column($list,'game_id'));
			foreach ($list as $k=>$v){
				$list[$k]['nick']=$userinfo[$v['uid']]['nick'];
				$list[$k]['game_name']=$gameinfo[$v['game_id']]['name'];
			}
		}
		$this->status = array( '1' => '未推荐', '2' => '已推荐', '3' => '设置为展示的主播', '4' => '设置为未展示的主播' );
		$this->data = $list;
		$this->page = $Page->show();
		$this->display();
	}



	/**
	 * 技能推荐
	 */
	public function recommend()
	{

		I( "post.id" ) ? $id = I( "post.id" ) : '';
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'due_skill' );
		$info = $Dao->where( "id=" . $id )->select();
		if( $info )
		{
			$res = $Dao->where( "id=" . $info[0]['id'] )->save( array( 'status' => 1 ) );
			if( $res !== false )
			{
				$message = [ 'status' => 1, 'info' => '操作成功' ];
				return $this->ajaxReturn( $message );
			}
			else
			{
				$message = [ 'status' => 0, 'info' => '操作失败' ];
				return $this->ajaxReturn( $message );
			}
		}
		else
		{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			return $this->ajaxReturn( $message );
		}
	}

	/**
	 * 技能删除
	 */
	public function delete()
	{

		I( "post.id" ) ? $id = I( "post.id" ) : '';
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'due_skill' );
		$info = $Dao->where( "id=" . $id )->select();
		if( $info )
		{
			$res = $Dao->where( "id=" . $info[0]['id'] )->save( array( 'status' => 0 ) );
			if( ( false !== $res ) )
			{
				$message = [ 'status' => 1, 'info' => '操作成功' ];
				return $this->ajaxReturn( $message );
			}
			else
			{
				$message = [ 'status' => 0, 'info' => '操作失败' ];
				return $this->ajaxReturn( $message );
			}
		}
		else
		{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			return $this->ajaxReturn( $message );
		}
	}

	/**
	 * 约玩游戏列表
	 */
	public function gameindex()
	{
		I( "get.gid" ) ? $gid = I( "get.gid" ) : '';
		I( "get.gname" ) ? $gname = I( "get.gname" ) : '';
		I( "get.status" ) ? $status = I( "get.status" ) : '';
		$where = 1;
		if( $gid )
		{
			$where .= "  and game_id =$gid";
		}
		if( $gname )
		{

		}
		if( $status == 1 )
		{//未推荐
			$where .= " and status=0";
		}
		if( $status == 2 )
		{//已推荐
			$where .= " and status=1";
		}
		if( $status == 3 )
		{//删除
			$where .= " and status=2";
		}
		$Dao = D( 'due_game' );
		$total = $Dao->where( $where )->count();
		$Page = new \HP\Util\Page( $total, $this->pageSize );
		if( $total )
		{
			$list = $Dao->where( $where )->order( 'ord desc ' )->limit( $Page->firstRow . ',' . $Page->listRows )->select();
		}
		else
		{
			$list = array();
		}
		$this->status = array( '1' => '未推荐', '2' => '已推荐', '3' => '已删除' );
		$this->data = $list;
		$this->page = $Page->show();
		$this->conf = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'];
		$this->display();
	}

	/**
	 * 推荐游戏
	 */
	public function recommendgame()
	{

		I( "post.id" ) ? $id = I( "post.id" ) : '';
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'due_game' );
		$info = $Dao->where( "id=" . $id )->select();
		if( $info )
		{
			$res = $Dao->where( "id=" . $info[0]['id'] )->save( array( 'status' => 1 ) );
			if( $res !== false )
			{
				$message = [ 'status' => 1, 'info' => '操作成功' ];
				return $this->ajaxReturn( $message );
			}
			else
			{
				$message = [ 'status' => 0, 'info' => '操作失败' ];
				return $this->ajaxReturn( $message );
			}
		}
		else
		{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			return $this->ajaxReturn( $message );
		}
	}

	/**
	 * 删除约玩游戏
	 */
	public function deletegame()
	{

		I( "post.id" ) ? $id = I( "post.id" ) : '';
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'due_game' );
		$info = $Dao->where( "id=" . $id )->select();
		if( $info )
		{
			$Dao2 = D( 'due_skill' );
			$res = $Dao->where( "id=" . $info[0]['id'] )->save( array( 'status' => 2 ) );
			$res2 = $Dao2->where( "game_id=" . $info[0]['game_id'] )->save( array( 'status' => 2 ) );
			if( false !== $res && false !== $res2 )
			{
				$message = [ 'status' => 1, 'info' => '操作成功' ];
				return $this->ajaxReturn( $message );
			}
			else
			{
				$message = [ 'status' => 0, 'info' => '操作失败' ];
				return $this->ajaxReturn( $message );
			}
		}
		else
		{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			return $this->ajaxReturn( $message );
		}
	}

	/**
	 * 新增约玩游戏
	 */
	public function addgame()
	{
		$dao = D( 'Game' );
		$dao2 = D( 'due_game' );
		$id = is_numeric( I( 'get.id' ) ) ? I( 'get.id' ) : '';
		if( IS_POST )
		{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			$gameid = is_numeric( I( 'post.gameid' ) ) ? I( 'post.gameid' ) : '0';
//			$icon = I( 'post.icon' ) ? I( 'post.icon' ) : '';
			$poster = I( 'post.poster' ) ? I( 'post.poster' ) : '';
			$ord = I( 'post.ord' ) ? I( 'post.ord' ) : '';
			$number = I( 'post.number' ) ? I( 'post.number' ) : '0';
			$id = is_numeric( I( 'post.rid' ) ) ? I( 'post.rid' ) : '';
			$checkIsExist = $dao2->where( "gameid=" . $gameid )->select();
			$info = $dao->find( $gameid );
			if(empty($info['icon'])){
				return $this->ajaxReturn( array('status'=>0,'info'=>'游戏ICON未设置，请到运营－>游戏管理->游戏列表上传游戏的ICON') );
			}
			if( $checkIsExist )
			{
				$res = $dao2->where( 'id=' . $id )->save( array( 'gameid' => $gameid, 'number' => $number, 'ord' => $ord, 'name' => $info['name'], 'gametid' => $info['gametid'], 'icon' => $info['icon'], 'poster' => $poster ) );
			}
			else
			{
				$res = $dao2->add( array( 'gameid' => $gameid, 'number' => $number, 'ord' => $ord, 'name' => $info['name'], 'gametid' => $info['gametid'], 'icon' =>$info['icon'], 'poster' => $poster ) );
			}
			if( false !== $res )
			{
				$message['status'] = 1;
				$message['info'] = '操作成功';
			}

			return $this->ajaxReturn( $message );
		}
		$gameInfo = $dao->field( "gameid,gametid,name" )->select();
		if( $id )
		{
			$assign = $dao2->where( "id=$id" )->select();
			if( $assign )
			{
				$this->gameid = $assign[0]['gameid'];
				$this->icon = $assign[0]['icon'];
				$this->poster = $assign[0]['poster'];
				$this->ord = $assign[0]['ord'];
				$this->number = $assign[0]['number'];
			}
			else
			{
				$this->gameid = '';
				$this->icon = '';
				$this->poster = '';
				$this->ord = '';
				$this->number = '';
			}
		}
		$this->gameInfo = $gameInfo;
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->display();
	}

}
