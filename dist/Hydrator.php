<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities;

/**
 * Class Hydrator
 *
 * @package hollodotme\Utilities
 */
class Hydrator
{

	/** @var \ReflectionClass */
	private $reflection_class;

	/**
	 * @param string $class_name
	 */
	public function __construct( $class_name )
	{
		$this->guardClassNameIsValid( $class_name );

		$this->reflection_class = new \ReflectionClass( $class_name );
	}

	/**
	 * @param string $class_name
	 *
	 * @throws \InvalidArgumentException
	 */
	private function guardClassNameIsValid( $class_name )
	{
		if ( !ClassName::isValid( $class_name ) )
		{
			throw new \InvalidArgumentException( "Class name is invalid." );
		}
	}

	/**
	 * @param array $record
	 * @param array $ctor_args
	 *
	 * @return object
	 */
	public function fromRecord( array $record, array $ctor_args = [ ] )
	{
		$object = $this->getObjectWithoutCallingConstructor();

		foreach ( $this->reflection_class->getProperties() as $property )
		{
			$this->setPropertyValueIfPossible( $object, $property, $record );
		}

		$this->invokeConstructorIfPossible( $object, $ctor_args );

		return $object;
	}

	/**
	 * @return object
	 */
	private function getObjectWithoutCallingConstructor()
	{
		return $this->reflection_class->newInstanceWithoutConstructor();
	}

	/**
	 * @param object              $object
	 * @param \ReflectionProperty $property
	 * @param array               $record
	 */
	private function setPropertyValueIfPossible( $object, \ReflectionProperty $property, array $record )
	{
		$member = $property->getName();

		if ( isset($record[ $member ]) )
		{
			$property->setAccessible( true );
			$property->setValue( $object, $record[ $member ] );
		}
	}

	/**
	 * @param object $object
	 * @param array  $ctor_args
	 */
	private function invokeConstructorIfPossible( $object, array $ctor_args )
	{
		$constructor = $this->reflection_class->getConstructor();
		if ( $constructor instanceof \ReflectionMethod )
		{
			$constructor->invokeArgs( $object, $ctor_args );
		}
	}
}