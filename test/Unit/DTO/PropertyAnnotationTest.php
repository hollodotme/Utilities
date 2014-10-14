<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities\Test\Unit\DTO;

use hollodotme\Utilities\PropertyAnnotation;

class PropertyAnnotationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider docCommentStringProvider
	 */
	public function testCanBeConstructedFromDocCommentString(
		$doc_comment_string, $expected_name, $expected_type,
		$expected_accessability, $expected_comment
	)
	{
		$annotation = PropertyAnnotation::fromDocCommentString( $doc_comment_string );

		$this->assertInstanceOf( PropertyAnnotation::class, $annotation );
		$this->assertEquals( $expected_name, $annotation->getName() );
		$this->assertEquals( $expected_type, $annotation->getType() );
		$this->assertEquals( $expected_accessability, $annotation->getAccessability() );
		$this->assertEquals( $expected_comment, $annotation->getComment() );
	}

	public function docCommentStringProvider()
	{
		return [
			[
				'@property bool $yes_no',
				'yes_no',
				'bool',
				(PropertyAnnotation::ACCESS_READ | PropertyAnnotation::ACCESS_WRITE),
				''
			],
			[
				'@property int $integer An Integer',
				'integer',
				'int',
				(PropertyAnnotation::ACCESS_READ | PropertyAnnotation::ACCESS_WRITE),
				'An Integer'
			],
			[
				'@property  string   $string    A String',
				'string',
				'string',
				(PropertyAnnotation::ACCESS_READ | PropertyAnnotation::ACCESS_WRITE),
				'A String'
			],
			[
				'@property-read  \\stdClass   $stdclass    An Object',
				'stdclass',
				'\\stdClass',
				PropertyAnnotation::ACCESS_READ,
				'An Object'
			],
			[
				'@property-write  \\std\\Class   $stdclass    An Object',
				'stdclass',
				'\\std\\Class',
				PropertyAnnotation::ACCESS_WRITE,
				'An Object'
			],
			[
				'@PROPERTY-WRITE  \\std\\Class   $stdclass    An Object',
				'stdclass',
				'\\std\\Class',
				PropertyAnnotation::ACCESS_WRITE,
				'An Object'
			],
			[
				'@PROPERTY-READ  \\std\\Class   $stdclass    An Object',
				'stdclass',
				'\\std\\Class',
				PropertyAnnotation::ACCESS_READ,
				'An Object'
			],
		];
	}

	/**
	 * @dataProvider invalidDocCommentStringProvider
	 * @expectedException \hollodotme\Utilities\Exceptions\BadPropertyAnnotationDetected
	 */
	public function testConstructionFailsOnInvalidDocCommentStrings( $doc_comment_string )
	{
		PropertyAnnotation::fromDocCommentString( $doc_comment_string );
	}

	public function invalidDocCommentStringProvider()
	{
		return [
			[ '@property $just_a_var And Comment' ],
			[ '@property-readonly string $just_a_var And Comment' ],
			[ '@property-writable int $integer And Comment' ],
			[ '@property $integer int And Comment' ],
		];
	}

	/**
	 * @dataProvider readWriteOnlyProvider
	 */
	public function testReadAndWriteOnly( $doc_comment_string, $expected_readonly, $expected_writeonly )
	{
		$annotation = PropertyAnnotation::fromDocCommentString( $doc_comment_string );

		$this->assertSame( $expected_readonly, $annotation->isReadOnly() );
		$this->assertSame( $expected_writeonly, $annotation->isWriteOnly() );
	}

	public function readWriteOnlyProvider()
	{
		return [
			[ '@property int $read_write', false, false ],
			[ '@property-read string $readonly', true, false ],
			[ '@property-write mixed $writeonly', false, true ],
		];
	}

	/**
	 * @dataProvider readableWritableProvider
	 */
	public function testIsReadableIsWritable( $doc_comment_string, $expected_readable, $expected_writable )
	{
		$annotation = PropertyAnnotation::fromDocCommentString( $doc_comment_string );

		$this->assertSame( $expected_readable, $annotation->isReadable() );
		$this->assertSame( $expected_writable, $annotation->isWritable() );
	}

	public function readableWritableProvider()
	{
		return [
			[ '@property int $read_write', true, true ],
			[ '@property-read string $readonly', true, false ],
			[ '@property-write mixed $writeonly', false, true ],
		];
	}
}
