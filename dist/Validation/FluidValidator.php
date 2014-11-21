<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Validation;

use hollodotme\Utilities\String;

/**
 * Class FluidValidator
 *
 * @package hollodotme\Utilities\Validation
 */
class FluidValidator
{

	/** @var bool */
	protected $bool_result;

	/** @var array */
	protected $messages;

	public function __construct()
	{
		$this->reset();
	}

	/**
	 * @return FluidValidator
	 */
	public static function check()
	{
		return new self();
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
	 * @param mixed       $var
	 * @param null|string $message
	 *
	 * @return $this
	 */
	public function notEmpty( $var, $message = null )
	{
		if ( empty($this->getValue( $var )) )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}

		return $this;
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
	 * @param string $message
	 */
	protected function addMessageIfSet( $message )
	{
		if ( !empty($message) )
		{
			$this->messages[] = $message;
		}
	}

	/**
	 * @param mixed       $var
	 * @param null|string $message
	 *
	 * @return $this
	 */
	public function notEmptyString( $var, $message = null )
	{
		if ( !$this->isString( $var )->getBoolResult() )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}
		elseif ( trim( (string)$this->getValue( $var ) ) === '' )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}

		return $this;
	}

	/**
	 * @param mixed       $var
	 * @param null|string $message
	 *
	 * @return $this
	 */
	public function isString( $var, $message = null )
	{
		if ( !String::isValid( $this->getValue( $var ) ) )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}

		return $this;
	}

	/**
	 * @param mixed       $var
	 * @param null|string $message
	 *
	 * @return $this
	 */
	public function isArray( $var, $message = null )
	{
		if ( !is_array( $this->getValue( $var ) ) )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}

		return $this;
	}

	/**
	 * @param mixed       $var
	 * @param null|string $message
	 *
	 * @return $this
	 */
	public function isInt( $var, $message = null )
	{
		$value = $this->getValue( $var );
		if ( !is_numeric( $value ) || intval( $value ) != strval( $value ) )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}

		return $this;
	}

	/**
	 * @param mixed       $var
	 * @param null|string $message
	 *
	 * @return $this
	 */
	public function positiveInt( $var, $message = null )
	{
		if ( !$this->isInt( $var )->getBoolResult() || $this->getValue( $var ) <= 0 )
		{
			$this->bool_result = false;
			$this->addMessageIfSet( $message );
		}

		return $this;
	}
}