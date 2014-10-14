<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities\Test\Unit;

use hollodotme\Utilities\AnnotatedDTO;

/**
 * Class TestDTORead
 *
 * @property-read string $test_member Comment
 *
 * @package hollodotme\Utilities\Test\Unit
 */
class TestDTORead extends AnnotatedDTO
{
	public function __construct( $test_value )
	{
		$this->data['test_member'] = $test_value;
	}
}
