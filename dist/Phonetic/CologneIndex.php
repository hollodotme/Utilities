<?php

namespace hollodotme\Utilities\Phonetic;

/**
 * The Cologne Phonetic Index
 *
 * @author hollodotme
 */
class CologneIndex
{
	/**
	 * @param string $string
	 * @param string $separator
	 *
	 * @return string
	 */
	public static function get( $string, $separator = '' )
	{
		return join( $separator, self::getWords( $string ) );
	}

	/**
	 * @param string $string
	 *
	 * @return array
	 */
	public static function getWords( $string )
	{
		$string = self::replaceChars( $string );
		$words  = preg_split( "#[\W_]#i", $string, -1, PREG_SPLIT_NO_EMPTY );

		array_map( array( __CLASS__, 'getIndex' ), $words );
		$words = array_filter( $words, 'trim' );

		return $words;
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	private static function replaceChars( $string )
	{
		return str_replace(
			array( 'ç', 'v', 'w', 'j', 'y', 'ph', 'ä', 'ö', 'ü', 'ß', 'é', 'è', 'ê', 'à', 'á', 'â', 'ë' ),
			array( 'c', 'f', 'f', 'i', 'i', 'f', 'a', 'o', 'u', 'ss', 'e', 'e', 'e', 'a', 'a', 'a', 'e' ),
			$string
		);
	}

	/**
	 * @param string $word
	 *
	 * @return string
	 */
	public static function getIndex( $word )
	{
		$code = '';
		$word = strtolower( $word );

		if ( strlen( $word ) < 1 )
		{
			return '';
		}

		$word = self::replaceChars( $word );
		$word = preg_replace( '#[^a-z]#i', '', $word );

		$wordlen = strlen( $word );
		$char    = str_split( $word );

		if ( $char[0] == 'c' )
		{
			switch ( $char[1] )
			{
				case 'a':
				case 'h':
				case 'k':
				case 'l':
				case 'o':
				case 'q':
				case 'r':
				case 'u':
				case 'x':
					$code = '4';
					break;
				default:
					$code = '8';
					break;
			}
			$x = 1;
		}
		else
		{
			$x = 0;
		}

		for ( ; $x < $wordlen; $x++ )
		{
			switch ( $char[ $x ] )
			{
				case 'a':
				case 'e':
				case 'i':
				case 'o':
				case 'u':
					$code .= '0';
					break;
				case 'b':
				case 'p':
					$code .= '1';
					break;
				case 'd':
				case 't':
				{
					if ( $x + 1 < $wordlen )
					{
						switch ( $char[ $x + 1 ] )
						{
							case 'c':
							case 's':
							case 'z':
								$code .= '8';
								break;
							default:
								$code .= '2';
								break;
						}
					}
					else
					{
						$code .= '2';
					}
					break;
				}
				case 'f':
					$code .= '3';
					break;
				case 'g':
				case 'k':
				case 'q':
					$code .= '4';
					break;
				case 'c':
				{
					if ( $x + 1 < $wordlen )
					{
						switch ( $char[ $x + 1 ] )
						{
							case 'a':
							case 'h':
							case 'k':
							case 'o':
							case 'q':
							case 'u':
							case 'x':
								switch ( $char[ $x - 1 ] )
								{
									case 's':
									case 'z':
										$code .= '8';
										break;
									default:
										$code .= '4';
								}
								break;
							default:
								$code .= '8';
								break;
						}
					}
					else
					{
						$code .= '8';
					}
					break;
				}
				case 'x':
				{
					if ( $x > 0 )
					{
						switch ( $char[ $x - 1 ] )
						{
							case 'c':
							case 'k':
							case 'q':
								$code .= '8';
								break;
							default:
								$code .= '48';
						}
					}
					else
					{
						$code .= '48';
					}
					break;
				}
				case 'l':
					$code .= '5';
					break;
				case 'm':
				case 'n':
					$code .= '6';
					break;
				case 'r':
					$code .= '7';
					break;
				case 's':
				case 'z':
					$code .= '8';
					break;
			}
		}

		$codelen      = strlen( $code );
		$num          = str_split( $code );
		$phoneticcode = $num[0];

		for ( $x = 1; $x < $codelen; $x++ )
		{
			if ( $num[ $x ] != '0' )
			{
				$phoneticcode .= $num[ $x ];
			}
		}

		return strval( preg_replace( '#(.)\\1+#', '\\1', $phoneticcode ) );
	}
}
