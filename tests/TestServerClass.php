<?php

/**
 * Server class test class.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/08/2016
 */

//
// Test class.
//
class test_Server extends \Milko\wrapper\Server
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


?>

