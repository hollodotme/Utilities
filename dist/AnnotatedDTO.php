<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities;

use hollodotme\Utilities\Exceptions\PropertyIsNotReadable;
use hollodotme\Utilities\Exceptions\PropertyIsNotWritable;

/**
 * Class AnnotatedDTO
 *
 * @package hollodotme\Utilities
 */
abstract class AnnotatedDTO implements \Serializable, \JsonSerializable
{

	/** @var array */
	protected $data = [ ];

	/**
	 * @var PropertyAnnotation[]|null
	 */
	private $properties;

	/**
	 * @param string $name
	 *
	 * @return null|mixed
	 */
	public function __get( $name )
	{
		$this->guardPropertyIsDefinedAndReadable( $name );

		if ( $this->__isset( $name ) )
		{
			return $this->data[ $name ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function __set( $name, $value )
	{
		$this->guardPropertyIsDefinedAndWritable( $name );

		$this->data[ $name ] = $value;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name )
	{
		$this->guardPropertyIsDefinedAndReadable( $name );

		return isset($this->data[ $name ]);
	}

	/**
	 * @param string $name
	 */
	public function __unset( $name )
	{
		$this->guardPropertyIsDefinedAndWritable( $name );

		unset($this->data[ $name ]);
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize( $this->data );
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize( $serialized )
	{
		$this->data = unserialize( $serialized );
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->data;
	}

	public function toArray()
	{
		return $this->data;
	}

	/**
	 * @param string $name
	 *
	 * @throws PropertyIsNotReadable
	 */
	private function guardPropertyIsDefinedAndReadable( $name )
	{
		if ( !$this->isPropertyDefinedAndReadable( $name ) )
		{
			throw new PropertyIsNotReadable( $name );
		}
	}

	/**
	 * @param string $name
	 *
	 * @throws PropertyIsNotWritable
	 */
	private function guardPropertyIsDefinedAndWritable( $name )
	{
		if ( !$this->isPropertyDefinedAndWritable( $name ) )
		{
			throw new PropertyIsNotWritable( $name );
		}
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	private function isPropertyDefinedAndReadable( $name )
	{
		$property = $this->getPropertyWithName( $name );

		if ( is_null( $property ) )
		{
			return false;
		}
		else
		{
			return $property->isReadable();
		}
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	private function isPropertyDefinedAndWritable( $name )
	{
		$property = $this->getPropertyWithName( $name );

		if ( is_null( $property ) )
		{
			return false;
		}
		else
		{
			return $property->isWritable();
		}
	}

	/**
	 * @return PropertyAnnotation[]
	 */
	private function getProperties()
	{
		if ( is_null( $this->properties ) )
		{
			$this->properties = [ ];

			$reflection_class = new \ReflectionClass( $this );
			$doc_comment      = $reflection_class->getDocComment();

			$lines = explode( PHP_EOL, $doc_comment );
			foreach ( $lines as $doc_comment_string )
			{
				$property_annotation = $this->getPropertyAnnotation( $doc_comment_string );
				if ( $property_annotation instanceof PropertyAnnotation )
				{
					$this->properties[] = $property_annotation;
				}
			}
		}

		return $this->properties;
	}

	/**
	 * @param string $name
	 *
	 * @return PropertyAnnotation|null
	 */
	private function getPropertyWithName( $name )
	{
		$property     = null;
		$properties   = $this->getProperties();
		$cur_property = reset( $properties );

		while ( is_null( $property ) && $cur_property !== false )
		{
			/** @var PropertyAnnotation $cur_property */
			if ( $cur_property->getName() == $name )
			{
				$property = $cur_property;
			}
			else
			{
				$cur_property = next( $properties );
			}
		}

		return $property;
	}

	/**
	 * @param string $doc_comment_string
	 *
	 * @return PropertyAnnotation|null
	 */
	private function getPropertyAnnotation( $doc_comment_string )
	{
		return PropertyAnnotation::fromDocCommentString( $doc_comment_string );
	}
}
