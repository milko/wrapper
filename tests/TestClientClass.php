<?php

/**
 * Client class test class.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/08/2016
 */

//
// Test class.
//
class test_Client extends \Milko\wrapper\Client
{
	//
	// Implement Clients() method.
	//
	public function Clients()
	{
		return [ "SubClient1", "SubClient2", "SubClient3" ];
	}

	//
	// Implement connection method.
	//
	protected function connectionCreate()
	{
		return "Client is connected";
	}

	//
	// Implement disconnection method.
	//
	protected function connectionDestruct()
	{
		return "Client is not connected";
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

	//
	// Implement server instantiation metod.
	//
	protected function serverCreate()
	{
		return new test_ClientServer(
			$this->URL(
				NULL,
				[
					self::kTAG_USER,
					self::kTAG_PATH,
					self::kTAG_OPTS,
					self::kTAG_FRAG
				]
			)
		);
	}
}


?>

