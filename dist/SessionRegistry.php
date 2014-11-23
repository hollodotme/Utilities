<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities;

/**
 * Class SessionRegistry
 *
 * @package hollodotme\Utilities
 */
abstract class SessionRegistry
{

	/** @var array */
	private $data;

	/**
	 * @param array $session
	 */
	public function __construct( array &$session )
	{
		$this->data = &$session;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	final protected function setSessionValue( $key, $value )
	{
		$this->data[ $key ] = $value;
	}

	/**
	 * @param string $key
	 *
	 * @return null|mixed
	 */
	final protected function getSessionValue( $key )
	{
		if ( $this->isSessionKeySet( $key ) )
		{
			return $this->data[ $key ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	final protected function isSessionKeySet( $key )
	{
		return isset($this->data[ $key ]);
	}

	/**
	 * @param string $key
	 */
	final protected function unsetSessionValue( $key )
	{
		if ( $this->isSessionKeySet( $key ) )
		{
			unset($this->data[ $key ]);
		}
	}
}