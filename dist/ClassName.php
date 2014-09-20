<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities;

/**
 * Class ClassName
 *
 * @package hollodotme\Utilities
 */
abstract class ClassName
{
	/**
	 * @param mixed $class_name
	 *
	 * @return bool
	 */
	public static function isValid( $class_name )
	{
		$class_name_string = strval( $class_name );
		$trimmed_string = String::ltrim( $class_name_string, '\\' );
		$class_name_parts  = explode( '\\', $trimmed_string );

		if ( empty($class_name_parts) )
		{
			return false;
		}
		else
		{
			foreach ( $class_name_parts as $class_name_part )
			{
				if ( self::isInvalidClassNamePart( $class_name_part ) )
				{
					return false;
				}
			}

			return true;
		}
	}

	/**
	 * @param string $class_name_part
	 *
	 * @return bool
	 */
	private static function isInvalidClassNamePart( $class_name_part )
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
}
