<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Container;


// Empty container.
$object = new Container();
// Milko\wrapper\Container Object
// (
// 	[mProperties:protected] => Array
// 		(
// 		)
// )

// With array.
$object = new Container( [1,2,3] );
// Milko\wrapper\Container Object
// (
// 	[mProperties:protected] => Array
// 		(
//			[0] => 1
//			[1] => 2
//			[2] => 3
// 		)
// )

// With Container.
$object = new Container( new Container( [1,2,3] ) );
// Milko\wrapper\Container Object
// (
// 	[mProperties:protected] => Array
// 		(
//			[0] => 1
//			[1] => 2
//			[2] => 3
// 		)
// )

// With ArrayObject.
$object = new Container( new ArrayObject( [1,2,3] ) );
// Milko\wrapper\Container Object
// (
// 	[mProperties:protected] => Array
// 		(
//			[0] => 1
//			[1] => 2
//			[2] => 3
// 		)
// )

// With ArrayObject converted to array.
$object = new Container([ 1 => ]);
print_r($object);

exit;



// Example class.
class Test extends Container {
	private $status = NULL;
	private $attribute = NULL;
	public function __construct( $props = NULL, bool $array = FALSE ) {
		// Construct parent.
		parent::__construct( $props, $array );
		// Initialise status attribute.
		$this->status = hex2bin( '00000000' );
	}
	public function Status( string $mask = NULL,
							   bool $value = NULL,
							   bool $old = FALSE ) {
		return $this->manageBitfieldAttribute( $this->status, $mask, $value, $old );
	}
	public function Attribute( $val = NULL, $old = FALSE ) {
		return $this->manageAttribute( $this->attribute, $val, $old );
	}
	public function Property( $off = NULL, $val = NULL, bool $old = FALSE ) {
		return $this->manageProperty( $off, $val, $old );
	}
}

// Example structure.
$object = new Test();

?>