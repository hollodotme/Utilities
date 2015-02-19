<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities\Test\Unit\Fixures\ValueObjects;

/**
 * Class ObjectWithToStringMethod
 *
 * @package hollodotme\Utilities\Test\Unit\Fixures\ValueObjects
 */
class ObjectWithToStringMethod
{

	private $string;

	public function __construct( $string )
	{
		$this->string = $string;
	}

	public function __toString()
	{
		return $this->string;
	}
}
