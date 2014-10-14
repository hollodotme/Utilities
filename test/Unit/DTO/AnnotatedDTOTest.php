<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities\Test\Unit\DTO;

use hollodotme\Utilities\Test\Unit\TestDTORead;
use hollodotme\Utilities\Test\Unit\TestDTOReadWrite;
use hollodotme\Utilities\Test\Unit\TestDTOWrite;

require_once __DIR__ . '/../Fixures/TestDTOReadWrite.php';
require_once __DIR__ . '/../Fixures/TestDTORead.php';
require_once __DIR__ . '/../Fixures/TestDTOWrite.php';

class AnnotatedDTOTest extends \PHPUnit_Framework_TestCase
{
	public function testPropertiesWithoutAccessabilityAreReadableAndWritable()
	{
		$dto = new TestDTOReadWrite();

		$dto->unit_member = 'Unit';
		$dto->test_member = 'Test';

		$this->assertEquals( 'Unit', $dto->unit_member );
		$this->assertEquals( 'Test', $dto->test_member );
	}

	/**
	 * @expectedException \hollodotme\Utilities\Exceptions\PropertyIsNotWritable
	 */
	public function testWritingUndefinedPropertiesFails()
	{
		$dto = new TestDTOReadWrite();

		$dto->undefined_member = 'Unit-Test';
	}

	/**
	 * @expectedException \hollodotme\Utilities\Exceptions\PropertyIsNotReadable
	 */
	public function testReadingUndefinedPropertiesFails()
	{
		$dto = new TestDTOReadWrite();

		$read_test = $dto->undefined_member;
	}

	public function testPropertiesWithoutAccessabilityCanBeUnset()
	{
		$dto              = new TestDTOReadWrite();
		$dto->test_member = 'Test';

		unset($dto->test_member);

		$this->assertNull( $dto->test_member );
	}

	/**
	 * @expectedException \hollodotme\Utilities\Exceptions\PropertyIsNotWritable
	 */
	public function testWritingReadOnlyPropertiesFails()
	{
		$dto = new TestDTORead( 'Unit-Test' );

		$this->assertEquals( 'Unit-Test', $dto->test_member );

		$dto->test_member = "Should not work!";
	}

	public function testCanBeRepresentedAsAssocArray()
	{
		$dto              = new TestDTOReadWrite();
		$dto->test_member = 'Test';
		$dto->unit_member = 'Unit';

		$this->assertSame( [ 'test_member' => 'Test', 'unit_member' => 'Unit' ], $dto->toArray() );
	}

	public function testCanBeSerializedAndUnserialized()
	{
		$dto              = new TestDTOReadWrite();
		$dto->test_member = 'Test';
		$dto->unit_member = 'Unit';

		$serialized   = serialize( $dto );
		$unserialized = unserialize( $serialized );

		$this->assertInternalType( 'string', $serialized );
		$this->assertInstanceOf( TestDTOReadWrite::class, $unserialized );
		$this->assertEquals( $dto->toArray(), $unserialized->toArray() );
	}

	public function testCanBeJsonSerialized()
	{
		$dto              = new TestDTOReadWrite();
		$dto->test_member = 'Test';
		$dto->unit_member = 'Unit';

		$json_encoded = json_encode( $dto );
		$expected     = '{"test_member": "Test", "unit_member": "Unit"}';

		$this->assertJson( $json_encoded );
		$this->assertJsonStringEqualsJsonString( $expected, $json_encoded );
	}

	/**
	 * @expectedException \hollodotme\Utilities\Exceptions\PropertyIsNotReadable
	 */
	public function testReadingWriteOnlyPropertiesFails()
	{
		$dto              = new TestDTOWrite();
		$dto->test_member = 'Unit-Test';

		$this->assertEquals( [ 'test_member' => 'Unit-Test' ], $dto->toArray() );

		$read_test = $dto->test_member;
	}

	public function testWriteOnlyPropertiesCanBeUnset()
	{
		$dto              = new TestDTOWrite();
		$dto->test_member = 'Unit-Test';

		$this->assertEquals( [ 'test_member' => 'Unit-Test' ], $dto->toArray() );

		unset($dto->test_member);

		$this->assertEquals( [ ], $dto->toArray() );
	}
}
