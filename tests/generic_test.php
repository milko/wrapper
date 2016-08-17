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

$x = "/dir/file/frag";
print_r( explode( '/', $x ) );
exit;

// Instantiate from URL.
$test = new test_Server( 'prot://user:pass@host/database?@user@=val&@password@=password#frag' );
print_r( $test );

?>