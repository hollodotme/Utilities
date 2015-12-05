<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities;

use hollodotme\Utilities\Exceptions\ArgumentIsNotRepresentableAsString;

/**
 * Class Str
 *
 * @package hollodotme\Utilities
 */
class Str
{

	const CANONICAL_SEPARATOR = '.';

	/** @var string */
	private $string = '';

	/**
	 * @param string $string
	 */
	public function __construct( $string = '' )
	{
		$this->guardConvertableToString( $string );

		$this->string = strval( $string );
	}

	/**
	 * @param string $additional_chars
	 *
	 * @return $this
	 */
	public function trim( $additional_chars )
	{
		$this->guardConvertableToString( $additional_chars );

		$this->string = trim( $this->string, "\t\n\r\0\x0B{$additional_chars}" );

		return $this;
	}

	/**
	 * @param string $additional_chars
	 *
	 * @return $this
	 */
	public function trimLeft( $additional_chars )
	{
		$this->guardConvertableToString( $additional_chars );

		$this->string = ltrim( $this->string, "\t\n\r\0\x0B{$additional_chars}" );

		return $this;
	}

	/**
	 * @param string $additional_chars
	 *
	 * @return $this
	 */
	public function trimRight( $additional_chars )
	{
		$this->guardConvertableToString( $additional_chars );

		$this->string = rtrim( $this->string, "\t\n\r\0\x0B{$additional_chars}" );

		return $this;
	}

	/**
	 * @param string $string
	 *
	 * @return $this
	 */
	public function removeDuplicatesOf( $string )
	{
		$this->guardConvertableToString( $string );

		$escaped_string = preg_quote( $string, '#' );
		$this->string = preg_replace( "#{$escaped_string}+#", $string, $this->string );

		return $this;
	}

	/**
	 * @param string $separator
	 *
	 * @return $this
	 */
	public function toCanonical( $separator )
	{
		$this->trim( $separator )
		     ->removeDuplicatesOf( $separator );

		$this->string = str_replace( $separator, self::CANONICAL_SEPARATOR, $this->string );

		return $this;
	}

	/**
	 * @param string $glue
	 *
	 * @return $this
	 */
	public function fromCanonical( $glue )
	{
		$this->guardConvertableToString( $glue );

		$this->trim( self::CANONICAL_SEPARATOR )
		     ->removeDuplicatesOf( self::CANONICAL_SEPARATOR );

		$this->string = str_replace( self::CANONICAL_SEPARATOR, $glue, $this->string );

		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->string;
	}

	/**
	 * @param mixed $argument
	 *
	 * @throws ArgumentIsNotRepresentableAsString
	 */
	private function guardConvertableToString( $argument )
	{
		if ( is_object( $argument ) )
		{
			if ( !is_callable( [ $argument, '__toString' ] ) )
			{
				throw new ArgumentIsNotRepresentableAsString( gettype( $argument ) );
			}
		}
		elseif ( !is_scalar( $argument ) )
		{
			throw new ArgumentIsNotRepresentableAsString( gettype( $argument ) );
		}
		elseif ( is_bool( $argument ) )
		{
			throw new ArgumentIsNotRepresentableAsString( gettype( $argument ) );
		}
	}

	/**
	 * @param mixed $string
	 *
	 * @return bool
	 */
	public static function isValid( $string )
	{
		try
		{
			new self( $string );

			return true;
		}
		catch ( ArgumentIsNotRepresentableAsString $e )
		{
			return false;
		}
	}

	/**
	 * @return array
	 */
	public function getWords()
	{
		$string = $this->replaceChars( $this->string );

		return preg_split( "#[\W_]#", $string, -1, PREG_SPLIT_NO_EMPTY );
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	private function replaceChars( $string )
	{
		return iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $string );
	}
}
