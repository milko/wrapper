<?php

/**
 * Url trait test class.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/08/2016
 */

/**
 * Require trait.
 */
require_once( dirname(__DIR__) . "/src/traits/Url.php" );

//
// Test class.
//
class test_Url
{
	//
	// Trait.
	//
	use Url;

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

