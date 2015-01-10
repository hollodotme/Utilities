<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Test\Unit\Hydrator;

use hollodotme\Utilities\Hydrator;

class HydratorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider invalidClassNameProvider
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructionFailsWithInvalidClassNames( $class_name )
	{
		new Hydrator( $class_name );
	}

	public function invalidClassNameProvider()
	{
		return [
			[ '0Class' ],
			[ '|Class' ],
			[ 'Class with whitespace' ],
		];
	}

	public function testCanHydrateAllMemberTypesFromRecord()
	{
		$record = [
			'unit'   => 'Hello',
			'test'   => 'Real',
			'string' => 'World',
		];

		$hydrator = new Hydrator( TestObject::class );

		/** @var TestObject $object */
		$object = $hydrator->fromRecord( $record );

		$this->assertEquals( 'Hello', $object->getUnit() );
		$this->assertEquals( 'Real', $object->getTest() );
		$this->assertEquals( 'World', $object->getString() );
	}

	public function testCanLeaveMemberNullWhenNotSetInRecord()
	{
		$record = [
			'unit' => 'Hello',
			'test' => 'Real',
		];

		$hydrator = new Hydrator( TestObject::class );

		/** @var TestObject $object */
		$object = $hydrator->fromRecord( $record );

		$this->assertEquals( 'Hello', $object->getUnit() );
		$this->assertEquals( 'Real', $object->getTest() );
		$this->assertNull( $object->getString() );
	}

	public function testRecordCanHaveMoreValuesThanMembersExist()
	{
		$record = [
			'unit'     => 'Hello',
			'test'     => 'Real',
			'string'   => 'World',
			'one_more' => 'This one does not exists',
		];

		$hydrator = new Hydrator( TestObject::class );

		/** @var TestObject $object */
		$object = $hydrator->fromRecord( $record );

		$this->assertEquals( 'Hello', $object->getUnit() );
		$this->assertEquals( 'Real', $object->getTest() );
		$this->assertEquals( 'World', $object->getString() );
	}

	public function testRecordCanBeEmpty()
	{
		$record = [ ];

		$hydrator = new Hydrator( TestObject::class );

		/** @var TestObject $object */
		$object = $hydrator->fromRecord( $record );

		$this->assertNull( $object->getUnit() );
		$this->assertNull( $object->getTest() );
		$this->assertNull( $object->getString() );
	}

	public function testConstructorIsCalledAfterHydration()
	{
		$record = [
			'unit'   => 'Hello',
			'test'   => 'Real',
			'string' => 'World',
		];

		$hydrator = new Hydrator( TestObjectWithConstructor::class );

		/** @var TestObjectWithConstructor $object */
		$object = $hydrator->fromRecord( $record, [ 'Override by ctor_args' ] );

		$this->assertEquals( 'Hello', $object->getUnit() );
		$this->assertEquals( 'Override by ctor', $object->getTest() );
		$this->assertEquals( 'Override by ctor_args', $object->getString() );
	}

	public function testCanHydrateMembersOfParentClasses()
	{
		$record = [
			'unit'      => 'Hello',
			'test'      => 'Real',
			'string'    => 'World',
			'extending' => 'In extending class',
		];

		$hydrator = new Hydrator( ExtendingTestObject::class );

		/** @var ExtendingTestObject $object */
		$object = $hydrator->fromRecord( $record );

		$this->assertEquals( 'Hello', $object->getUnit() );
		$this->assertEquals( 'Real', $object->getTest() );
		$this->assertEquals( 'World', $object->getString() );
		$this->assertEquals( 'In extending class', $object->getExtending() );
	}
}

class TestObject
{

	private   $unit;

	protected $test;

	public    $string;

	public function getUnit()
	{
		return $this->unit;
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getString()
	{
		return $this->string;
	}
}

class TestObjectWithConstructor
{

	private $unit;

	private $test;

	private $string;

	public function __construct( $string )
	{
		$this->test   = 'Override by ctor';
		$this->string = $string;
	}

	public function getUnit()
	{
		return $this->unit;
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getString()
	{
		return $this->string;
	}
}

class ExtendingTestObject extends TestObject
{

	private $extending;

	public function getExtending()
	{
		return $this->extending;
	}
}