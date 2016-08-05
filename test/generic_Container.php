<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Container;

// Example class.
class Test extends Container {
	public function Property( 		 $off,
							  string $mask = NULL,
							  bool	 $value = NULL,
							  bool	 $old = FALSE ) {
		return $this->manageBitfieldProperty(
			$off, $mask, $value, $old
		);
	}
}

// Example structure.
$object = new Test();

// Set initial value.
$state = $object->Property( "flag", hex2bin( '00000000' ), TRUE );
// $state == 0x00000000;
// $object["flag"] == 0x00000000;

// Turn on masked bits in attribute and return current value.
$state = $object->Property( "flag", hex2bin( 'ff0000ff' ), TRUE );
// $state == 0xff0000ff;
// $object["flag"] == 0xff0000ff;

// Turn on masked bits in non existing property and return current value.
$state = $object->Property( "UNKNOWN", hex2bin( 'ff0000ff' ), TRUE );
//var_dump($state);
var_dump('0x'.bin2hex($state));
var_dump('0x'.bin2hex($object["flag"]));
exit;
// $state == 0xff0000ff;
// $object["flag"] == 0xff0000ff;

// Turn on masked bits in attribute and return old value.
$state = $object->Property( "flag", hex2bin( '000ff000' ), TRUE, TRUE );
// $state == 0xff0000ff;
// $object["flag"] == 0xff0ff0ff;

// Match attribute with mask.
$state = $object->Property( "flag", hex2bin( '0000000f' ) );
// $state == bool(true);

// Match attribute with mask.
$state = $object->Property( "flag", hex2bin( '00f00f00' ) );
// $state == bool(false);

// Return state of property.
$state = $object->Property( "flag" );
// $state == 0xff0ff0ff;

// Return state of non existing property.
$state = $object->Property( "UNKNOWN" );
// $state === NULL;

// Turn off masked bits in attribute and return old value.
$state = $object->Property( "flag", hex2bin( '00ffff00' ), FALSE, TRUE );
// $state == 0xff0ff0ff;
// $object["flag"] == 0xff0000ff;

//
// Note how I use hex functions: passing hex values will convert them to integers:
// Depending on the machine byte order you will not be able to use the integer sign
// bit.
//

?>