<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities;

/**
 * Class ArrayConverter
 *
 * @package hollodotme\Utilities
 */
class ArrayConverter
{

	/** @var array */
	private $array;

	/**
	 * @param array $array
	 */
	public function __construct( array $array )
	{
		$this->array = $array;
	}

	/**
	 * @return \stdClass
	 */
	public function toStdClass()
	{
		if ( empty($this->array) )
		{
			return new \stdClass();
		}
		else
		{
			$array    = $this->array;
			$array    = $this->addEmptyKeyToArray( $array );
			$stdclass = json_decode( json_encode( $array ) );
			$this->unsetEmptyKeyFromStdClass( $stdclass );

			return $stdclass;
		}
	}

	/**
	 * @param array $array
	 *
	 * @return array
	 */
	private function addEmptyKeyToArray( array $array )
	{
		$array[''] = '';
		foreach ( $array as &$value )
		{
			if ( is_array( $value ) )
			{
				$value = $this->addEmptyKeyToArray( $value );
			}
		}

		return $array;
	}

	/**
	 * @param \stdClass $stdclass
	 */
	private function unsetEmptyKeyFromStdClass( \stdClass $stdclass )
	{
		unset($stdclass->_empty_);

		foreach ( $stdclass as &$value )
		{
			if ( $value instanceof \stdClass )
			{
				$this->unsetEmptyKeyFromStdClass( $value );
			}
		}
	}
}