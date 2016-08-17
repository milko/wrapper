<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Server;

//
// Test class.
//
class test_Server extends Server
{
	//
	// Declare connection method.
	//
	protected function connectionCreate()
	{
		return "Is connected";
	}

	//
	// Declare disconnection method.
	//
	protected function connectionDestruct()
	{
		return "Is not connected";
	}
}

// Instantiate from URL.
$test = new test_Server( 'user:pass@/database?arg=val#frag' );
print_r( $test );

?>