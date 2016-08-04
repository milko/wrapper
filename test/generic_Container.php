<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Container;

// Example structure.
$object = new Container( [
	"offset" => "value",
	"list" => [ 1, 2 ],
	"nested" => [ 1 => [ 2 => new ArrayObject( [ 3 => "three" ] ) ] ]
] );

// Will delete the "offset" property.
$object->offsetUnset( "offset" );

// Will not raise an alert.
$object->offsetUnset( "UNKNOWN" );

// Will delete the $object[ "list" ][ 2 ] property.
$object->offsetUnset( [ "list", 0 ] );

// Will delete the $object[ "nested" ][ 1 ][ 2 ][ 3 ] property
// and all properties including "nested", since they would be empty.
$object->offsetUnset( [ "nested", 1, 2, 3 ] );

?>