<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Test\Unit\Validation;

use hollodotme\Utilities\String;
use hollodotme\Utilities\Validation\FluidValidator;

class FluidValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testBoolResultIsTrueAfterConstruction()
	{
		$this->assertInternalType( 'bool', FluidValidator::check()->getBoolResult() );
		$this->assertTrue( FluidValidator::check()->getBoolResult() );
		$this->assertTrue( ( new FluidValidator() )->getBoolResult() );
	}

	public function testMessagesAreEmptyAfterConstruction()
	{
		$this->assertInternalType( 'array', FluidValidator::check()->getMessages() );
		$this->assertEmpty( FluidValidator::check()->getMessages() );
		$this->assertEmpty( ( new FluidValidator() )->getMessages() );
	}

	public function testNotEmptyArray()
	{
		$var    = [ 'I am not empty' ];
		$result = FluidValidator::check()
		                        ->isArray( $var )
		                        ->notEmpty( $var )
		                        ->getBoolResult();

		$this->assertTrue( $result );
	}

	public function testEmptyArray()
	{
		$var    = [ ];
		$result = FluidValidator::check()
		                        ->isArray( $var )
		                        ->notEmpty( $var )
		                        ->getBoolResult();

		$this->assertFalse( $result );
	}

	public function testIsNotAnArray()
	{
		$var    = 'I am not an array';
		$result = FluidValidator::check()
		                        ->isArray( $var )
		                        ->getBoolResult();

		$this->assertFalse( $result );
	}

	/**
	 * @dataProvider notEmptyStringProvider
	 */
	public function testNotEmptyString( $string )
	{
		$result = FluidValidator::check()
		                        ->notEmptyString( $string )
		                        ->getBoolResult();

		$this->assertTrue( $result );
	}

	public function notEmptyStringProvider()
	{
		return [
			[ '-' ],
			[ new String( 'not emtpy' ) ],
			[ 123 ],
			[ 123.45 ],
			[ 0 ],
			[ '0' ],
		];
	}

	/**
	 * @dataProvider emptyStringProvider
	 */
	public function testEmptyString( $string )
	{
		$result = FluidValidator::check()
		                        ->notEmptyString( $string )
		                        ->getBoolResult();

		$this->assertFalse( $result );
	}

	public function emptyStringProvider()
	{
		return [
			[ '' ],
			[ ' ' ],
			[ new String( "\n" ) ],
			[ "\r" ],
			[ false ],
			[ null ],
			[ true ],
		];
	}

	public function testGetMessages()
	{
		$expected = [
			'No int',
			'No string',
			'Empty array',
			'Empty string',
			'No array',
			'No positive int',
		];

		$validator = FluidValidator::check();
		$result    = $validator->isInt( 123.56, 'No int' )
		                       ->isString( true, 'No string' )
		                       ->notEmpty( [ ], 'Empty array' )
		                       ->notEmptyString( ' ', 'Empty string' )
		                       ->isArray( '', 'No array' )
		                       ->positiveInt( -1, 'No positive int' )
		                       ->getBoolResult();

		$messages = $validator->getMessages();

		$this->assertFalse( $result );
		$this->assertEquals( $expected, $messages );
	}
}
 