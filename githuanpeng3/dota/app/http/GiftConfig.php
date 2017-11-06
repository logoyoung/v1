<?php

//include __DIR__ . "/../../../include/init.php";
namespace dota\app\http;

class GiftConfig
{
	private $configIdList = [ 1 ];

	private $service;

	private static $configInfo;

	const ERR_ID_NOT_VALID         = 1000;
	const ERR_DEL_ITEM_FAILED          = 1001;
	const ERR_LOGIC_RUN_ERROR          = 1002;
	const ERR_ADD_ITEM_FAILED          = 1003;
	const ERR_PARAM_NOT_VALID          = 1004;
	const ERR_PARAM_ORDER_NOT_VALID    = 1008;
	const ERR_UPDATE_ITEM_ORDER_FAILED = 1005;
	const ERR_UPDATE_CONFIG_FAILED     = 1006;
	const ERR_CONFIG_ID_NOT_VALID      = 1007;

	private static $errMsg = [
		self::ERR_ID_NOT_VALID         => "条目不存在",
		self::ERR_DEL_ITEM_FAILED          => "删除条目失败",
		self::ERR_LOGIC_RUN_ERROR          => "程序逻辑错误",
		self::ERR_ADD_ITEM_FAILED          => "添加失败，系统错误或确保同一个礼物的设置数量相同",
		self::ERR_PARAM_NOT_VALID          => "请求参数逻辑非法",
		self::ERR_UPDATE_ITEM_ORDER_FAILED => "更新条目顺序失败",
		self::ERR_UPDATE_CONFIG_FAILED     => "更新配置失败",
		self::ERR_CONFIG_ID_NOT_VALID      => "配置不存在",
		self::ERR_PARAM_ORDER_NOT_VALID    => "配置参数排序值无效",
	];


	private function getService():\service\room\RoomGiftService
	{
		if ( !$this->service )
		{
			$this->service = new \service\room\RoomGiftService();
		}

		return $this->service;
	}

	private function getConfig( $configId )
	{
		if ( !isset( static::$configInfo[$configId] ) )
		{
			$config = $this->getService()->getRoomConfigInfo( $configId );
			foreach ( $config as $key => $value )
			{
				$config[$key]['isModify'] = 0;
			}

			static::$configInfo[$configId] = $config;
		}

		return static::$configInfo[$configId];
	}

	private function checkOrderList( &$orderList )
	{
		asort( $orderList );
		$i = 0;

		foreach ( $orderList as $value )
		{
			if ( $i != $value )
			{
				return false;
			}

			$i++;
		}

		return true;
	}

	public function changeConfig()
	{
		$configId = intval($_POST['configId']);
		$configFile = $_POST['configFile'];

		if(!is_array($configFile))
		{
			$code = self::ERR_PARAM_NOT_VALID;
			$msg = static::$errMsg[$code];

			render_error_json($msg);
		}

		if ( !$configId || !in_array( $configId, $this->configIdList ) )
		{
			$code = self::ERR_CONFIG_ID_NOT_VALID;
			$msg  = static::$errMsg[$code];
			render_error_json( $msg, $code, 2 );
		}

		//获取当前配置数组
		$config = $this->getConfig( $configId );
		//config id bu 存在 需要返回
		if ( !$config )
		{
			$code = self::ERR_CONFIG_ID_NOT_VALID;
			$msg  = static::$errMsg[$code];
			render_error_json( $msg, $code, 2 );
		}

		$idList  = array_column( $config, "id" );
		$optList = array_column( $configFile, "opt" );

		$delIdList    = [];
		$addIndexList = [];
		$nonIndexList = [];
		$orderList    = [];

		foreach ( $optList as $index => $opt )
		{
			if ( $opt == "del" )
			{
				$delIdList[$index] = $configFile[$index]['item_id'];

			}
			elseif ( $opt == 'add' )
			{
				array_push( $addIndexList, $index );
				$orderList[$index] = $configFile[$index]['order'];
			}
			else
			{
				$orderList[$index] = $configFile[$index]['order'];
				array_push( $nonIndexList, $index );
			}
		}

		asort( $orderList );

		if ( !$this->checkOrderList( $orderList ) )
		{
			$code = self::ERR_PARAM_ORDER_NOT_VALID;
			$msg  = static::$errMsg[$code];

			render_error_json( $msg, $code );
		}

		$db = $this->getService()->getDbHandler()->getDb();

		try
		{
			$db->beginTransaction();

			//删除逻辑
			foreach ( $delIdList as $index => $id )
			{
				if ( !$id || !in_array( $id, $idList ) )
				{
					$code = self::ERR_ID_NOT_VALID;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				if ( !$this->getService()->delConfigItemFromDb( $id, $configId ) )
				{
					$code = self::ERR_DEL_ITEM_FAILED;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				$index = array_search( $id, $idList );
				if ( !isset( $config[$index] ) )
				{
					$code = self::ERR_LOGIC_RUN_ERROR;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				$config[$index]['isModify'] = 1;
			}

			//添加逻辑
			foreach ( $addIndexList as $index )
			{
				if ( !isset( $configFile[$index] ) )
				{
					$code = self::ERR_LOGIC_RUN_ERROR;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				$giftId = $configFile[$index]['gift_id'];
				$num    = $configFile[$index]['num'];
				$order  = $configFile[$index]['order'];

				if ( !$this->getService()->addConfigItemFromDb( $configId, $giftId, $num, $order ) )
				{
					$code = self::ERR_ADD_ITEM_FAILED;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}
			}

			//修改逻辑
			foreach ( $nonIndexList as $index )
			{
				if ( !isset( $configFile[$index] ) )
				{
					$code = self::ERR_LOGIC_RUN_ERROR;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				$id = $configFile[$index]['item_id'];
				if ( !$id || !in_array( $id, $idList ) )
				{
					$code = self::ERR_ID_NOT_VALID;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}


				$oldIndex = array_search( $id, $idList );
				if ( !isset( $config[$oldIndex] ) )
				{
					$code = self::ERR_LOGIC_RUN_ERROR;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				//获取单个ID的配置信息
				$info = $config[$oldIndex];
				if ( $info['isModify'] == 1 )
				{
					$code = self::ERR_PARAM_NOT_VALID;
					$msg  = static::$errMsg[$code];

					throw new \Exception( $msg, $code );
				}

				$newOrder = $configFile[$index]['order'];
				$oldOrder = $info['order'];

				if ( $newOrder != $oldOrder )
				{
					if ( !$this->getService()->updateConfigItemOrderFromDb( $id, $configId, $newOrder ) )
					{
						$code = self::ERR_UPDATE_ITEM_ORDER_FAILED;
						$msg  = self::$errMsg[$code];

						throw new \Exception( $msg, $code );
					}

					$config[$oldIndex]['isModify'] == 1;
				}

			}

			$db->commit();

			$this->log("commit success");

			if ( !$this->getService()->update( $configId ) )
			{
				//todo log update cache failed
//				$this->log()
			}
			$this->log("update configid:$configId");
			render_json( [] );

		} catch ( \Exception $exception )
		{
			$code = $exception->getCode();
			$msg  = $exception->getMessage() . ",online:" . $exception->getLine();
			$this->log("msg:$msg,code:$code");

			$db->rollback();
			render_error_json( $msg, $code, 2 );
		}

		//找出所有del操作
		//查看del时候有ID存在
		//检查ID是否存在于数据库
		//存入删除操作数组
		//从当前配置数组中 删除del 并且从新排序

		//找出所有add操作
		//根据modify操作，循环修改当前配置数组，并且验证合法性

		//找出modify操作
		//根据modify操作，循环修改当前配置数组，并且验证合法性


		//最终结果与当前配置进行匹配，找出哪些被修改了
		//找出修改操作并进行执行

		//如果验证没有问题，执行sql更新
	}

	public function getConfigInfo()
	{
		$configId = $_POST['configId'];

		if ( !$configId || !in_array( $configId, $this->configIdList ) )
		{
			$code = self::ERR_CONFIG_ID_NOT_VALID;
			$msg  = static::$errMsg[$code];
			render_error_json( $msg, $code, 2 );
		}

		$info = $this->getService()->getRoomConfigGiftInfo($configId);

		if(!$info)
		{
			render_json([]);
		}
		else
		{
			render_json($info);
		}
	}

	private function log($msg)
	{
		$dir = "config_gift";
		write_log( $msg, $dir );
	}
}


//$configFile = [
//	[
//		'gift_id' => 36,
//		'num'     => 1,
//		'order'   => 0,
//		'id'      => 4,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 32,
//		'num'     => 1,
//		'order'   => 1,
//		'id'      => 5,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 520,
//		'order'   => 2,
//		'id'      => 6,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 200,
//		'order'   => 3,
//		'id'      => 7,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 100,
//		'order'   => 4,
//		'id'      => 8,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 666,
//		'order'   => 5,
//		'id'      => 9,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 888,
//		'order'   => 6,
//		'id'      => 10,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 999,
//		'order'   => 7,
//		'id'      => 11,
//		'opt'     => 'modify'
//	],
//	[
//		'gift_id' => 31,
//		'num'     => 1000,
//		'order'   => 8,
//		'id'      => 0,
//		'opt'     => 'add'
//	],
//];
//
//$configId = 1;
//
//$giftConfigobh = new GiftConfig();
//
////var_dump( $giftConfigobh->changeConfig( $configId, $configFile ) );
//
//$giftConfigobh->getConfigInfo($configId);



//优先级比较
// del > add > modify