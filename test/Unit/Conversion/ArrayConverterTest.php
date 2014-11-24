<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Test\Unit\Conversion;

use hollodotme\Utilities\ArrayConverter;

class ArrayConverterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider arrayToStdClassProvider
	 */
	public function testConvertToStdClass( array $array, \stdClass $expected )
	{
		$converter = new ArrayConverter( $array );

		$this->assertEquals( $expected, $converter->toStdClass() );
	}

	public function arrayToStdClassProvider()
	{
		$stdclass_1        = new \stdClass();
		$stdclass_1->{'0'} = 'test';

		$stdclass_2       = new \stdClass();
		$stdclass_2->unit = 'test';

		$stdclass_3        = new \stdClass();
		$stdclass_3->{'0'} = 1;
		$stdclass_3->unit  = 'test';

		$stdclass_4        = new \stdClass();
		$stdclass_4->{'1'} = 'unit';
		$stdclass_4->{'2'} = 'test';

		$stdclass_5        = new \stdClass();
		$stdclass_5->{'0'} = 'unit';
		$stdclass_5->test  = 'test';
		$stdclass_5->unit  = clone $stdclass_4;
		$stdclass_5->empty = new \stdClass();

		return [
			[
				[ ], new \stdClass()
			],
			[
				[ 'test' ], $stdclass_1
			],
			[
				[ 'unit' => 'test' ], $stdclass_2
			],
			[
				[ 1, 'unit' => 'test' ], $stdclass_3
			],
			[
				[ 1 => 'unit', 2 => 'test' ], $stdclass_4
			],
			[
				[
					'unit',
					'test'  => 'test',
					'unit'  => [ 1 => 'unit', 2 => 'test' ],
					'empty' => [ ]
				], $stdclass_5
			],
		];
	}
}
 