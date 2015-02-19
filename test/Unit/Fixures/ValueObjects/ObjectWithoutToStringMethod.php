<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities\Test\Unit\Fixures\ValueObjects;

/**
 * Class ObjectWithoutToStringMethod
 *
 * @package hollodotme\Utilities\Test\Unit\Fixures\ValueObjects
 */
class ObjectWithoutToStringMethod
{

	private $string;

	public function __construct( $string )
	{
		$this->string = $string;
	}
}
