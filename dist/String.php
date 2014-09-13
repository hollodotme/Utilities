<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities;

/**
 * Class String
 *
 * @package hollodotme\Utilities
 */
abstract class String
{

	const CANONICAL_SEPARATOR = '.';

	public static function trim( $string, $additional_chars )
	{
		return trim( $string, "\t\n\r\0\x0B{$additional_chars}" );
	}

	public static function ltrim( $string, $additional_chars )
	{
		return ltrim( $string, "\t\n\r\0\x0B{$additional_chars}" );
	}

	public static function rtrim( $string, $additional_chars )
	{
		return rtrim( $string, "\t\n\r\0\x0B{$additional_chars}" );
	}

	public static function toCanonical( $string, $separator )
	{
		$string = self::trim( $string, $separator );

		return str_replace( $separator, self::CANONICAL_SEPARATOR, $string );
	}

	public static function fromCanonical( $string, $glue )
	{
		$string = self::trim( $string, self::CANONICAL_SEPARATOR );

		return str_replace( self::CANONICAL_SEPARATOR, $glue, $string );
	}
}
