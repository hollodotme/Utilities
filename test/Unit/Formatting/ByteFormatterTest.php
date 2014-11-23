<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Test\Unit\Formatting;

use hollodotme\Utilities\Formatting\ByteFormatter;

class ByteFormatterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider byteFormatProvider
	 */
	public function testFormat( $bytes, $expected_string )
	{
		$formatter = new ByteFormatter( $bytes );

		$this->assertEquals( $expected_string, $formatter->format() );
	}

	public function byteFormatProvider()
	{
		return [
			[ 1, '1,00 Byte' ],
			[ 1023, '1.023,00 Byte' ],
			[ 1024, '1,00 KB' ],
			[ 1500, '1,46 KB' ],
			[ 1024 * 1024 - 1024, '1.023,00 KB' ],
			[ 1024 * 1024, '1,00 MB' ],
			[ 1024 * 1024 * 1024 - (1024 * 1024), '1.023,00 MB' ],
			[ 1024 * 1024 * 1024, '1,00 GB' ],
			[ 1234567890, '1,15 GB' ],
			[ 1024 * 1024 * 1024 * 1024 - (1024 * 1024 * 1024), '1.023,00 GB' ],
			[ 1024 * 1024 * 1024 * 1024, '1,00 TB' ],
		];
	}
}
 