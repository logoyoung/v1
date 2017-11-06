<?php

namespace Admin\Controller;

use HP\Op\Admin;

class NewsController extends BaseController
{
	protected $pageSize = 20;
	protected $Infotype = array();
	protected $H5_to_pub = 1;//上架
	protected $H5_to_unpub = 2; //下架

	protected function _access()
	{
		return [
		    'index'=>['index'],
			'add'=>['index'],
		    'detail'=>['index'],
		    'savenew'=>['index'],
		    'del'=>['index'],
		    'publish'=>['index'],
			'unpublic'=>['index'],
		    'stat'=>['index'],
		    'scan'=>['index'],
		    'checkRoomIdsIsOK'=>['index'],
			'htoapplive'=>['index'],
			'informationapp'=>['index'],
			'deletetoapp'=>['index'],
			'recommendtoapp'=>['index']
		];
	}

	public function index()
	{
//		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : '';
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : '';
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : '';
		I( "get.IMtype" ) ? $IMtype = I( "get.IMtype" ) : '';
		I( "get.IMstatus" ) ? $IMstatus = I( "get.IMstatus" ) : '';
		I( "get.title" ) ? $title = I( "get.title" ) : '';
		I( "get.client" ) ? $client = I( "get.client" ) : '';
		I( "get.recommend" ) ? $recommend = I( "get.recommend" ) : '';
		$where = 1;
		if( $IMtype )
		{
			$where .= " and tid =$IMtype";
		}
		if( $IMstatus == 1 )
		{//已发布
			$where .= " and  ispublish=1";
		}
		if( $IMstatus == 2 )
		{//未发布
			$where .= " and  ispublish=0";
		}
		if( $IMstatus == 3 )
		{
			$date = date( 'Y-m-d H:i:s' );
			$where .= " and  etime < '$date'";
		}
		else
		{
			if( $stime )
			{
				$where .= " and stime >='$stime'";
			}
			if( $etime )
			{
				$where .= " and  etime<='$etime'";
			}
		}
		if( $IMstatus == 4 )
		{//已下架
			$where .= " and  ispublish=2";
		}
		if( $title )
		{
			$where .= " and title like '%$title%'";
		}
		if( $client )
		{
			$where .= " and  client=$client";
		}
		if( $recommend )
		{
			$where .= " and  isrecommend=$recommend";
		}
		$Dao = D( 'admin_information' );
		$list = array();
		$total = $Dao->where( $where )->count();
		$Page = new \HP\Util\Page( $total, $this->pageSize );
		$res = $Dao->where( $where )->order( 'utime desc ' )->limit( $Page->firstRow . ',' . $Page->listRows )->select();
		if( $res )
		{
			foreach ( $res as $v )
			{
				$temp['id'] = $v['id'];
				$temp['title'] = $v['title'];
				if( $v['poster'] )
				{
					$temp['poster'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $v['poster'];
				}
				else
				{
					$temp['poster'] = $v['poster'];
				}
				$temp['client'] = $v['client'];
				$temp['isrecommend'] = $v['isrecommend'];
				$temp['status'] = $v['status'];
				$temp['is_login'] = $v['is_login'];
				$temp['typename'] = $v['tid'];
				$temp['ispublish'] = $v['ispublish'];
				$temp['ctime'] = $v['ctime'];
				$temp['stime'] = $v['stime'];
				$temp['utime'] = $v['utime'];
				$temp['etime'] = $v['etime'];
				array_push( $list, $temp );
			}
		}
		$this->data = $list;
		$this->IMtype = array( '5' => '新闻', '8' => '活动', '13' => '公告' );
		$this->IMstatus = array( '1' => '已发布', '2' => '未发布', '3' => '已结束', '4' => '已下架' );
		$this->client = array( '1' => 'App端', '2' => 'Web端', '3' => 'H5' );
		$this->recommend = array( '1' => '焦点推荐', '2' => '列表推荐' );
		$this->page = $Page->show();
		$this->display();
	}

	public function add()
	{
		$this->display();
	}


	public function del()
	{
		$id = I( 'post.id' ) ? I( 'post.id' ) : '';
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'admin_information' );
		$Dao->fetchSql( true );
		$res = $Dao->where( "id=$id" )->save( array( 'status' => 2, 'isrecommend' => 0 ) );
		dump( $res );
//		if($res){
//			return $this->ajaxReturn( array( 'status' => 1, 'info' => '删除成功' ) );
//		}
	}

	/**
	 *添加一条新数据
	 */
	public function savenew()
	{
		I( "post.infoid" ) ? $id = I( "post.infoid" ) : '';
		$tid = I( 'post.type' ) ? I( 'post.type' ) : '';
		$title = I( 'post.title' ) ? I( 'post.title' ) : '';
		$poster = I( 'post.poster' ) ? I( 'post.poster' ) : '';
		$thumbnail = I( 'post.thumbnail' ) ? I( 'post.thumbnail' ) : '';
		$timestart = I( 'post.timestart' ) ? I( 'post.timestart' ) : date( 'Y-m-d H:i:s', time() );
		$timeend = I( 'post.timeend' ) ? I( 'post.timeend' ) : date( 'Y-m-d H:i:s', strtotime( "next year" ) );
		$content = I( 'post.content' ) ? I( 'post.content' ) : '';
		$client = I( 'post.client' ) ? I( 'post.client' ) : ''; // 手机端为1 web端为2 H5为3
		if( !in_array( $tid, array( 5, 8, 13 ) ) )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		if( strtotime( $timeend ) <= strtotime( $timestart ) )
		{
			$message = [ 'status' => 0, 'info' => '结束时间必须大于开始时间' ];
			return $this->ajaxReturn( $message );
		}
//		if( !$content )
//		{
//			$message = [ 'status' => 0, 'info' => '资讯内容不允许为空' ];
//			return $this->ajaxReturn( $message );
//		}
		$Dao = D( 'admin_information' );
		$data = array(
			'tid' => $tid,
			'title' => $title,
			'content' => $content,
			'poster' => $poster,
			'thumbnail' => $thumbnail,
			'stime' => $timestart,
			'etime' => $timeend,
			'adminid' => Admin::getUid()
		);
		if( $tid == 8 )
		{//活动
			$isLogin = I( 'post.islogin' ) ? I( 'post.islogin' ) : '';// 1不登录  2登录
			$url = I( 'post.url' ) ? I( 'post.url' ) : '';//
			$showtype = I( 'post.showtype' ) ? I( 'post.showtype' ) : '';// 1公告形式  2新页面
			$activetype = I( 'post.activetype' ) ? I( 'post.activetype' ) : 1;//1首页  2直播间活动  3首页&直播间  4指定直播间
			$certid = I( 'post.certid' ) ? I( 'post.certid' ) : '';// 资质id
			$skillid = I( 'post.skillid' ) ? I( 'post.skillid' ) : '';//技能id
			$luids = I( 'post.luids' ) ? I( 'post.luids' ) : '0';//主播id
			if( !in_array( $client, array( 1, 2, 3 ) ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '活动所属平台未选或参数有误' ) );
			}
			if( !in_array( $activetype, array( 1, 2, 3, 4 ) ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '活动类型未选或参数有误' ) );
			}
			if( $activetype == 4 )
			{
				if( !$luids )
				{
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '指定直播间的主播ID不能为空' ) );
				}
				else
				{
					$luids = $this->checkRoomIdsIsOK( $luids ); //TODO
				}
			}
			if( !in_array( $isLogin, array( 1, 2 ) ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '是否登录可见未选或参数有误' ) );
			}
			if( $isLogin == 1 )
			{ //兼容以前App端逻辑
				$isLogin = 0;
			}
			if( $isLogin == 2 )
			{ //兼容以前App端逻辑
				$isLogin = 1;
			}
			if( !in_array( $showtype, array( 1, 2, 3, 4 ) ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '展现形式未选或参数有误' ) );
			}
			if( $showtype == 2 )
			{
				if( !$url )
				{
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '新页面URL,必须填写' ) );
				}
			}
			if( $showtype == 3 )
			{
				if( empty( $luids ) )
				{
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '请填写正确的主播ID' ) );
				}
			}
			if( $showtype == 4 )
			{
				if( empty( $certid ) || empty( $skillid ) || empty( $luids ) )
				{
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '资质ID、技能ID、主播id,不允许为空' ) );
				}
			}
			$data['client'] = $client;
			$data['url'] = $url;
			$data['is_login'] = $isLogin;
			$data['show_type'] = $showtype;
			$data['type'] = $activetype;
			$data['luids'] = $luids ? $luids : '';
			$data['certid'] = $certid;
			$data['skillid'] = $skillid;
			$data['utime'] = date( 'Y-m-d H:i:s' );
		}

		if( ( $data['client'] == 3 ) && ( $data['show_type'] == 1 ) )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => 'H5平台不支持公告形式' ) );
		}
		if( ( $data['client'] == 1 && $data['show_type'] == 1 ) )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => 'App端不支持公告展现形式' ) );
		}
		if( $id )
		{
			if( $data['tid'] == 8 )
			{
				$data['isrecommend'] = 1;//焦点
				$publicCount = $Dao->where( "ispublish=1 and isrecommend=1 and client=" . $client )->count();
			}
			else
			{
				$data['isrecommend'] = 2; //列表
				$publicCount = $Dao->where( "ispublish=1 and isrecommend=2 and client=" . $client )->count();
			}
			if( $publicCount > INFORMATION_RECOMMENT_NUMBER )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '推荐位已满!' ) );
			}
			$res = $Dao->where( "id=$id" )->save( $data );
		}
		else
		{
			$res = $Dao->add( $data );
		}
		if( $res !== false )
		{
			return $this->ajaxReturn( array( 'status' => 1, 'info' => '操作成功' ) );
		}

	}

	public function detail()
	{
		$id = I( 'get.id' ) ? I( 'get.id' ) : ''; // 资讯id
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'admin_information' );
		$assign = $id ? $Dao->find( $id ) : [];
//		$assign['stime'] = substr( $assign['stime'], 0, 10 );
//		$assign['etime'] = substr( $assign['etime'], 0, 10 );
		$assign['content'] = html_entity_decode( $assign['content'] );
		$this->assign( $assign );
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->display();
	}

	/**
	 * 发布
	 */
	public function publish()
	{
		$data = array();
		$id = I( 'post.id' ) ? I( 'post.id' ) : '0'; // 资讯id
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$isrecommend = I( 'post.isrecommend' ) ? I( 'post.isrecommend' ) : '0'; //
		if( !in_array( $isrecommend, array( 0, 1, 2 ) ) )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'admin_information' );
		$Info = $Dao->find( $id );
		if( $Info )
		{

			if( $Info['tid'] == 8 )
			{
				$data['isrecommend'] = 1;//焦点
				$publicCount = $Dao->where( "ispublish=1 and isrecommend=1 and client=" . $Info['client'] )->count();
			}
			else
			{
				$data['isrecommend'] = 2; //列表
				$publicCount = $Dao->where( "ispublish=1 and isrecommend=2 and client=" . $Info['client'] )->count();
			}
			if( $publicCount >= INFORMATION_RECOMMENT_NUMBER )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '推荐位已满!' ) );
			}
			if( $Info['show_type'] == 1 && $Info['client'] == 2 )
			{
				$data['url'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . '/news.php?id=' . $id;
			}
			if( $Info['client'] == 3 && $Info['show_type'] == 1 )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => 'H5平台不支持支持公告形式' ) );
			}
			if( $Info['client'] == 1 && $Info['show_type'] == 1 )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => 'App端不支持公告展现形式' ) );
			}
			$data['ispublish'] = 1;
			$data['utime'] = date( 'Y-m-d H:i:s' );
			$res = $Dao->where( "id=$id" )->save( $data );
			if( $res )
			{
				return $this->ajaxReturn( array( 'status' => 1, 'info' => '操作成功' ) );
			}
			else
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '操作失败' ) );
			}
		}
	}

	/**
	 * 取消发布
	 */
	public function unpublic()
	{
		$id = I( 'post.id' ) ? I( 'post.id' ) : '0'; // 资讯id
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'admin_information' );
		$res = $Dao->find( $id );
		if( $res )
		{
//			$Dao2 = D( 'recommend_information' );
//			$info = $Dao2->where( "id=" . $res['isrecommend'] . " and client=" . $res['client'] )->select();
//			if( $info[0]['list'] )
//			{
//				$lists = explode( ',', $info[0]['list'] );
//				$lists = array_merge( array_diff( $lists, array( $id ) ) );
//				$idList = implode( ',', $lists );
//				$Dao2->where( "id=" . $res['isrecommend'] . " and client=" . $res['client'] )->save( array( 'list' => $idList, 'utime' => date( 'Y-m-d H:i:s' ) ) );
//			}
			$res = $Dao->where( "id=$id" )->save( array( 'status' => 0, 'isrecommend' => 0, 'ispublish' => 2 ) );//关闭
			if( false !== $res )
			{
				return $this->ajaxReturn( array( 'status' => 1, 'info' => '操作成功' ) );
			}
			else
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '操作失败' ) );
			}
		}
	}

	/**
	 *
	 * 推荐列表
	 */
	public function stat()
	{
		$slist = array();
		$id = I( 'get.isrecommend' ) ? I( 'get.isrecommend' ) : '0'; // 资讯id
		$client = I( 'get.client' ) ? I( 'get.client' ) : '1'; // 资讯id
		if( !$id || !in_array( $client, array( 1, 2 ) ) )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'recommend_information' );
		$res = $Dao->where( "id=$id  and client=$client" )->select();
		if( $res[0]['list'] )
		{
			$Dao2 = D( 'admin_information' );
			$list = $Dao2->where( 'id in (' . $res[0]['list'] . ')' )->select();
			if( $list )
			{
				foreach ( $list as $v )
				{
					$tem[$v['id']] = $v;
				}
				$ids = explode( ',', $res[0]['list'] );
				for ( $i = 0, $k = count( $ids ); $i < $k; $i++ )
				{
					$temp['id'] = $tem[$ids[$i]]['id'];
					$temp['title'] = $tem[$ids[$i]]['title'];
					if( $tem[$ids[$i]]['poster'] )
					{
						$temp['poster'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $tem[$ids[$i]]['poster'];
					}
					else
					{
						$temp['poster'] = $tem[$ids[$i]]['poster'];
					}
					$temp['client'] = $tem[$ids[$i]]['client'];
					$temp['is_login'] = $tem[$ids[$i]]['is_login'];
					$temp['typename'] = $tem[$ids[$i]]['tid'];
					$temp['stime'] = $tem[$ids[$i]]['stime'];
					$temp['etime'] = $tem[$ids[$i]]['etime'];
					array_push( $slist, $temp );
				}
			}
		}
		$this->isrecommend = $id;
		$this->client = $client;
		$this->data = $slist;
		$this->display();
	}

	/**
	 * 浏览
	 */
	public function scan()
	{
		$id = I( 'post.id' ) ? I( 'post.id' ) : '0'; // 资讯id
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D( 'admin_information' );
		$info = $Dao->find( $id );
		if( $info['show_type'] == 2 )
		{
			if( $info['client'] == 3 )
			{
				$jump_url = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . '/mobile/index.html';
			}
			else
			{
				$jump_url = $info['url'];
			}

		}
		elseif( ( $info['show_type'] == 3 ) )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => 'App直播间不支持预览' ) );
		}
		elseif( $info['show_type'] == 4 )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => '约玩详情不支持预览' ) );
		}
		else
		{
			$jump_url = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . '/news.php?id=' . $id;
		}
		return $this->ajaxReturn( array( 'status' => 1, 'info' => $jump_url ) );
	}

	public function checkRoomIdsIsOK( $luids )
	{
		$luids = array_filter( explode( ',', $luids ) );
		if( $luids )
		{
			return implode( ',', $luids );
		}
		else
		{
			return '';
		}
	}

	/**
	 * 已下是H5活动推荐到App直播间相关
	 */
	public function informationapp()
	{
		$Dao = D( 'app_information' );
		$status = I( 'get.status' ) ? I( 'get.status' ) : '';
		$infoid = I( 'get.infoid' ) ? I( 'get.infoid' ) : '';
		if( in_array( $status, array( 1, 2, 3 ) ) )
		{
			if( $status == 3 )
			{
				$status = 0;
			}
			$where['status'] = $status;
		}
		if( $infoid )
		{
			$where['info_id'] = $infoid;
		}
		$data = $Dao->where( $where )->select();
		if( $data )
		{
			$idao = D( 'admin_information' );
			$ids = implode( ',', array_column( $data, "info_id" ) );
			$titles = $idao->where( "id in ($ids)" )->getField( 'id,title' );
			foreach ( $data as $k => $v )
			{
				$data[$k]['title'] = $titles[$v['info_id']] ? $titles[$v['info_id']] : '';
			}
		}
		else
		{
			$data = array();
		}
		$this->data = $data;
		$this->status = [ '0' => '请选择状态', '1' => '已推荐', '2' => '已下架', '3' => '未推荐' ];
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->display();
	}


	public function htoapplive()
	{
		$Dao = D( 'admin_information' );
		$hdao = D( 'app_information' );
		if( IS_POST )
		{
			$id = I( 'post.rid' ) ? I( 'post.rid' ) : '';
			$data['info_id'] = I( 'post.infoid' ) ? I( 'post.infoid' ) : '';
			$data['thumbnail'] = I( 'post.thumbnail' ) ? I( 'post.thumbnail' ) : '';
			if( empty( $data['info_id'] ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '请选择活动！' ) );
			}
			if( empty( $data['thumbnail'] ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '请上传缩略图！' ) );
			}
			$data['adminid'] = Admin::getUid();
			$data['utime'] = date( 'Y-m-d H:i:s' );
			if( $id )
			{ //修改
				$res = $hdao->where( "id=$id" )->save( $data );
			}
			else
			{
				$res = $hdao->add( $data );
			}
			if( $res !== false )
			{
				return $this->ajaxReturn( array( 'status' => 1, 'info' => '操作成功' ) );
			}
			else
			{
				return $this->ajaxReturn( array( 'status' => 0, 'info' => '操作失败' ) );
			}
		}
		$id = I( 'get.id' ) ? I( 'get.id' ) : '';
		if( $id )
		{
			$assign = $hdao->where( "id=$id" )->select();
			if( $assign )
			{
				$this->info_id = $assign[0]['info_id'];
				$this->thumbnail = $assign[0]['thumbnail'];

			}
			else
			{
				$this->info_id = '';
				$this->thumbnail = '';

			}
		}
		$data = $Dao->field( 'id,title' )->where( "ispublish=1" )->select();
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->info = $data;
		$this->display();
	}

	public function recommendtoapp()
	{
		$id = I( 'post.id' ) ? I( 'post.id' ) : '';
		if( empty( $id ) )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => '请求非法' ) );
		}
		$dao = D( 'app_information' );
		$isexist = $dao->where( "status=$this->H5_to_pub" )->select();
		if( $isexist )
		{
			$ids = implode( ',', array_column( $isexist, "id" ) );
			$dao->where( "id in ($ids)" )->save( [ 'status' => $this->H5_to_unpub, 'utime' => date( 'Y-m-d H:i:s' ), 'adminid' => Admin::getUid() ] );
		}
		$res = $dao->where( "id=$id" )->save( [ 'status' => $this->H5_to_pub ] );
		if( $res !== false )
		{
			return $this->ajaxReturn( array( 'status' => 1, 'info' => '操作成功' ) );
		}
		else
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => '操作失败' ) );
		}
	}

	public function deletetoapp()
	{
		$id = I( 'post.id' ) ? I( 'post.id' ) : '';
		if( empty( $id ) )
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => '请求非法' ) );
		}
		$dao = D( 'app_information' );
		$res = $dao->where( "id=$id" )->save( [ 'status' => $this->H5_to_unpub ] );
		if( $res !== false )
		{
			return $this->ajaxReturn( array( 'status' => 1, 'info' => '操作成功' ) );
		}
		else
		{
			return $this->ajaxReturn( array( 'status' => 0, 'info' => '操作失败' ) );
		}
	}


}
