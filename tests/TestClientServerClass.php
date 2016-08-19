<?php

/**
 * ClientServer class test class.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/08/2016
 */

//
// Test class.
//
class test_ClientServer extends \Milko\wrapper\ClientServer
{
	//
	// Implement Clients() method.
	//
	public function Clients()
	{
		return [ "Client1", "Client2", "Client3" ];
	}

	//
	// Implement connection method.
	//
	protected function connectionCreate()
	{
		return "ClientServer is connected";
	}

	//
	// Implement disconnection method.
	//
	protected function connectionDestruct()
	{
		return "ClientServer is not connected";
	}

	//
	// Implement client instantiation metod.
	//
	protected function clientCreate()
	{
		return new test_Client( NULL, $this );
	}

	//
	// Implement client destruction metod.
	//
	protected function clientDestruct( \Milko\wrapper\Client $theClient )
	{
		// Do nothing;
	}
}


?>

