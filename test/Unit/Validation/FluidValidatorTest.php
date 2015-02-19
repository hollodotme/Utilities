<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities\Test\Unit\Validation;

use hollodotme\Utilities\Test\Unit\Fixures\ValueObjects;
use hollodotme\Utilities\Validation\FluidValidator;

class FluidValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialStatus()
	{
		$validator = new FluidValidator();

		$this->assertTrue( $validator->getBoolResult() );
		$this->assertEmpty( $validator->getMessages() );
		$this->assertInternalType( 'array', $validator->getMessages() );
	}

	public function testStatusAfterResetIsSameAsInitialStatus()
	{
		$validator = new FluidValidator();

		$this->assertFalse( $validator->isString( null, 'TestMessage' )->getBoolResult() );
		$this->assertEquals( [ 'TestMessage' ], $validator->getMessages() );

		$this->assertFalse( $validator->getBoolResult() );
		$this->assertNotEmpty( $validator->getMessages() );
		$this->assertInternalType( 'array', $validator->getMessages() );

		$validator->reset();

		$this->assertTrue( $validator->getBoolResult() );
		$this->assertEmpty( $validator->getMessages() );
		$this->assertInternalType( 'array', $validator->getMessages() );
	}

	public function testCanSuffixOrNull()
	{
		$validator = new FluidValidator();

		$this->assertFalse( $validator->isString( null, 'TestMessage' )->getBoolResult() );
		$this->assertEquals( [ 'TestMessage' ], $validator->getMessages() );

		$validator->reset();

		$this->assertTrue( $validator->isStringOrNull( null, 'TestMessage' )->getBoolResult() );
		$this->assertEquals( [ ], $validator->getMessages() );
	}

	/**
	 * @dataProvider unknownMethodProvider
	 * @expectedException \hollodotme\Utilities\Validation\Exceptions\FluidValidatorCheckMethodIsNotCallable
	 */
	public function testCallingUnknownMethodFails( $unknown_method )
	{
		$validator = new FluidValidator();
		$validator->{$unknown_method}( null, 'TestMessage' );
	}

	public function unknownMethodProvider()
	{
		return [
			[ 'OrNull' ],
			[ 'orNull' ],
			[ 'unknownMethod' ],
			[ 'isStringOrNullString' ],
		];
	}

	public function testRecordingMessagesStopsAfterFirstFailedValidationInStopOnFirstFailMode()
	{
		$validator = new FluidValidator( FluidValidator::MODE_STOP_ON_FIRST_FAIL );
		$validator->isString( 'Yes', 'Succeeds' )
		          ->isString( null, 'First fail' )
		          ->isString( null, 'Second fail' )
		          ->isString( 'Yes', 'Succeeds' );

		$this->assertFalse( $validator->getBoolResult() );
		$this->assertEquals( [ 'First fail' ], $validator->getMessages() );
	}

	public function testRecordingMessagesContinuesAfterFirstFailedValidationInCheckAllMode()
	{
		$validator = new FluidValidator( FluidValidator::MODE_CHECK_ALL );
		$validator->isString( 'Yes', 'Succeeds' )
		          ->isString( null, 'First fail' )
		          ->isString( null, 'Second fail' )
		          ->isString( 'Yes', 'Succeeds' )
		          ->isString( null, 'Third fail' );

		$this->assertFalse( $validator->getBoolResult() );
		$this->assertEquals( [ 'First fail', 'Second fail', 'Third fail' ], $validator->getMessages() );
	}

	/**
	 * @dataProvider isNonEmptyStringProvider
	 */
	public function testIsNonEmptyString( $value, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isNonEmptyString( $value, 'String is empty' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isNonEmptyStringProvider()
	{
		return [
			[ '', false, [ 'String is empty' ] ],
			[ ' ', false, [ 'String is empty' ] ],
			[ "\n", false, [ 'String is empty' ] ],
			[ "\r", false, [ 'String is empty' ] ],
			[ "\t", false, [ 'String is empty' ] ],
			[ "\x0B", false, [ 'String is empty' ] ],
			[ "\0", false, [ 'String is empty' ] ],
			[ "Unit-Test", true, [ ] ],
			[ "1", true, [ ] ],
			[ "0", true, [ ] ],
			[ "null", true, [ ] ],
			[ "1.23", true, [ ] ],
			[ 12, true, [ ] ],
			[ 12.3, true, [ ] ],
			[ new ValueObjects\ObjectWithToStringMethod( '' ), false, [ 'String is empty' ] ],
			[ new ValueObjects\ObjectWithToStringMethod( 'Unit-Test' ), true, [ ] ],
			[ new ValueObjects\ObjectWithoutToStringMethod( 'Unit-Test' ), false, [ 'String is empty' ] ],
			[ new \stdClass(), false, [ 'String is empty' ] ],
			[ false, false, [ 'String is empty' ] ],
			[ true, false, [ 'String is empty' ] ],
			[ null, false, [ 'String is empty' ] ],
		];
	}

	/**
	 * @dataProvider isNotEmptyProvider
	 */
	public function testIsNotEmpty( $value, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isNotEmpty( $value, 'Is empty' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isNotEmptyProvider()
	{
		return [
			[ ' ', true, [ ] ],
			[ 'Unit', true, [ ] ],
			[ 123, true, [ ] ],
			[ 0, false, [ 'Is empty' ] ],
			[ '', false, [ 'Is empty' ] ],
			[ [ ], false, [ 'Is empty' ] ],
			[ 0.0, false, [ 'Is empty' ] ],
			[ null, false, [ 'Is empty' ] ],
			[ false, false, [ 'Is empty' ] ],
			[ true, true, [ ] ],
			[ 12.3, true, [ ] ],
		];
	}

	/**
	 * @dataProvider isArrayProvider
	 */
	public function testIsArray( $value, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isArray( $value, 'Not an array' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isArrayProvider()
	{
		return [
			[ [ ], true, [ ] ],
			[ [ 'Unit', 'Test' ], true, [ ] ],
			[ [ 'Unit', [ 'Test' ] ], true, [ ] ],
			[ 'Unit,Test', false, [ 'Not an array' ] ],
			[ new \stdClass(), false, [ 'Not an array' ] ],
			[ 1, false, [ 'Not an array' ] ],
			[ null, false, [ 'Not an array' ] ],
			[ false, false, [ 'Not an array' ] ],
			[ true, false, [ 'Not an array' ] ],
			[ 0, false, [ 'Not an array' ] ],
		];
	}

	/**
	 * @dataProvider isIntProvider
	 */
	public function testIsInt( $value, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isInt( $value, 'Not an int' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isIntProvider()
	{
		return [
			[ false, false, [ 'Not an int' ] ],
			[ true, false, [ 'Not an int' ] ],
			[ null, false, [ 'Not an int' ] ],
			[ 0, true, [ ] ],
			[ 1, true, [ ] ],
			[ -1, true, [ ] ],
			[ '-1', true, [ ] ],
			[ '0', true, [ ] ],
			[ '1', true, [ ] ],
			[ '13232345546548785456464121515454', false, [ 'Not an int' ] ],
			[ 13232345546548785456464121515454, false, [ 'Not an int' ] ],
			[ '12.3', false, [ 'Not an int' ] ],
			[ 12.3, false, [ 'Not an int' ] ],
			[ new \stdClass(), false, [ 'Not an int' ] ],
			[ new ValueObjects\ObjectWithToStringMethod( '' ), false, [ 'Not an int' ] ],
			[ new ValueObjects\ObjectWithToStringMethod( '12345' ), true, [ ] ],
			[ new ValueObjects\ObjectWithoutToStringMethod( '12345' ), false, [ 'Not an int' ] ],
		];
	}

	/**
	 * @dataProvider isIntInRangeProvider
	 */
	public function testIsIntInRange( $value, array $list, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isIntInRange( $value, $list, 'Not in range' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isIntInRangeProvider()
	{
		return [
			[ 0, range( -5, +5 ), true, [ ] ],
			[ 5, range( -5, +5 ), true, [ ] ],
			[ -5, range( -5, +5 ), true, [ ] ],
			[ -6, range( -5, +5 ), false, [ 'Not in range' ] ],
			[ 6, range( -5, +5 ), false, [ 'Not in range' ] ],
			[ '0', range( -5, +5 ), true, [ ] ],
			[ '5', range( -5, +5 ), true, [ ] ],
			[ '-5', range( -5, +5 ), true, [ ] ],
			[ '-6', range( -5, +5 ), false, [ 'Not in range' ] ],
			[ '6', range( -5, +5 ), false, [ 'Not in range' ] ],
			[ false, range( -5, +5 ), false, [ 'Not in range' ] ],
			[ true, range( -5, +5 ), false, [ 'Not in range' ] ],
			[ null, range( -5, +5 ), false, [ 'Not in range' ] ],
			[ new \stdClass(), range( -5, +5 ), false, [ 'Not in range' ] ],
			[ new ValueObjects\ObjectWithoutToStringMethod( '5' ), range( -5, +5 ), false, [ 'Not in range' ] ],
			[ new ValueObjects\ObjectWithToStringMethod( '3' ), range( -5, +5 ), true, [ ] ],
		];
	}

	/**
	 * @dataProvider isOneStringOfProvider
	 */
	public function testIsOneStringOf( $value, array $list, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isOneStringOf( $value, $list, 'Not a string of' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isOneStringOfProvider()
	{
		return [
			[ '', [ 'Yes', '', 'No' ], true, [ ] ],
			[ 'Yes', [ 'Yes', '', 'No' ], true, [ ] ],
			[ 'No', [ 'Yes', '', 'No' ], true, [ ] ],
			[ 0, [ 'Yes', '', 'No' ], false, [ 'Not a string of' ] ],
			[ null, [ 'Yes', '', 'No' ], false, [ 'Not a string of' ] ],
			[ false, [ 'Yes', '', 'No' ], false, [ 'Not a string of' ] ],
			[ true, [ 'Yes', '', 'No' ], false, [ 'Not a string of' ] ],
			[ new \stdClass(), [ 'Yes', '', 'No' ], false, [ 'Not a string of' ] ],
			[
				new ValueObjects\ObjectWithoutToStringMethod( 'Yes' ), [ 'Yes', '', 'No' ], false, [ 'Not a string of' ]
			],
			[ new ValueObjects\ObjectWithToStringMethod( 'Yes' ), [ 'Yes', '', 'No' ], true, [ ] ],
		];
	}

	/**
	 * @dataProvider isSubsetOfProvider
	 */
	public function testIsSubsetOf( $values, array $list, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isSubsetOf( $values, $list, 'Not a subset' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isSubsetOfProvider()
	{
		return [
			[ [ '' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'Yes' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'No' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'No', '', 'Yes' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'No', 'Yes' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'No', '' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'Yes', '' ], [ 'Yes', '', 'No' ], true, [ ] ],
			[ [ 'Unit', 'Test' ], [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ [ ], [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ [ false, true, 0 ], [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ 'Yes', [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ new \stdClass(), [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ null, [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ false, [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
			[ true, [ 'Yes', '', 'No' ], false, [ 'Not a subset' ] ],
		];
	}

	/**
	 * @dataProvider isUuidProvider
	 */
	public function testIsUuid( $value, $expected_bool, $expected_message )
	{
		$validator = new FluidValidator();
		$validator->isUuid( $value, 'Not a uuid' );

		$this->assertSame( $expected_bool, $validator->getBoolResult() );
		$this->assertEquals( $expected_message, $validator->getMessages() );
	}

	public function isUuidProvider()
	{
		return [
			[ '00000000-0000-0000-0000-000000000000', true, [ ] ],
			[ '01a2b3c4-D5F6-7a8b-9c0D-1E2f3a4B5c6D', true, [ ] ],
			[ 'AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEEE', true, [ ] ],
			[ '12345678-1234-5678-9101-121314151617', true, [ ] ],
			[ new ValueObjects\ObjectWithToStringMethod( '12345678-1234-5678-9101-121314151617' ), true, [ ] ],
			[
				new ValueObjects\ObjectWithoutToStringMethod( '12345678-1234-5678-9101-121314151617' ), false,
				[ 'Not a uuid' ]
			],
			[ 'GGGGGGGG-HHHH-IIII-JJJJ-KKKKKKKKKKKK', false, [ 'Not a uuid' ] ],
			[ 0, false, [ 'Not a uuid' ] ],
			[ 123, false, [ 'Not a uuid' ] ],
			[ '0', false, [ 'Not a uuid' ] ],
			[ false, false, [ 'Not a uuid' ] ],
			[ true, false, [ 'Not a uuid' ] ],
			[ null, false, [ 'Not a uuid' ] ],
			[ 12.3, false, [ 'Not a uuid' ] ],
		];
	}
}
