<?php

/**
 * Url trait test class.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/08/2016
 */

//
// Test class.
//
class test_Url
{
	//
	// Trait.
	//
	use \Milko\wrapper\Url;

	/**
	 * Component tags.
	 */
	const kTAG_PROT = "PROT";
	const kTAG_HOST = "HOST";
	const kTAG_PORT = "PORT";
	const kTAG_USER = "USER";
	const kTAG_PASS = "PASS";
	const kTAG_PATH = "PATH";
	const kTAG_OPTS = "OPTS";
	const kTAG_FRAG = "FRAG";

	//
	// Connection string.
	//
	public $url = NULL;

	//
	// Declare constructor.
	//
	public function __construct( $connection = NULL ) {
		if( $connection !== NULL )
			$this->url = $this->URL( $connection );
	}
}


?>

