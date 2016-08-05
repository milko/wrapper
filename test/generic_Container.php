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