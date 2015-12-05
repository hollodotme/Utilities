<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Test\Unit\String;

use hollodotme\Utilities\Str;

class StringTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider splitStringProvider
	 */
	public function testGetWords( $string, array $expected_words )
	{
		$str = new Str( $string );

		$this->assertEquals( $expected_words, $str->getWords() );
	}

	public function splitStringProvider()
	{
		return [
			[ '', [ ] ],
			[ 'Unit', [ 'Unit' ] ],
			[ 'Unit Test', [ 'Unit', 'Test' ] ],
			[ 'Unìt Tést', [ 'Unit', 'Test' ] ],
			[ 'çity and îsland', [ 'city', 'and', 'island' ] ],
			[ 'some_and_something', [ 'some', 'and', 'something' ] ],
			[ 'some-and-something', [ 'some', 'and', 'something' ] ],
			[ 'some & something', [ 'some', 'something' ] ],
			[ 'some|something', [ 'some', 'something' ] ],
			[ 'säme büt difförent', [ 'same', 'but', 'difforent' ] ],
			[ 'Heiße Milch', [ 'Heisse', 'Milch' ] ],
			[ 'Î lòvé Öpen Ûnit Tests', [ 'I', 'love', 'Open', 'Unit', 'Tests' ] ],
		];
	}
}
