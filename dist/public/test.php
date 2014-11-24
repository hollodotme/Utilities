<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\Utilities;

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/../ArrayConverter.php';

$arr = [
	'unit',
	'test'  => 'test',
	'unit'  => [ 1 => 'unit', 2 => 'test' ],
	'empty' => [ ]
];

$converter = new ArrayConverter( $arr );

$std = $converter->toStdClass();

echo '<pre>', htmlspecialchars( print_r( $std, 1 ) ), '</pre>';