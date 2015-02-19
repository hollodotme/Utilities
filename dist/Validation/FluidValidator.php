<?php
/**
 * @author h.woltersdorf
 *
 */

namespace hollodotme\Utilities\Validation;

use hollodotme\Utilities\Validation\Exceptions\FluidValidatorCheckMethodIsNotCallable;

/**
 * Class FluidValidator
 *
 * @package hollodotme\Utilities\Validation
 *
 * METHODSTART
 * @method FluidValidator isString($value, $message)
 * @method FluidValidator isStringOrNull($value, $message)
 * @method FluidValidator isNonEmptyString($value, $message)
 * @method FluidValidator isNonEmptyStringOrNull($value, $message)
 * @method FluidValidator isNotEmpty($value, $message)
 * @method FluidValidator isNotEmptyOrNull($value, $message)
 * @method FluidValidator isArray($value, $message)
 * @method FluidValidator isArrayOrNull($value, $message)
 * @method FluidValidator isInt($value, $message)
 * @method FluidValidator isIntOrNull($value, $message)
 * @method FluidValidator isIntInRange($value, array $range, $message)
 * @method FluidValidator isIntInRangeOrNull($value, array $range, $message)
 * @method FluidValidator isOneStringOf($value, array $list, $message)
 * @method FluidValidator isOneStringOfOrNull($value, array $list, $message)
 * @method FluidValidator isSubsetOf($values, array $list, $message)
 * @method FluidValidator isSubsetOfOrNull($values, array $list, $message)
 * @method FluidValidator isUuid($value, $message)
 * @method FluidValidator isUuidOrNull($value, $message)
 * @method FluidValidator isEqual($value1, $value2, $message)
 * @method FluidValidator isNotEqual($value1, $value2, $message)
 * @method FluidValidator isSame($value1, $value2, $message)
 * @method FluidValidator isNotSame($value1, $value2, $message)
 * @method FluidValidator isNull($value, $message)
 * @method FluidValidator isNotNull($value, $message)
 * @method FluidValidator matchesRegex($value, $regex, $message)
 * @method FluidValidator matchesRegexOrNull($value, $regex, $message)
 * @method FluidValidator hasLength($value, $length, $message)
 * @method FluidValidator hasLengthOrNull($value, $length, $message)
 * @method FluidValidator hasMinLength($value, $min_length, $message)
 * @method FluidValidator hasMinLengthOrNull($value, $min_length, $message)
 * @method FluidValidator hasMaxLength($value, $max_length, $message)
 * @method FluidValidator hasMaxLengthOrNull($value, $max_length, $message)
 * @method FluidValidator counts($values, $count, $message)
 * @method FluidValidator countsOrNull($values, $count, $message)
 * @method FluidValidator isEmail($value, $message)
 * @method FluidValidator isUrl($value, $message)
 * @method FluidValidator isJson($value, $message)
 * @method FluidValidator hasKey($values, $key, $message)
 * @method FluidValidator hasKeyOrNull($values, $key, $message)
 * METHODEND
 */
class FluidValidator
{

	const MODE_CHECK_ALL          = 1;

	const MODE_STOP_ON_FIRST_FAIL = 2;

	/** @var bool */
	protected $bool_result;

	/** @var array */
	protected $messages;

	/** @var int */
	private $mode;

	/**
	 * @param int $mode
	 */
	public function __construct( $mode = self::MODE_CHECK_ALL )
	{
		$this->mode = $mode;
		$this->reset();
	}

	public function reset()
	{
		$this->bool_result = true;
		$this->messages    = [ ];
	}

	/**
	 * @return boolean
	 */
	public function getBoolResult()
	{
		return $this->bool_result;
	}

	/**
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @throws FluidValidatorCheckMethodIsNotCallable
	 * @return $this
	 */
	public function __call( $name, array $arguments )
	{
		$or_null = (substr( $name, -6 ) == 'OrNull');

		$check_method = 'check' . ucfirst( preg_replace( "#OrNull$#", '', $name ) );
		$this->guardCheckMethodIsCallable( $check_method );

		if ( $this->mode == self::MODE_STOP_ON_FIRST_FAIL && !$this->bool_result )
		{
			return $this;
		}
		else
		{
			if ( $or_null && is_null( $this->getValue( $arguments[0] ) ) )
			{
				return $this;
			}
			else
			{
				$message = array_pop( $arguments );

				$check_result = call_user_func_array( [ $this, $check_method ], $arguments );

				if ( !$check_result )
				{
					$this->bool_result = false;
					$this->messages[]  = $message;
				}
			}

			return $this;
		}
	}

	/**
	 * @param string $check_method
	 *
	 * @throws FluidValidatorCheckMethodIsNotCallable
	 */
	private function guardCheckMethodIsCallable( $check_method )
	{
		$check_method = trim( $check_method );

		if ( $check_method == 'check' || !method_exists( $this, $check_method ) )
		{
			throw new FluidValidatorCheckMethodIsNotCallable( $check_method );
		}
	}

	/**
	 * @param mixed $var
	 *
	 * @return mixed
	 */
	protected function getValue( $var )
	{
		return $var;
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsString( $value )
	{
		return ( new StringValidator() )->isString( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsNonEmptyString( $value )
	{
		return ( new StringValidator() )->isNonEmptyString( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsNotEmpty( $value )
	{
		return !empty($this->getValue( $value ));
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsArray( $value )
	{
		return is_array( $this->getValue( $value ) );
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	protected function checkIsInt( $value )
	{
		return ( new StringValidator() )->isInt( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value
	 * @param array $range
	 *
	 * @return bool
	 */
	protected function checkIsIntInRange( $value, array $range )
	{
		if ( $this->checkIsInt( $value ) )
		{
			$val = intval( strval( $this->getValue( $value ) ) );

			return in_array( $val, $range, true );
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 * @param array $list
	 *
	 * @return bool
	 */
	protected function checkIsOneStringOf( $value, array $list )
	{
		if ( $this->checkIsString( $value ) )
		{
			$val = strval( $this->getValue( $value ) );

			return in_array( $val, $list, true );
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $values
	 * @param array $list
	 *
	 * @return bool
	 */
	protected function checkIsSubsetOf( $values, array $list )
	{
		if ( $this->checkIsArray( $values ) )
		{
			$vals = $this->getValue( $values );

			return (count( $vals ) > 0 && count( array_diff( $vals, $list ) ) == 0);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsUuid( $value )
	{
		return ( new StringValidator() )->isUuid( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value1
	 * @param mixed $value2
	 *
	 * @return bool
	 */
	protected function checkIsEqual( $value1, $value2 )
	{
		return ($this->getValue( $value1 ) == $value2);
	}

	/**
	 * @param mixed $value1
	 * @param mixed $value2
	 *
	 * @return bool
	 */
	protected function checkIsNotEqual( $value1, $value2 )
	{
		return ($this->getValue( $value1 ) != $value2);
	}

	/**
	 * @param mixed $value1
	 * @param mixed $value2
	 *
	 * @return bool
	 */
	protected function checkIsSame( $value1, $value2 )
	{
		return ($this->getValue( $value1 ) === $value2);
	}

	/**
	 * @param mixed $value1
	 * @param mixed $value2
	 *
	 * @return bool
	 */
	protected function checkIsNotSame( $value1, $value2 )
	{
		return ($this->getValue( $value1 ) !== $value2);
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsNull( $value )
	{
		return is_null( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsNotNull( $value )
	{
		return !is_null( $this->getValue( $value ) );
	}

	/**
	 * @param mixed  $value
	 * @param string $regex
	 *
	 * @return bool
	 */
	protected function checkMatchesRegex( $value, $regex )
	{
		if ( $this->checkIsString( $value ) )
		{
			$val = strval( $this->getValue( $value ) );

			return boolval( preg_match( $regex, $val ) );
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 * @param int   $length
	 *
	 * @return bool
	 */
	protected function checkHasLength( $value, $length )
	{
		if ( $this->checkIsString( $value ) )
		{
			$val = strval( $this->getValue( $value ) );

			return (strlen( $val ) == $length);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 * @param int   $min_length
	 *
	 * @return bool
	 */
	protected function checkHasMinLength( $value, $min_length )
	{
		if ( $this->checkIsString( $value ) )
		{
			$val = strval( $this->getValue( $value ) );

			return (strlen( $val ) >= $min_length);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 * @param int   $max_length
	 *
	 * @return bool
	 */
	protected function checkHasMaxLength( $value, $max_length )
	{
		if ( $this->checkIsString( $value ) )
		{
			$val = strval( $this->getValue( $value ) );

			return (strlen( $val ) <= $max_length);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 * @param int   $count
	 *
	 * @return bool
	 */
	protected function checkCounts( $value, $count )
	{
		if ( $this->checkIsArray( $value ) )
		{
			return (count( $this->getValue( $value ) ) == $count);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsEmail( $value )
	{
		return ( new StringValidator() )->isEmail( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsUrl( $value )
	{
		return ( new StringValidator() )->isUrl( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function checkIsJson( $value )
	{
		return ( new StringValidator() )->isJson( $this->getValue( $value ) );
	}

	/**
	 * @param mixed $values
	 * @param mixed $key
	 *
	 * @return bool
	 */
	protected function checkHasKey( $values, $key )
	{
		if ( $this->checkIsArray( $values ) )
		{
			return array_key_exists( $key, $this->getValue( $values ) );
		}
		else
		{
			return false;
		}
	}
}
