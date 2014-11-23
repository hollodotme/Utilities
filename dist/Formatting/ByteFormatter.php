<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities\Formatting;

/**
 * Class ByteFormatter
 *
 * @package hollodotme\Utilities\Formatting
 */
class ByteFormatter
{

	/** @var int */
	private $bytes;

	/**
	 * @param int $byte
	 */
	public function __construct( $byte )
	{
		$this->bytes = intval( $byte );
	}

	/**
	 * @param int $precision
	 *
	 * @return string
	 */
	public function format( $precision = 2 )
	{
		return $this->getHumanReadableFormat( $precision );
	}

	/**
	 * @param int $precision
	 *
	 * @return string
	 */
	private function getHumanReadableFormat( $precision )
	{
		$units = array( 'Byte', 'KB', 'MB', 'GB', 'TB' );
		$bytes = max( $this->bytes, 0 );
		$pow   = floor( ($bytes ? log( $bytes ) : 0) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );

		# Uncomment one of the following alternatives
		$bytes /= pow( 1024, $pow );

		# $bytes /= (1 << (10 * $pow));

		$precision = intval( $precision );
		$bytes     = round( $bytes, intval( $precision ) );

		return number_format( $bytes, $precision, ',', '.' ) . ' ' . $units[ $pow ];
	}
}
