<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/24
 * Time: 下午10:09
 */

namespace lib;


class RedEnvelope
{

	const ONE_RED_ENVELOPE_OPEN_COUNT = 50;

	const ONE_RED_ENVELOPE_MONEY = 10000;

	public function getReadEnvelopeList( int $money = RedEnvelope::ONE_RED_ENVELOPE_MONEY, int $count = RedEnvelope::ONE_RED_ENVELOPE_OPEN_COUNT )
	{
		return $this->_getRedEnvelopeList($money,$count);
	}

	private function _getRedEnvelopeList($money,$count)
	{

		$avg  = (int)( $money / $count );
		$rang = (int)( $avg / 10 );

		$max = $avg + $rang;
		$min = $avg - $rang;

		return $this->_generate( $money, $count, $max, $min );
	}

	/**
	 * 生成红包列表
	 * 正太分布规则
	 *
	 * @param int $money 红包总额
	 * @param int $count 红包个数
	 * @param int $max   每个红包最大金额
	 * @param int $min   每个红包最小金额
	 *
	 * @return array
	 */
	private function _generate( int $money, int $count, int $max, int $min )
	{
		$average = (int)( $money / $count );

		$result = [];

		for ( $i = 0; $i < $count; $i++ )
		{
			//因为小红包的数量通常是要比大红包的数量要多的，因为这里的概率要调换过来。
			//当随机数>平均值，则产生小红包
			//当随机数<平均值，则产生大红包
			if ( $this->_randomNextLong( $min, $max ) > $average )
			{
				$one = $min + $this->_xRandom( $min, $average );
				array_push( $result, $one );

				$money -= $one;
			}
			else
			{
				$one = $min + $this->_xRandom( $average, $max );
				array_push( $result, $one );

				$money -= $one;
			}
		}

		//如果还有余钱，则尝试加到小红包里，如果加不进去，则尝试下一个。
		while ( $money > 0 )
		{
			for ( $i = 0; $i < $count; $i++ )
			{
				if ( $money > 0 && $result[$i] < $max )
				{
					$result[$i]++;
					$money--;
				}
			}
		}

		// 如果钱是负数了，还得从已生成的小红包中抽取回来
		while ( $money < 0 )
		{
			for ( $i = 0; $i < $count; $i++ )
			{
				if ( $money < 0 && $result[$i] > $min )
				{
					$result[$i]--;
					$money++;
				}
			}
		}

		return $result;
	}

	private function _randomNextLong( $min, $max )
	{
		return rand( $min, $max + 1 );
	}

	private function _xRandom( $min, $max )
	{
		return (int)sqrt( rand( 0, pow( $max - $min, 2 ) ) );
	}
}