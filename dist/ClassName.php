<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities;

use hollodotme\Utilities\Exceptions\ArgumentIsNotAValidClassName;

/**
 * Class ClassName
 *
 * @package hollodotme\Utilities
 */
class ClassName
{

	/** @var String */
	private $class_name;

	/**
	 * @param string $class_name
	 *
	 * @throws ArgumentIsNotAValidClassName
	 */
	public function __construct( $class_name )
	{
		$this->class_name = new Str( $class_name );

		$this->guardIsValidClassName();
	}

	/**
	 * @throws ArgumentIsNotAValidClassName
	 */
	private function guardIsValidClassName()
	{
		$this->class_name->trimLeft( '\\' );
		$class_name_parts = explode( '\\', $this->class_name );

		if ( empty($class_name_parts) )
		{
			throw new ArgumentIsNotAValidClassName( (string)$this->class_name );
		}
		else
		{
			foreach ( $class_name_parts as $class_name_part )
			{
				if ( $this->isInvalidClassNamePart( $class_name_part ) )
				{
					throw new ArgumentIsNotAValidClassName( (string)$this->class_name );
				}
			}
		}
	}

	/**
	 * @param string $class_name_part
	 *
	 * @return bool
	 */
	private function isInvalidClassNamePart( $class_name_part )
	{
		if ( $class_name_part === '' )
		{
			return true;
		}
		elseif ( !preg_match( "/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $class_name_part ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param string $class_name
	 *
	 * @return bool
	 */
	public static function isValid( $class_name )
	{
		try
		{
			new self( $class_name );

			return true;
		}
		catch ( ArgumentIsNotAValidClassName $e )
		{
			return false;
		}
	}
}
