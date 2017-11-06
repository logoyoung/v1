<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/6/6
 * Time: 上午9:45
 */

namespace Common\Model;

class SalaryModel extends PublicBaseModel
{

	//实例化数据库对象
	public function getDao($suffix)
	{
		return D('giftrecordcoin_'.$suffix);
	}
}
